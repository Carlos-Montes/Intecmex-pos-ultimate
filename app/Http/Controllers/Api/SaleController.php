<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashRegisterSession;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'min:0'],

            'manual_discount_type' => [
                'required',
                'in:amount,percent'
            ],

            'manual_discount_value' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'tax_percentage' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ],

            'cash_received' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'items' => ['required', 'array', 'min:1'],

            'items.*.product_id' => [
                'required',
                'integer',
                'distinct',
                'exists:products,id'
            ],

            'items.*.quantity_kg' => [
                'required',
                'numeric',
                'gt:0'
            ],
        ]);

        $sale = DB::transaction(function () use ($validated) {

            // ======================================
            // 1. VALIDAR CAJA ABIERTA DEL USUARIO
            // ======================================

            $session = CashRegisterSession::where(
                'user_id',
                auth()->id()
            )
                ->where('status', 'open')
                ->lockForUpdate()
                ->first();

            if (!$session) {
                throw ValidationException::withMessages([
                    'cash_register' => [
                        'No tienes una caja abierta para realizar ventas.'
                    ]
                ]);
            }

            // ======================================
            // 2. VALIDAR CLIENTE
            // ======================================

            $customer = null;

            $customerId = $validated['customer_id'] ?? null;

            if ($customerId && (int) $customerId !== 0) {

                $customer = User::where('id', $customerId)
                    ->where('role', 2)
                    ->first();

                if (!$customer) {
                    throw ValidationException::withMessages([
                        'customer_id' => [
                            'El cliente seleccionado no es válido.'
                        ]
                    ]);
                }
            }

            // ======================================
            // 3. CONSULTAR Y BLOQUEAR PRODUCTOS
            // ======================================

            $productIds = collect($validated['items'])
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values();

            $products = Product::whereIn('id', $productIds)
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== $productIds->count()) {
                throw ValidationException::withMessages([
                    'items' => [
                        'Uno o más productos ya no están disponibles.'
                    ]
                ]);
            }

            // ======================================
            // 4. CALCULAR SUBTOTAL Y VALIDAR STOCK
            // ======================================

            $subtotal = 0;
            $details = [];

            foreach ($validated['items'] as $item) {

                $productId = (int) $item['product_id'];

                $product = $products->get($productId);

                $quantity = round((float) $item['quantity_kg'], 3);

                $price = (float) $product->price;

                if ($price <= 0) {
                    throw ValidationException::withMessages([
                        'items' => [
                            "El producto {$product->name} no tiene un precio válido."
                        ]
                    ]);
                }

                // Bloquear inventario
                $inventory = DB::table('meat_inventories')
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (!$inventory) {
                    throw ValidationException::withMessages([
                        'items' => [
                            "El producto {$product->name} no tiene registro de inventario."
                        ]
                    ]);
                }

                $available = (float) $inventory->quantity_kg;

                if ($quantity > $available) {
                    throw ValidationException::withMessages([
                        'items' => [
                            "Inventario insuficiente para {$product->name}. Disponible: "
                            . number_format($available, 2) . ' kg.'
                        ]
                    ]);
                }

                $lineSubtotal = round($quantity * $price, 2);

                $subtotal += $lineSubtotal;

                $details[] = [
                    'product' => $product,
                    'inventory_id' => $inventory->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $subtotal = round($subtotal, 2);

            // ======================================
            // 5. DESCUENTO AUTOMÁTICO DEL CLIENTE
            // ======================================

            // indiscount representa un importe fijo en pesos.
            $customerDiscountValue = $customer
                ? max(0, (float) $customer->indiscount)
                : 0;

            $customerDiscount = min(
                $customerDiscountValue,
                $subtotal
            );

            // ======================================
            // 6. DESCUENTO MANUAL
            // ======================================

            $manualType = $validated['manual_discount_type'];

            $manualValue = max(
                0,
                (float) ($validated['manual_discount_value'] ?? 0)
            );

            $remaining = max(
                0,
                $subtotal - $customerDiscount
            );

            if ($manualType === 'percent') {

                $manualValue = min($manualValue, 100);

                $manualDiscount = round(
                    $remaining * $manualValue / 100,
                    2
                );

            } else {

                $manualDiscount = min(
                    $manualValue,
                    $remaining
                );
            }

            $discountTotal = round(
                $customerDiscount + $manualDiscount,
                2
            );

            // ======================================
            // 7. CALCULAR IVA Y TOTAL
            // ======================================

            $taxPercentage = (float) (
                $validated['tax_percentage'] ?? 0
            );

            $taxableAmount = max(
                0,
                $subtotal - $discountTotal
            );

            $taxAmount = round(
                $taxableAmount * $taxPercentage / 100,
                2
            );

            $total = round(
                $taxableAmount + $taxAmount,
                2
            );

            // ======================================
            // 8. VALIDAR PAGO EN EFECTIVO
            // ======================================

            $cashReceived = round(
                (float) ($validated['cash_received'] ?? $total),
                2
            );

            if ($cashReceived < $total) {
                throw ValidationException::withMessages([
                    'cash_received' => [
                        'El efectivo recibido es insuficiente.'
                    ]
                ]);
            }

            $change = round(
                $cashReceived - $total,
                2
            );

            // ======================================
            // 9. CREAR VENTA
            // ======================================

            $sale = Sale::create([
                'folio' => 'VTA-' . now()->format('YmdHis')
                    . '-' . Str::upper(Str::random(5)),

                'user_id' => auth()->id(),

                'customer_id' => $customer?->id,

                'cash_register_session_id' => $session->id,

                'subtotal' => $subtotal,

                'customer_discount_amount' => $customerDiscount,

                'manual_discount_type' => $manualType,

                'manual_discount_value' => $manualValue,

                'manual_discount_amount' => $manualDiscount,

                'discount_amount' => $discountTotal,

                'tax_amount' => $taxAmount,

                'total' => $total,

                'payment_method' => 'cash',

                'cash_received' => $cashReceived,

                'change_amount' => $change,

                'status' => 'completed',

                'sold_at' => now(),
            ]);

            // ======================================
            // 10. GUARDAR DETALLES Y DESCONTAR STOCK
            // ======================================

            foreach ($details as $detail) {

                $sale->details()->create([
                    'product_id' => $detail['product']->id,

                    'product_code' => $detail['product']->code,

                    'product_name' => $detail['product']->name,

                    'unit_price' => $detail['price'],

                    'quantity_kg' => $detail['quantity'],

                    'subtotal' => $detail['subtotal'],
                ]);

                DB::table('meat_inventories')
                    ->where('id', $detail['inventory_id'])
                    ->decrement(
                        'quantity_kg',
                        $detail['quantity']
                    );
            }

            return $sale;
        }, 3);

        // ======================================
        // 11. RESPUESTA
        // ======================================

        $sale->load([
            'details',
            'customer',
            'user',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Venta registrada correctamente.',
            'sale' => $sale,
        ], 201);
    }
}
