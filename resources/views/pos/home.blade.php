@extends('master')

@section('custom_css')
    <link rel="stylesheet" href="{{ url('/static/css/pos.css?v=' . time()) }}">
@endsection

@section('custom_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        window.cashRegisterRoutes = {
            status: @json(route('api.cash-register.status')),
            open: @json(route('api.cash-register.open')),
            products: @json(route('api.pos.products')),
            customers: @json(route('api.pos.customers')),
            sales: @json(route('api.pos.sales.store')),
            summary: @json(route('api.cash-register.summary')),
            close: @json(route('api.cash-register.close'))
        };

        window.qzRoutes = {
            certificate: @json(url('/static/certs/digital-certificate.txt')),
            sign: @json(route('qz.sign'))
        };
    </script>

    {{-- Librería de conexión con QZ Tray --}}
    <script src="{{ url('/static/js/vendor/qz-tray.js') }}"></script>

    {{-- JavaScript principal del punto de venta --}}
    <script src="{{ url('/static/js/pos.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="pos-app">

        {{-- INFORMACIÓN DE CAJA ABIERTA --}}
        @if ($session)
            <div class="pos-cash-info">
                <div class="pos-cash-details">
                    <span>🟢 Caja abierta</span>
                    <strong>{{ $session->account->name }}</strong>

                    <small>
                        Apertura: ${{ number_format($session->opening_amount, 2) }}
                        | Responsable: {{ $session->user->name }}
                    </small>
                </div>

                <button type="button" id="btn-close-cash" class="btn-close-cash">
                    <span>🔒</span>
                    <strong>Cerrar caja</strong>
                </button>
            </div>
        @endif

        {{-- MODAL OBLIGATORIO DE APERTURA --}}
        @unless ($session)
            <div id="cash-opening-overlay" class="cash-opening-overlay">
                <div class="cash-opening-modal">

                    <div class="cash-opening-icon">💰</div>

                    <h2>Apertura de caja</h2>

                    <p>
                        Para comenzar a trabajar en el punto de venta,
                        primero debes abrir la caja.
                    </p>

                    <form id="form-open-cash">
                        @csrf

                        <div class="cash-form-group">
                            <label for="cash-account">Cuenta contable</label>

                            <select id="cash-account" name="account_id" required>
                                <option value="">Cargando cuentas...</option>
                            </select>
                        </div>

                        <div class="cash-form-group">
                            <label for="cash-opening-amount">
                                Monto inicial de efectivo
                            </label>

                            <input type="number" id="cash-opening-amount" name="opening_amount" min="0" step="0.01"
                                placeholder="0.00" required>
                        </div>

                        <div id="cash-opening-error" class="cash-opening-error" hidden></div>

                        <button type="submit" id="btn-open-cash" disabled>
                            Abrir caja
                        </button>
                    </form>
                </div>
            </div>
        @endunless

        {{-- PANEL PRINCIPAL --}}
        <main class="pos-layout">

            {{-- COLUMNA DE PRODUCTOS Y BÁSCULA --}}
            <section class="pos-products-panel">

                {{-- BÁSCULA --}}
                <div class="scale-card">
                    <div class="scale-icon">⚖</div>

                    <div class="scale-reading">
                        <label for="scale-weight">
                            Peso actual de báscula
                        </label>

                        <div class="scale-weight-box">
                            <input id="scale-weight" type="number" min="0" step="0.001"
                                aria-label="Capturar peso manualmente">
                            <span>KG</span>
                        </div>
                    </div>

                    <div class="scale-status">
                        <div class="scale-status-text">
                            <span id="scale-indicator" class="status-dot offline"></span>

                            <strong id="scale-status-label">
                                Báscula desconectada
                            </strong>
                        </div>

                        <small id="scale-message">
                            Esperando conexión...
                        </small>

                        <button type="button" class="btn-tare" id="btn-tare">
                            ⚖ &nbsp; Tara
                            <span id="scale-tare">(0.000)</span>
                        </button>
                    </div>
                </div>

                {{-- BUSCADOR --}}
                <div class="pos-search">
                    <span class="search-icon">⌕</span>

                    <input type="search" id="product-search" placeholder="Buscar producto o escanear código..."
                        autocomplete="off">

                    <button type="button" id="clear-search" aria-label="Limpiar búsqueda">×</button>
                </div>

                {{-- CATEGORÍAS --}}
                <nav class="pos-categories" aria-label="Categorías">
                    <button type="button" class="category-btn active" data-category="all">
                        Todos
                    </button>

                    @foreach ($categories as $category)
                        <button type="button" class="category-btn" data-category="{{ $category->id }}">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </nav>

                {{-- PRODUCTOS --}}
                <div class="products-grid" id="products-grid">
                    <div class="products-loading">
                        Cargando productos...
                    </div>
                </div>

                <div class="products-empty" id="products-empty" hidden>
                    No se encontraron productos.
                </div>
            </section>

            {{-- COLUMNA DEL TICKET --}}
            <aside class="pos-ticket-panel">

                <div class="ticket-heading">
                    <div class="ticket-heading-title">
                        <span class="ticket-heading-icon">🧾</span>
                        <h2>Ticket de venta</h2>
                    </div>

                    <button type="button" class="btn-clear" id="btn-clear-ticket">
                        🗑 Limpiar
                    </button>
                </div>

                {{-- SELECCIÓN DE CLIENTE --}}
                <div class="customer-card">
                    <div class="customer-icon">👥</div>

                    <div class="customer-details">
                        <span>Cliente</span>

                        <select id="customer-select" class="form-select">
                            <option value="0" data-discount="0">
                                Público General
                            </option>
                        </select>

                        <small id="customer-discount-label">
                            Sin descuento
                        </small>
                    </div>
                </div>

                {{-- TABLA DEL TICKET --}}
                <div class="ticket-table-wrapper">
                    <table class="ticket-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th>Peso (kg)</th>
                                <th>Precio/kg</th>
                                <th>Importe</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id="ticket-items">
                            <tr class="ticket-empty-row">
                                <td colspan="6">
                                    Selecciona un producto para comenzar
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- TOTALES --}}
                <div class="ticket-totals">
                    <div class="total-line">
                        <span>Subtotal</span>
                        <strong id="ticket-subtotal">$0.00</strong>
                    </div>

                    <div class="total-line">
                        <span>Descuento</span>

                        <div class="ticket-manual-field">
                            <select id="discount-type">
                                <option value="percent">%</option>
                                <option value="amount">$</option>
                            </select>

                            <input type="number" id="discount-value" value="0" min="0" step="0.01">
                        </div>

                        <strong id="ticket-discount">$0.00</strong>
                    </div>

                    <div class="total-line">
                        <span>IVA (%)</span>

                        <div class="ticket-manual-field">
                            <input type="number" id="tax-value" value="0" min="0" step="0.01"
                                placeholder="0">
                        </div>

                        <strong id="ticket-tax">$0.00</strong>
                    </div>

                    <div class="grand-total">
                        <span>TOTAL</span>
                        <strong id="ticket-total">$0.00</strong>
                    </div>
                </div>

                {{-- ACCIONES --}}
                <div class="ticket-actions">
                    <button type="button" class="btn-cancel" id="btn-cancel">
                        <span>▣</span>
                        <strong>Cancelar</strong>
                    </button>

                    <button type="button" class="btn-charge" id="btn-charge">
                        <span>▣</span>
                        <strong>Cobrar</strong>
                    </button>
                </div>

            </aside>
        </main>

        {{-- MODAL DE COBRO --}}
        <div class="checkout-overlay" id="checkout-overlay" hidden>
            <div class="checkout-modal">

                <div class="checkout-header">
                    <div>
                        <span>🧾</span>
                        <h2>Finalizar venta</h2>
                        <small>Confirma los importes antes de cobrar</small>
                    </div>

                    <button type="button" id="btn-close-checkout">×</button>
                </div>

                <div class="checkout-summary">
                    <div>
                        <span>Subtotal</span>
                        <strong id="checkout-subtotal">$0.00</strong>
                    </div>

                    <div>
                        <span>Descuento del cliente</span>
                        <strong id="checkout-customer-discount">$0.00</strong>
                    </div>

                    <div>
                        <span>Descuento manual</span>
                        <strong id="checkout-manual-discount">$0.00</strong>
                    </div>

                    <div>
                        <span>IVA</span>
                        <strong id="checkout-tax">$0.00</strong>
                    </div>

                    <div class="checkout-total">
                        <span>Total a pagar</span>
                        <strong id="checkout-total">$0.00</strong>
                    </div>
                </div>

                <form id="checkout-form">
                    @csrf

                    <div class="checkout-cash-group">
                        <label for="cash-received">
                            Efectivo recibido
                        </label>

                        <input type="number" id="cash-received" min="0" step="0.01" value="0" required>
                    </div>

                    <div class="checkout-change">
                        <span>Cambio</span>
                        <strong id="checkout-change">$0.00</strong>
                    </div>

                    <div id="checkout-error" class="checkout-error" hidden></div>

                    <div class="checkout-actions">
                        <button type="button" id="btn-cancel-checkout">
                            Regresar
                        </button>

                        <button type="submit" id="btn-confirm-sale">
                            Confirmar y cobrar
                        </button>
                    </div>
                </form>

            </div>
        </div>
        {{-- MODAL DE CORTE Y CIERRE DE CAJA --}}
        <div class="cash-closing-overlay" id="cash-closing-overlay" hidden>
            <div class="cash-closing-modal">

                <div class="cash-closing-header">
                    <div>
                        <span class="cash-closing-icon">🔐</span>
                        <h2>Corte de caja</h2>
                        <small>Revisa tus ventas y realiza el arqueo de efectivo</small>
                    </div>

                    <button type="button" id="btn-close-cash-modal">×</button>
                </div>

                <div class="cash-closing-summary">

                    <div class="cash-summary-card">
                        <span>Fondo inicial</span>
                        <strong id="closing-opening">$0.00</strong>
                    </div>

                    <div class="cash-summary-card">
                        <span>Ventas en efectivo</span>
                        <strong id="closing-sales">$0.00</strong>
                        <small id="closing-sales-count">0 ventas</small>
                    </div>

                    <div class="cash-summary-card cash-summary-expected">
                        <span>Efectivo esperado</span>
                        <strong id="closing-expected">$0.00</strong>
                    </div>

                </div>

                <div class="cash-count-section">

                    <div class="cash-count-title">
                        <div>
                            <h3>Arqueo de efectivo</h3>
                            <p>Captura cuántos billetes y monedas tienes físicamente.</p>
                        </div>
                    </div>

                    <div class="cash-denominations" id="cash-denominations">

                        <div class="cash-denomination-row">
                            <label>Billetes de $1,000</label>
                            <input type="number" min="0" step="1" value="0"
                                data-denomination="bill_1000">
                            <strong>$0.00</strong>
                        </div>

                        <div class="cash-denomination-row">
                            <label>Billetes de $500</label>
                            <input type="number" min="0" step="1" value="0"
                                data-denomination="bill_500">
                            <strong>$0.00</strong>
                        </div>

                        <div class="cash-denomination-row">
                            <label>Billetes de $200</label>
                            <input type="number" min="0" step="1" value="0"
                                data-denomination="bill_200">
                            <strong>$0.00</strong>
                        </div>

                        <div class="cash-denomination-row">
                            <label>Billetes de $100</label>
                            <input type="number" min="0" step="1" value="0"
                                data-denomination="bill_100">
                            <strong>$0.00</strong>
                        </div>

                        <div class="cash-denomination-row">
                            <label>Billetes de $50</label>
                            <input type="number" min="0" step="1" value="0"
                                data-denomination="bill_50">
                            <strong>$0.00</strong>
                        </div>

                        <div class="cash-denomination-row">
                            <label>Billetes de $20</label>
                            <input type="number" min="0" step="1" value="0"
                                data-denomination="bill_20">
                            <strong>$0.00</strong>
                        </div>

                        <div class="cash-denomination-row">
                            <label>Monedas de $20</label>
                            <input type="number" min="0" step="1" value="0"
                                data-denomination="coin_20">
                            <strong>$0.00</strong>
                        </div>

                        <div class="cash-denomination-row">
                            <label>Monedas de $10</label>
                            <input type="number" min="0" step="1" value="0"
                                data-denomination="coin_10">
                            <strong>$0.00</strong>
                        </div>

                        <div class="cash-denomination-row">
                            <label>Monedas de $5</label>
                            <input type="number" min="0" step="1" value="0"
                                data-denomination="coin_5">
                            <strong>$0.00</strong>
                        </div>

                        <div class="cash-denomination-row">
                            <label>Monedas de $2</label>
                            <input type="number" min="0" step="1" value="0"
                                data-denomination="coin_2">
                            <strong>$0.00</strong>
                        </div>

                        <div class="cash-denomination-row">
                            <label>Monedas de $1</label>
                            <input type="number" min="0" step="1" value="0"
                                data-denomination="coin_1">
                            <strong>$0.00</strong>
                        </div>

                        <div class="cash-denomination-row">
                            <label>Monedas de $0.50</label>
                            <input type="number" min="0" step="1" value="0"
                                data-denomination="coin_50c">
                            <strong>$0.00</strong>
                        </div>

                    </div>
                </div>

                <div class="cash-closing-result">

                    <div>
                        <span>Total contado físicamente</span>
                        <strong id="closing-counted">$0.00</strong>
                    </div>

                    <div class="cash-difference-line" id="closing-difference-line">
                        <span>Diferencia</span>
                        <strong id="closing-difference">$0.00</strong>
                    </div>

                    <small id="closing-difference-message">
                        Captura el efectivo para calcular la diferencia.
                    </small>

                </div>

                <div id="cash-closing-error" class="cash-closing-error" hidden></div>

                <div class="cash-closing-actions">
                    <button type="button" id="btn-cancel-cash-closing">
                        Regresar al POS
                    </button>

                    <button type="button" id="btn-confirm-cash-closing">
                        🔒 Confirmar corte y cerrar caja
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection
