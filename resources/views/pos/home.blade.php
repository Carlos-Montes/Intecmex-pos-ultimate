@extends('master')

@section('custom_css')
    <link rel="stylesheet" href="{{ url('/static/css/pos.css?v='.time()) }}">
@endsection

@section('content')
    <div class="pos-app">
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
                            <input id="scale-weight" type="text" value="0.000" readonly
                                aria-label="Peso recibido de la báscula">
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
                    <button class="category-btn active" data-category="all">Todos</button>
                    <button class="category-btn" data-category="Res">Res</button>
                    <button class="category-btn" data-category="Cerdo">Cerdo</button>
                    <button class="category-btn" data-category="Pollo">Pollo</button>
                    <button class="category-btn" data-category="Embutidos">Embutidos</button>
                    <button class="category-btn" data-category="Otros">Otros</button>
                </nav>

                {{-- TARJETAS DE PRODUCTOS --}}
                <div class="products-grid" id="products-grid">

                    @php
                        $demoProducts = [
                            [
                                'id' => 1,
                                'name' => 'Bistec',
                                'code' => 'RES001',
                                'category' => 'Res',
                                'price' => 220,
                                'image' => null,
                            ],
                            [
                                'id' => 2,
                                'name' => 'Costilla',
                                'code' => 'RES002',
                                'category' => 'Res',
                                'price' => 180,
                                'image' => null,
                            ],
                            [
                                'id' => 3,
                                'name' => 'Molida',
                                'code' => 'RES003',
                                'category' => 'Res',
                                'price' => 170,
                                'image' => null,
                            ],
                            [
                                'id' => 4,
                                'name' => 'Arrachera',
                                'code' => 'RES004',
                                'category' => 'Res',
                                'price' => 290,
                                'image' => null,
                            ],
                            [
                                'id' => 5,
                                'name' => 'Diezmillo',
                                'code' => 'RES005',
                                'category' => 'Res',
                                'price' => 210,
                                'image' => null,
                            ],
                            [
                                'id' => 6,
                                'name' => 'Suadero',
                                'code' => 'RES006',
                                'category' => 'Res',
                                'price' => 160,
                                'image' => null,
                            ],
                            [
                                'id' => 7,
                                'name' => 'Aguja',
                                'code' => 'RES007',
                                'category' => 'Res',
                                'price' => 190,
                                'image' => null,
                            ],
                            [
                                'id' => 8,
                                'name' => 'Chamorro',
                                'code' => 'RES008',
                                'category' => 'Res',
                                'price' => 150,
                                'image' => null,
                            ],
                            [
                                'id' => 9,
                                'name' => 'Filete',
                                'code' => 'RES009',
                                'category' => 'Res',
                                'price' => 320,
                                'image' => null,
                            ],
                            [
                                'id' => 10,
                                'name' => 'T-Bone',
                                'code' => 'RES010',
                                'category' => 'Res',
                                'price' => 340,
                                'image' => null,
                            ],
                            [
                                'id' => 11,
                                'name' => 'Chorizo',
                                'code' => 'EMB001',
                                'category' => 'Embutidos',
                                'price' => 160,
                                'image' => null,
                            ],
                            [
                                'id' => 12,
                                'name' => 'Longaniza',
                                'code' => 'EMB002',
                                'category' => 'Embutidos',
                                'price' => 155,
                                'image' => null,
                            ],
                        ];
                    @endphp

                    @foreach ($demoProducts as $product)
                        <button type="button" class="product-card" data-id="{{ $product['id'] }}"
                            data-name="{{ $product['name'] }}" data-code="{{ $product['code'] }}"
                            data-category="{{ $product['category'] }}" data-price="{{ $product['price'] }}">

                            <div class="product-image">
                                @if ($product['image'])
                                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}">
                                @else
                                    <div class="product-placeholder">🥩</div>
                                @endif
                            </div>

                            <div class="product-info">
                                <strong>{{ $product['name'] }}</strong>

                                <span class="product-price">
                                    ${{ number_format($product['price'], 2) }} / kg
                                </span>

                                <small>{{ $product['code'] }}</small>
                            </div>
                        </button>
                    @endforeach

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
                        <strong id="ticket-discount">$0.00</strong>
                    </div>

                    <div class="total-line">
                        <span>IVA</span>
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
   

