@extends('master')

@section('custom_css')
    <link rel="stylesheet" href="{{ url('/static/css/pos.css?v=' . time()) }}">
@endsection

@section('custom_js')
    <script>
        window.cashRegisterRoutes = {
            status: @json(route('api.cash-register.status')),
            open: @json(route('api.cash-register.open')),
            products: @json(route('api.pos.products'))
        };
    </script>

    <script src="{{ url('/static/js/pos.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="pos-app">

        {{-- INFORMACIÓN DE CAJA ABIERTA --}}
        @if ($session)
            <div class="pos-cash-info">
                <span>🟢 Caja abierta</span>
                <strong>{{ $session->account->name }}</strong>
                <small>
                    Apertura: ${{ number_format($session->opening_amount, 2) }}
                    | Responsable: {{ $session->user->name }}
                </small>
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
                    <button type="button" class="category-btn active" data-category="all"> Todos </button>

                    @foreach ($categories as $category)
                        <button type="button" class="category-btn" data-category="{{ $category->id }}">
                            {{ $category->name }} </button>
                    @endforeach
                </nav>

                {{-- PRODUCTOS DE DEMOSTRACIÓN --}}
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

                {{-- CLIENTE PREDETERMINADO --}}
                <div class="customer-card">
                    <div class="customer-icon">👥</div>

                    <div class="customer-details">
                        <span>Cliente</span>
                        <strong id="customer-name">Público General</strong>
                        <small>Cliente por defecto</small>
                    </div>

                    <button type="button" class="btn-change-customer" id="btn-change-customer">
                        Buscar cliente
                    </button>
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
                            <input type="number" id="tax-value" value="0" min="0" step="0.01" placeholder="0">
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
                    <button type="button" class="btn-pause" id="btn-pause">
                        <span>Ⅱ</span>
                        <strong>Pausar</strong>
                        <small>F2</small>
                    </button>

                    <button type="button" class="btn-cancel" id="btn-cancel">
                        <span>▣</span>
                        <strong>Cancelar</strong>
                        <small>F3</small>
                    </button>

                    <button type="button" class="btn-charge" id="btn-charge">
                        <span>▣</span>
                        <strong>Cobrar</strong>
                        <small>F4</small>
                    </button>
                </div>

            </aside>
        </main>
    </div>
@endsection
