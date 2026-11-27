@extends('master')

@section('custom_css')
    <link rel="stylesheet" href="{{ url('/static/css/sales.css?v=' . time()) }}">
@endsection


@section('custom_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        window.qzRoutes = {
            certificate: @json(url('/static/certs/digital-certificate.txt')),
            sign: @json(route('qz.sign'))
        };

        window.salesCurrency = @json(config('intecmex.currency'));
    </script>

    <script src="{{ url('/static/js/vendor/qz-tray.js') }}?v={{ time() }}"></script>

    <script>
        console.log('QZ Tray cargado:', typeof window.qz);
    </script>

    <script src="{{ url('/static/js/receipt-printer.js?v=' . time()) }}"></script>

    <script src="{{ url('/static/js/sales.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="sales-container">

        {{-- ENCABEZADO --}}
        <div class="sales-header">
            <div>
                <h1>Historial de ventas</h1>
                <p>Consulta y administra las ventas realizadas en tu negocio.</p>
            </div>

            <div class="sales-header-icon">
                <i class="bi bi-receipt"></i>
            </div>
        </div>

        {{-- RESUMEN --}}
        <div class="sales-summary">

            <div class="sales-summary-card">
                <div class="sales-summary-icon sales-icon-blue">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>

                <div>
                    <span>Ventas encontradas</span>
                    <h3>{{ number_format($salesCount ?? 0) }}</h3>
                </div>
            </div>

            <div class="sales-summary-card">
                <div class="sales-summary-icon sales-icon-green">
                    <i class="bi bi-cash"></i>
                </div>

                <div>
                    <span>Importe total</span>
                    <h3>${{ number_format($totalSales ?? 0, 2) }}</h3>
                </div>
            </div>

        </div>

        {{-- FILTROS --}}
        <div class="sales-panel">

            <div class="sales-panel-header">
                <div>
                    <h2><i class="fas fa-filter"></i> Filtros de búsqueda</h2>
                    <p>Encuentra ventas por folio o fecha.</p>
                </div>
            </div>

            <form action="{{ route('sales') }}" method="GET" class="sales-filters">

                <div class="sales-filter-group sales-filter-folio">
                    <label for="folio">Folio de venta</label>

                    <div class="sales-input-icon">
                        <i class="fas fa-search"></i>
                        <input type="text" id="folio" name="folio" placeholder="Buscar folio..."
                            value="{{ request('folio') }}">
                    </div>
                </div>

                <div class="sales-filter-group">
                    <label for="date_from">Fecha inicial</label>

                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="sales-filter-group">
                    <label for="date_to">Fecha final</label>

                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>

                <div class="sales-filter-actions">
                    <button type="submit" class="btn-sales-search">
                        <i class="fas fa-search"></i>
                        Buscar
                    </button>

                    <a href="{{ route('sales') }}" class="btn-sales-clear">
                        <i class="fas fa-eraser"></i>
                        Limpiar
                    </a>
                </div>

            </form>

        </div>

        {{-- TABLA DE VENTAS --}}
        <div class="sales-panel sales-table-panel">

            <div class="sales-panel-header">
                <div>
                    <h2><i class="fas fa-list"></i> Ventas registradas</h2>
                    <p>Listado de operaciones realizadas.</p>
                </div>

                <span class="sales-results-count">
                    {{ $sales->total() }} registros
                </span>
            </div>

            <div class="sales-table-wrapper">

                <table class="sales-table">

                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Cajero</th>
                            <th>Cliente</th>
                            <th>Pago</th>
                            <th>Estado</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($sales as $sale)
                            <tr>
                                <td>
                                    <span class="sales-folio">
                                        #{{ $sale->folio }}
                                    </span>
                                </td>

                                <td>
                                    <div class="sales-date">
                                        {{ $sale->sold_at?->format('d/m/Y') ?? '—' }}
                                    </div>

                                    <small class="sales-time">
                                        {{ $sale->sold_at?->format('h:i A') ?? '' }}
                                    </small>
                                </td>

                                <td>
                                    {{ $sale->user?->name ?? 'Sin cajero' }}
                                </td>

                                <td>
                                    {{ $sale->customer?->name ?? 'Público general' }}
                                </td>

                                <td>
                                    {{ ucfirst($sale->payment_method ?? '—') }}
                                </td>

                                <td>
                                    <span
                                        class="sales-status
                                    {{ $sale->status === 'completed' ? 'status-completed' : 'status-other' }}">
                                        {{ ucfirst($sale->status ?? 'Sin estado') }}
                                    </span>
                                </td>

                                <td>
                                    <strong class="sales-total">
                                        {{ config('intecmex.currency') . number_format($sale->total, 2) }}
                                    </strong>
                                </td>

                                <td>
                                    <div class="sales-actions">
                                        <button type="button" class="btn-sales-view" title="Ver venta"
                                            data-url="{{ route('sales.detail', $sale->id) }}">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <button type="button" class="btn-sales-print" title="Reimprimir ticket"
                                            data-url="{{ route('sales.detail', $sale->id) }}">
                                            <i class="bi bi-printer"></i>
                                        </button>




                                    </div>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="8">
                                    <div class="sales-empty">
                                        <i class="fas fa-receipt"></i>
                                        <h3>No hay ventas registradas</h3>
                                        <p>No encontramos ventas con los filtros seleccionados.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINACIÓN --}}
            @if ($sales->hasPages())
                <div class="sales-pagination">
                    {{ $sales->links() }}
                </div>
            @endif

        </div>

    </div>


    <div class="sales-modal-overlay" id="sale-detail-modal">

        <div class="sales-detail-modal">

            <div class="sales-detail-header">
                <div>
                    <h2><i class="fas fa-receipt"></i> Detalle de venta</h2>
                    <p id="detail-folio">Venta</p>
                </div>

                <button type="button" id="btn-close-sale-detail">
                    &times;
                </button>
            </div>

            <div class="sales-detail-body">

                <div id="sale-detail-loading" class="sales-detail-loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Cargando información...</span>
                </div>

                <div id="sale-detail-content" style="display:none;">

                    <div class="sales-detail-info">

                        <div>
                            <span>Fecha</span>
                            <strong id="detail-date">—</strong>
                        </div>

                        <div>
                            <span>Cajero</span>
                            <strong id="detail-cashier">—</strong>
                        </div>

                        <div>
                            <span>Cliente</span>
                            <strong id="detail-customer">—</strong>
                        </div>

                        <div>
                            <span>Método de pago</span>
                            <strong id="detail-payment">—</strong>
                        </div>

                        <div>
                            <span>Estado</span>
                            <strong id="detail-status">—</strong>
                        </div>

                    </div>

                    <div class="sales-detail-products">

                        <h3>Productos vendidos</h3>

                        <div class="sales-detail-table-wrapper">
                            <table class="sales-detail-table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Kilos</th>
                                        <th>Precio/kg</th>
                                        <th>Importe</th>
                                    </tr>
                                </thead>

                                <tbody id="detail-products"></tbody>
                            </table>
                        </div>

                    </div>

                    <div class="sales-detail-totals">

                        <div>
                            <span>Subtotal</span>
                            <strong id="detail-subtotal">$0.00</strong>
                        </div>

                        <div>
                            <span>Descuento</span>
                            <strong id="detail-discount">$0.00</strong>
                        </div>

                        <div>
                            <span>Impuestos</span>
                            <strong id="detail-tax">$0.00</strong>
                        </div>

                        <div class="sales-detail-grand-total">
                            <span>Total</span>
                            <strong id="detail-total">$0.00</strong>
                        </div>

                        <div>
                            <span>Efectivo recibido</span>
                            <strong id="detail-received">$0.00</strong>
                        </div>

                        <div>
                            <span>Cambio</span>
                            <strong id="detail-change">$0.00</strong>
                        </div>

                    </div>

                </div>

                <div id="sale-detail-error" class="sales-detail-error" style="display:none;">
                    No fue posible cargar el detalle de la venta.
                </div>

            </div>

            <div class="sales-detail-footer">
                <button type="button" id="btn-close-sale-detail-footer">
                    Cerrar
                </button>
            </div>

        </div>

    </div>
@endsection
