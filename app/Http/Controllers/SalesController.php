<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SalesController extends Controller
{

    public function __construct(){
        $this->middleware('user.status');
        $this->middleware('user.permissions');
    }

    public function getSales(Request $request)
    {
        $query = Sale::with([
            'customer',
            'user',
            'details',
        ]);

        // Buscar por folio
        if ($request->filled('folio')) {
            $query->where('folio', 'like', '%'.$request->folio.'%');
        }

        // Buscar por cliente
        if ($request->filled('customer')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->customer.'%');
            });
        }

        // Buscar por cajero
        if ($request->filled('user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->user.'%');
            });
        }

        // Filtrar por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Fecha inicial
        if ($request->filled('date_from')) {
            $query->whereDate('sold_at', '>=', $request->date_from);
        }

        // Fecha final
        if ($request->filled('date_to')) {
            $query->whereDate('sold_at', '<=', $request->date_to);
        }

        // Por defecto: ventas del día actual
        if (! $request->filled('date_from') && ! $request->filled('date_to')) {
            $query->whereDate('sold_at', today());
        }

        // Total acumulado de las ventas filtradas
        $totalSales = (clone $query)->sum('total');

        // Cantidad de ventas encontradas
        $salesCount = (clone $query)->count();

        // Resultados paginados
        $sales = $query
            ->orderByDesc('sold_at')
            ->paginate(15)
            ->withQueryString();

        return view('sales.home', [
            'sales' => $sales,
            'totalSales' => $totalSales,
            'salesCount' => $salesCount,
            'filters' => $request->only([
                'folio',
                'customer',
                'user',
                'status',
                'date_from',
                'date_to',
            ]),
        ]);
    }

    public function getSaleDetail($id)
    {
        $sale = Sale::with([
            'customer',
            'user',
            'details',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,

            'sale' => [
                'id' => $sale->id,
                'folio' => $sale->folio,
                'sold_at' => $sale->sold_at?->toIso8601String(),

                'user' => [
                    'name' => $sale->user?->name ?? 'Sin cajero',
                ],

                'customer' => [
                    'name' => $sale->customer?->name ?? 'Público General',
                ],

                'cashier' => $sale->user?->name ?? 'Sin cajero',

                'payment_method' => $sale->payment_method,
                'status' => $sale->status,

                'subtotal' => $sale->subtotal,
                'discount_amount' => $sale->discount_amount,
                'customer_discount_amount' => $sale->customer_discount_amount,
                'manual_discount_amount' => $sale->manual_discount_amount,
                'tax_amount' => $sale->tax_amount,
                'total' => $sale->total,

                'cash_received' => $sale->cash_received,
                'change_amount' => $sale->change_amount,

                'details' => $sale->details->map(function ($detail) {
                    return [
                        'product_code' => $detail->product_code,
                        'product_name' => $detail->product_name,
                        'unit_price' => $detail->unit_price,
                        'quantity_kg' => $detail->quantity_kg,
                        'subtotal' => $detail->subtotal,
                    ];
                })->values(),
            ],
        ]);
    }
}
