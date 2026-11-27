<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(){
        $this->middleware('user.status');
        $this->middleware('user.permissions');
    }

    public function getHome()
    {
        $today = Carbon::today();
        $lastSevenDays = Carbon::today()->subDays(6);

        // Ventas completadas del día
        $salesToday = DB::table('sales')
            ->whereDate('sold_at', $today)
            ->where('status', 'completed');

        $totalSalesToday = (clone $salesToday)->sum('total');
        $numberSalesToday = (clone $salesToday)->count();

        $averageTicket = $numberSalesToday > 0
            ? $totalSalesToday / $numberSalesToday
            : 0;

        // Inventario total en kilogramos
        $totalInventory = DB::table('meat_inventories')
            ->sum('quantity_kg');

        // Ventas completadas de los últimos 7 días
        $salesByDay = DB::table('sales')
            ->selectRaw('DATE(sold_at) as date, SUM(total) as total')
            ->where('status', 'completed')
            ->whereDate('sold_at', '>=', $lastSevenDays)
            ->whereDate('sold_at', '<=', $today)
            ->groupByRaw('DATE(sold_at)')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $salesChart = collect();

        for ($i = 0; $i < 7; $i++) {
            $date = $lastSevenDays->copy()->addDays($i);
            $key = $date->format('Y-m-d');

            $salesChart->push([
                'date' => $date->format('d/m'),
                'total' => (float) ($salesByDay[$key]->total ?? 0),
            ]);
        }

        // Últimas ventas registradas
        $recentSales = DB::table('sales')
            ->leftJoin('users as cashier', 'sales.user_id', '=', 'cashier.id')
            ->leftJoin('users as customer', 'sales.customer_id', '=', 'customer.id')
            ->select(
                'sales.id',
                'sales.folio',
                'sales.total',
                'sales.payment_method',
                'sales.status',
                'sales.sold_at',
                'cashier.name as cashier_name',
                'customer.name as customer_name'
            )
            ->orderByDesc('sales.sold_at')
            ->limit(8)
            ->get();

        // Sesiones de caja actualmente abiertas
        $openSessions = DB::table('cash_register_sessions')
            ->join('users', 'cash_register_sessions.user_id', '=', 'users.id')
            ->join('accounts', 'cash_register_sessions.account_id', '=', 'accounts.id')
            ->where('cash_register_sessions.status', 'open')
            ->select(
                'cash_register_sessions.id',
                'cash_register_sessions.account_id',
                'cash_register_sessions.opening_amount',
                'cash_register_sessions.opened_at',
                'users.name as user_name',
                'accounts.name as account_name',
                'accounts.account_number'
            )
            ->orderByDesc('cash_register_sessions.opened_at')
            ->get();

        $openSessionsCount = $openSessions->count();


        return view('dashboard', compact(
            'totalSalesToday',
            'numberSalesToday',
            'averageTicket',
            'totalInventory',
            'salesChart',
            'recentSales',
            'openSessions',
            'openSessionsCount'
        ));
    }
}
