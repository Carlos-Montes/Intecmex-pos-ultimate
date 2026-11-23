<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CashRegisterSession;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashRegisterController extends Controller
{
    /**
     * Denominaciones expresadas en centavos.
     */
    private const DENOMINATIONS = [
        'bill_1000' => [
            'label' => 'Billetes de $1,000',
            'value' => 100000,
        ],
        'bill_500' => [
            'label' => 'Billetes de $500',
            'value' => 50000,
        ],
        'bill_200' => [
            'label' => 'Billetes de $200',
            'value' => 20000,
        ],
        'bill_100' => [
            'label' => 'Billetes de $100',
            'value' => 10000,
        ],
        'bill_50' => [
            'label' => 'Billetes de $50',
            'value' => 5000,
        ],
        'bill_20' => [
            'label' => 'Billetes de $20',
            'value' => 2000,
        ],
        'coin_20' => [
            'label' => 'Monedas de $20',
            'value' => 2000,
        ],
        'coin_10' => [
            'label' => 'Monedas de $10',
            'value' => 1000,
        ],
        'coin_5' => [
            'label' => 'Monedas de $5',
            'value' => 500,
        ],
        'coin_2' => [
            'label' => 'Monedas de $2',
            'value' => 200,
        ],
        'coin_1' => [
            'label' => 'Monedas de $1',
            'value' => 100,
        ],
        'coin_50c' => [
            'label' => 'Monedas de 50 centavos',
            'value' => 50,
        ],
    ];

    /**
     * Consultar el estado de caja del usuario autenticado.
     */
    public function getStatus()
    {
        $session = CashRegisterSession::with(['account', 'user'])
            ->where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        return response()->json([
            'success' => true,
            'opened' => $session !== null,
            'session' => $session,
            'accounts' => $session
                ? []
                : Account::where('status', 1)
                    ->orderBy('name')
                    ->get([
                        'id',
                        'account_number',
                        'name',
                    ]),
        ]);
    }

    /**
     * Abrir una nueva sesión de caja.
     */
    public function postOpen(Request $request)
    {
        $validated = $request->validate([
            'account_id' => [
                'required',
                'integer',
                'exists:accounts,id',
            ],
            'opening_amount' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],
        ]);

        $session = DB::transaction(function () use ($validated) {

            /*
             * Bloqueamos el usuario para evitar que dos solicitudes
             * abran simultáneamente dos cajas para el mismo usuario.
             */
            DB::table('users')
                ->where('id', Auth::id())
                ->lockForUpdate()
                ->first();

            $existingSession = CashRegisterSession::where(
                'user_id',
                Auth::id()
            )
                ->where('status', 'open')
                ->first();

            if ($existingSession) {
                throw ValidationException::withMessages([
                    'account_id' => 'Ya tienes una sesión de caja abierta.',
                ]);
            }

            $account = Account::where('id', $validated['account_id'])
                ->where('status', 1)
                ->lockForUpdate()
                ->first();

            if (! $account) {
                throw ValidationException::withMessages([
                    'account_id' => 'La cuenta seleccionada no está activa.',
                ]);
            }

            return CashRegisterSession::create([
                'user_id' => Auth::id(),
                'account_id' => $account->id,
                'opening_amount' => round(
                    (float) $validated['opening_amount'],
                    2
                ),
                'status' => 'open',
                'opened_at' => now(),
            ]);
        }, 3);

        $session->load(['account', 'user']);

        return response()->json([
            'success' => true,
            'type' => 'success',
            'title' => config('intecmex.app_name'),
            'message' => 'La caja se abrió correctamente.',
            'msg' => 'La caja se abrió correctamente.',
            'session' => $session,
        ]);
    }

    /**
     * Consultar resumen financiero de la caja abierta.
     */
    public function getSummary()
    {
        $session = CashRegisterSession::with(['account', 'user'])
            ->where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes una caja abierta.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'summary' => $this->buildSummary($session),
        ]);
    }

    /**
     * Cerrar caja y registrar el arqueo de efectivo.
     */
    public function postClose(Request $request)
    {
        $rules = [
            'cash_count' => [
                'required',
                'array',
            ],
        ];

        /*
         * Validar cada denominación.
         */
        foreach (self::DENOMINATIONS as $key => $denomination) {
            $rules["cash_count.{$key}"] = [
                'nullable',
                'integer',
                'min:0',
                'max:1000000',
            ];
        }

        $validated = $request->validate($rules);

        $result = DB::transaction(function () use ($validated) {

            /*
             * Bloqueamos la misma sesión que bloquea SaleController
             * al registrar una venta.
             *
             * Así el cierre espera a que termine una venta en proceso,
             * o impide que se registre una nueva cuando ya está cerrada.
             */
            $session = CashRegisterSession::with(['account', 'user'])
                ->where('user_id', Auth::id())
                ->where('status', 'open')
                ->lockForUpdate()
                ->first();

            if (! $session) {
                throw ValidationException::withMessages([
                    'cash_register' => 'No tienes una caja abierta para cerrar.',
                ]);
            }

            /*
             * Recalculamos el resumen dentro de la transacción.
             * Nunca usamos los importes que envía el navegador.
             */
            $summary = $this->buildSummary($session);

            /*
             * Calcular arqueo en centavos para evitar errores
             * de precisión decimal.
             */
            $countedCents = 0;
            $cashCount = [];

            foreach (self::DENOMINATIONS as $key => $denomination) {

                $quantity = (int) (
                    $validated['cash_count'][$key] ?? 0
                );

                $subtotalCents = $quantity * $denomination['value'];

                $countedCents += $subtotalCents;

                $cashCount[$key] = [
                    'label' => $denomination['label'],
                    'quantity' => $quantity,
                    'unit_value' => round(
                        $denomination['value'] / 100,
                        2
                    ),
                    'subtotal' => round(
                        $subtotalCents / 100,
                        2
                    ),
                ];
            }

            $closingAmount = round($countedCents / 100, 2);

            $expectedAmount = (float) $summary['expected_amount'];

            /*
             * Positivo = sobrante.
             * Negativo = faltante.
             */
            $difference = round(
                $closingAmount - $expectedAmount,
                2
            );

            /*
             * Guardar el arqueo y cerrar la sesión.
             */
            $session->closing_amount = $closingAmount;
            $session->expected_amount = $expectedAmount;
            $session->difference = $difference;

            $session->cash_count = [
                'denominations' => $cashCount,
                'total' => $closingAmount,
            ];

            $session->status = 'closed';
            $session->closed_at = now();

            $session->save();

            return [
                'session' => $session->fresh([
                    'account',
                    'user',
                ]),
                'summary' => [
                    ...$summary,
                    'closing_amount' => $closingAmount,
                    'difference' => $difference,
                    'difference_type' => $difference > 0
                        ? 'surplus'
                        : ($difference < 0 ? 'shortage' : 'exact'),
                    'cash_count' => $cashCount,
                    'closed_at' => $session->closed_at,
                    'status' => 'closed',
                ],
            ];
        }, 3);

        return response()->json([
            'success' => true,
            'type' => 'success',
            'message' => 'El corte de caja se realizó correctamente.',
            'session' => $result['session'],
            'summary' => $result['summary'],
        ]);
    }

    /**
     * Construir resumen financiero de una sesión.
     *
     * El efectivo esperado se calcula con:
     *
     * Fondo inicial + ventas completadas en efectivo.
     *
     * Se suma sales.total, NO cash_received,
     * porque el cambio ya fue descontado.
     */
    private function buildSummary(CashRegisterSession $session): array
    {
        $salesQuery = Sale::where(
            'cash_register_session_id',
            $session->id
        )
            ->where('status', 'completed')
            ->where('payment_method', 'cash');

        $salesTotal = (float) (clone $salesQuery)->sum('total');

        $salesCount = (clone $salesQuery)->count();

        $openingAmount = (float) $session->opening_amount;

        /*
         * Por ahora no tenemos movimientos de entrada o salida
         * de efectivo. Se mantienen en cero hasta implementar
         * ese módulo.
         */
        $cashEntries = 0;
        $cashWithdrawals = 0;

        $expectedAmount = round(
            $openingAmount
            + $salesTotal
            + $cashEntries
            - $cashWithdrawals,
            2
        );

        return [
            'session_id' => $session->id,

            'account' => $session->account,
            'user' => $session->user,

            'opened_at' => $session->opened_at,

            'opening_amount' => round($openingAmount, 2),

            'sales_count' => $salesCount,

            'cash_sales' => round($salesTotal, 2),

            'cash_entries' => $cashEntries,

            'cash_withdrawals' => $cashWithdrawals,

            'expected_amount' => $expectedAmount,
        ];
    }
}
