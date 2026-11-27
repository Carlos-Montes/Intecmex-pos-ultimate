@extends('master')

@section('custom_css')
    <link rel="stylesheet" href="{{ url('/static/css/dashboard.css?v=' . time()) }}">
@endsection

@section('custom_js')
    <script src="{{ url('/static/js/dashboard.js?v=' . time()) }}" defer></script>
@endsection

@section('content')
        <div class="dashboard-page">

            {{-- Encabezado --}}
            <div class="dashboard-heading">
                <div>
                    <span class="dashboard-eyebrow">INTECMEX POS / ADMINISTRACIÓN</span>
                    <h2>Bienvenido: {{ Auth::user()->name }}  /  Panel de control</h2>
                    <p>Resumen general de la operación de tu negocio.</p>
                </div>

                <div class="dashboard-heading-actions">
                    <span class="preview-badge">
                        <span></span> Sistema activo
                    </span>

                    <div class="dashboard-date">
                        <i class="bi bi-calendar3"></i>
                        <span id="dashboard-current-date">Panel administrativo</span>
                    </div>
                </div>
            </div>
    @if (getPermissions(Auth::user()->permissions, 'all'))
            {{-- Accesos rápidos --}}
            <div class="dashboard-quick-actions">

                <a href="{{ url('/products/all') }}" class="dashboard-action">
                    <span class="action-icon action-blue">
                        <i class="bi bi-box-seam"></i>
                    </span>
                    <span>
                        <strong>Productos</strong>
                        <small>Consultar catálogo</small>
                    </span>
                    <i class="bi bi-arrow-up-right action-arrow"></i>
                </a>

                <a href="{{ url('/product/add') }}" class="dashboard-action">
                    <span class="action-icon action-purple">
                        <i class="bi bi-plus-lg"></i>
                    </span>
                    <span>
                        <strong>Nuevo producto</strong>
                        <small>Ampliar catálogo</small>
                    </span>
                    <i class="bi bi-arrow-up-right action-arrow"></i>
                </a>

                <a href="{{ url('/products/1') }}" class="dashboard-action">
                    <span class="action-icon action-green">
                        <i class="bi bi-clipboard-data"></i>
                    </span>
                    <span>
                        <strong>Inventario</strong>
                        <small>Revisar existencias</small>
                    </span>
                    <i class="bi bi-arrow-up-right action-arrow"></i>
                </a>

            </div>

            {{-- Indicadores --}}
            <div class="dashboard-section-heading">
                <div>
                    <h4>Resumen operativo</h4>
                    <p>Actividad registrada durante el día de hoy.</p>
                </div>

                <span class="dashboard-period">
                    <i class="bi bi-calendar-day"></i> Hoy
                </span>
            </div>

            <div class="dashboard-metrics">

                {{-- Ventas --}}
                <article class="metric-card">
                    <div class="metric-top">
                        <span class="metric-icon metric-icon-blue">
                            <i class="bi bi-currency-dollar"></i>
                        </span>
                        <span class="metric-tag">Ingresos</span>
                    </div>

                    <p class="metric-label">Ventas de hoy</p>

                    <div class="metric-value">
                        {{ config('intecmex.currency') }}{{ number_format($totalSalesToday, 2) }}
                    </div>

                    <div class="metric-footer">
                        <i class="bi bi-graph-up"></i>
                        <span>Importe de ventas completadas</span>
                    </div>
                </article>

                {{-- Tickets --}}
                <article class="metric-card">
                    <div class="metric-top">
                        <span class="metric-icon metric-icon-green">
                            <i class="bi bi-receipt"></i>
                        </span>
                        <span class="metric-tag">Operaciones</span>
                    </div>

                    <p class="metric-label">Tickets de hoy</p>

                    <div class="metric-value">
                        {{ number_format($numberSalesToday) }}
                    </div>

                    <div class="metric-footer">
                        <i class="bi bi-check-circle"></i>
                        <span>Ventas completadas</span>
                    </div>
                </article>

                {{-- Ticket promedio --}}
                <article class="metric-card">
                    <div class="metric-top">
                        <span class="metric-icon metric-icon-orange">
                            <i class="bi bi-calculator"></i>
                        </span>
                        <span class="metric-tag">Promedio</span>
                    </div>

                    <p class="metric-label">Ticket promedio</p>

                    <div class="metric-value">
                        {{ config('intecmex.currency') }}{{ number_format($averageTicket, 2) }}
                    </div>

                    <div class="metric-footer">
                        <i class="bi bi-info-circle"></i>
                        <span>Promedio por venta completada</span>
                    </div>
                </article>

                {{-- Inventario --}}
                <article class="metric-card">
                    <div class="metric-top">
                        <span class="metric-icon metric-icon-red">
                            <i class="bi bi-speedometer2"></i>
                        </span>
                        <span class="metric-tag">Existencias</span>
                    </div>

                    <p class="metric-label">Inventario de carne</p>

                    <div class="metric-value">
                        {{ number_format($totalInventory, 2) }} <small>kg</small>
                    </div>

                    <div class="metric-footer">
                        <i class="bi bi-box-seam"></i>
                        <span>Kilos registrados en inventario</span>
                    </div>
                </article>

            </div>

            {{-- Gráfica y sesiones --}}
            <div class="dashboard-content-grid">

                {{-- Ventas últimos 7 días --}}
                <section class="dashboard-panel">
                    <div class="dashboard-panel-heading">
                        <div>
                            <span class="panel-kicker">RENDIMIENTO</span>
                            <h4>Ventas de los últimos 7 días</h4>
                            <p>Importe diario de ventas completadas.</p>
                        </div>

                        <span class="panel-heading-icon">
                            <i class="bi bi-bar-chart-line"></i>
                        </span>
                    </div>

                    @php
                        $maxSales = max(1, (float) $salesChart->max('total'));
                    @endphp

                    <div class="dashboard-sales-chart">
                        @foreach ($salesChart as $day)
                            @php
                                $percentage = ($day['total'] / $maxSales) * 100;
                            @endphp

                            <div class="sales-chart-item">
                                <div class="sales-chart-value">
                                    {{ config('intecmex.currency') }}{{ number_format($day['total'], 2) }}
                                </div>

                                <div class="sales-chart-track">
                                    <div class="sales-chart-bar"
                                        style="height: {{ $day['total'] > 0 ? max(4, $percentage) : 0 }}%;"></div>
                                </div>

                                <span class="sales-chart-label">{{ $day['date'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- Sesiones abiertas --}}
                <section class="dashboard-panel">
                    <div class="dashboard-panel-heading">
                        <div>
                            <span class="panel-kicker">CAJAS</span>
                            <h4>Sesiones abiertas</h4>
                            <p>Estado actual de las cuentas de caja.</p>
                        </div>

                        <span class="panel-heading-icon">
                            <i class="bi bi-wallet2"></i>
                        </span>
                    </div>

                    <div class="dashboard-session-count">
                        <strong>{{ $openSessionsCount }}</strong>
                        <span>sesiones abiertas</span>
                    </div>

                    <div class="dashboard-session-list">
                        @forelse($openSessions as $session)
                            <div class="dashboard-session-item">
                                <div class="session-account-icon">
                                    <i class="bi bi-safe2"></i>
                                </div>

                                <div class="session-account-info">
                                    <strong>{{ $session->account_name }}</strong>
                                    <small>
                                        Cuenta {{ $session->account_number }}
                                        · {{ $session->user_name }}
                                    </small>
                                    <small>
                                        Apertura:
                                        {{ \Carbon\Carbon::parse($session->opened_at)->format('d/m/Y H:i') }}
                                    </small>
                                </div>

                                <span class="session-open-status">Abierta</span>
                            </div>
                        @empty
                            <div class="dashboard-empty">
                                <i class="bi bi-wallet"></i>
                                <strong>No hay sesiones abiertas</strong>
                                <span>Actualmente no hay cajas en operación.</span>
                            </div>
                        @endforelse
                    </div>
                </section>

            </div>

            {{-- Últimas ventas --}}
            <section class="dashboard-panel dashboard-recent-sales">
                <div class="dashboard-panel-heading">
                    <div>
                        <span class="panel-kicker">MOVIMIENTOS</span>
                        <h4>Últimas ventas</h4>
                        <p>Los últimos movimientos registrados en el punto de venta.</p>
                    </div>

                    <span class="panel-heading-icon">
                        <i class="bi bi-receipt-cutoff"></i>
                    </span>
                </div>

                <div class="dashboard-table-wrapper">
                    <table class="dashboard-sales-table">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Cajero</th>
                                <th>Pago</th>
                                <th>Estado</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td>
                                        <strong>{{ $sale->folio }}</strong>
                                    </td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($sale->sold_at)->format('d/m/Y H:i') }}
                                    </td>

                                    <td>
                                        {{ $sale->customer_name ?? 'Público General' }}
                                    </td>

                                    <td>
                                        {{ $sale->cashier_name ?? 'Sin cajero' }}
                                    </td>

                                    <td>
                                        {{ ucfirst($sale->payment_method) }}
                                    </td>

                                    <td>
                                        <span class="sale-status sale-status-{{ $sale->status }}">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    </td>

                                    <td class="text-end">
                                        <strong>
                                            {{ config('intecmex.currency') }}{{ number_format($sale->total, 2) }}
                                        </strong>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="dashboard-empty">
                                            <i class="bi bi-receipt"></i>
                                            <strong>No hay ventas registradas</strong>
                                            <span>Cuando se realicen ventas, aparecerán aquí.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Pie --}}
            <div class="dashboard-footer">
                <div class="dashboard-footer-brand">
                    <span class="footer-brand-icon">
                        <i class="bi bi-grid-1x2-fill"></i>
                    </span>

                    <div>
                        <strong>INTECMEX <span>POS</span></strong>
                        <small>Control inteligente para tu negocio</small>
                    </div>
                </div>

                <span class="dashboard-footer-note">
                    <i class="bi bi-shield-check"></i>
                    Panel administrativo
                </span>
            </div>

        </div>
    @endif
@endsection
