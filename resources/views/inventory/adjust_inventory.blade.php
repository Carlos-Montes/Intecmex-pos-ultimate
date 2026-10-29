@extends('master')

@section('custom_js')
    <script src="{{ url('/static/js/meat_distribution.js?v=' . time()) }}"></script>
@endsection

@section('subRouter')inventory_{{ $type }}@endsection

@section('content')
<div class="panel mtop16 sh meat-distribution">
    <div class="panel-header">
        <div class="inside">
            <h5><i class="bi bi-box-seam"></i> Distribución de carne</h5>
            <p class="md-subtitle">
                Distribuye los kilos de canal entre tus productos.
            </p>
        </div>
    </div>

    <div class="panel-body">
        <div class="inside">

            {{-- Resumen de la canal --}}
            <div class="md-summary">
                <div class="md-summary-card">
                    <div class="md-summary-icon">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                    <div>
                        <span>Peso de canal</span>
                        <h3 id="channel_weight">0.00 <small>kg</small></h3>
                    </div>
                </div>

                <div class="md-summary-card">
                    <div class="md-summary-icon md-icon-green">
                        <i class="bi bi-box-arrow-in-down"></i>
                    </div>
                    <div>
                        <span>Kilos disponibles</span>
                        <h3 id="kilos_available">0.00 <small>kg</small></h3>
                    </div>
                </div>

                <div class="md-summary-card">
                    <div class="md-summary-icon md-icon-blue">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div>
                        <span>Kilos distribuidos</span>
                        <h3 id="kilos_distributed">0.00 <small>kg</small></h3>
                    </div>
                </div>

                <div class="md-summary-card">
                    <div class="md-summary-icon md-icon-orange">
                        <i class="bi bi-cart-plus"></i>
                    </div>
                    <div>
                        <span>En esta operación</span>
                        <h3 id="md-total-selected">0.00 <small>kg</small></h3>
                    </div>
                </div>
            </div>

            {{-- Selección del animal --}}
            <form action="" method="get" id="animal_selector">
                <div class="md-section">
                    <div class="md-section-title">
                        <div>
                            <h5>
                                <i class="bi bi-clipboard2-pulse"></i>
                                Seleccionar canal
                            </h5>
                            <p>Elige el animal que deseas distribuir.</p>
                        </div>
                    </div>

                    <div class="md-animal-selector">
                        <div class="md-field">
                            <label for="md-animal">
                                Animal con merma registrada
                            </label>

                            <select id="md-animal" class="form-control">
                                <option value="">Seleccionar animal</option>
                                @foreach (getCategorieLiveWeight() as $item => $value)
                                    <option value="{{ $item }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md-animal-status">
                            <i class="bi bi-info-circle"></i>
                            Selecciona un animal para consultar su canal disponible.
                        </div>
                    </div>
                </div>
            </form>

            {{-- Formulario de distribución --}}
            <form id="form-meat-distribution" method="POST">
                @csrf
                <input type="hidden" name="category" class="md-form-data form-control" id="md-form-category" readonly>
                <input type="hidden" name="channel_weight" class="md-form-data form-control" id="md-form-channel-weight" readonly>
                <input type="hidden" name="available_before" class="md-form-data form-control" id="md-form-available-before" readonly>
                <input type="hidden" name="distributed_before" class="md-form-data form-control" id="md-form-distributed-before" readonly>
                <input type="hidden" name="total_assigned" class="md-form-data form-control" id="md-form-total-assigned" readonly>
                <input type="hidden" name="available_after" class="md-form-data form-control" id="md-form-available-after" readonly>
                {{-- Productos --}}
                <div class="md-section">
                    <div class="md-section-title md-products-heading">
                        <div>
                            <h5><i class="bi bi-grid"></i> Productos</h5>
                            <p>Ingresa los kilos que deseas asignar a cada producto.</p>
                        </div>

                        <div class="md-products-count">
                            <i class="bi bi-box"></i>
                            Catálogo de productos
                        </div>
                    </div>

                    <div class="md-toolbar">
                        <div class="md-search">
                            <i class="bi bi-search"></i>
                            <input type="text" id="md-search"
                                   placeholder="Buscar producto..."
                                   autocomplete="off">
                        </div>

                        <div class="md-filter">
                            <select id="md-category" class="form-control">
                                <option value="">Seleccionar animal</option>
                                @foreach (getCategorieLiveWeight() as $item => $value)
                                    <option value="{{ $item }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="md-product-grid" id="md-product-grid">
                        @foreach ($product as $item => $value)
                            <div class="md-product-card"
                                 data-name="{{ strtolower($value->name) }}"
                                 data-category="{{ strtolower($value->cat->name ?? '') }}"
                                 data-category-id="{{ $value->category_id }}"
                                 data-code="{{ strtolower($value->code ?? '') }}">

                                <div class="md-product-image"
                                     style="background-image: url('{{ getFileUrl($value->image) }}');">
                                    <span class="md-product-category">
                                        {{ $value->cat->name ?? 'Sin categoría' }}
                                    </span>
                                </div>

                                <div class="md-product-info">
                                    <h6>{{ $value->name }}</h6>

                                    <p>
                                        Inventario actual:
                                        <strong>
                                            {{ number_format((float) ($value->meatInventory->quantity_kg ?? 0), 2) }} kg
                                        </strong>
                                    </p>

                                    <label for="md-quantity-{{ $item }}">
                                        Cantidad a distribuir
                                    </label>

                                    <div class="md-quantity-input">
                                        <input type="number"
                                               id="md-quantity-{{ $item }}"
                                               class="md-quantity"
                                               min="0"
                                               step="0.01"
                                               value="0"
                                               data-product-id="{{ $value->id }}">
                                        <span>kg</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="md-no-products" id="md-no-products"
                             style="display:none;">
                            <i class="bi bi-search"></i>
                            <p>No se encontraron productos.</p>
                        </div>
                    </div>
                </div>

                {{-- Resumen y acción --}}
                <div class="md-footer">
                    <div class="md-footer-info">
                        <div class="md-footer-icon">
                            <i class="bi bi-calculator"></i>
                        </div>

                        <div>
                            <span>Total a distribuir</span>
                            <h4 id="md-footer-total">0.00 kg</h4>
                            <p id="md-remaining">
                                Selecciona un animal para consultar el saldo.
                            </p>
                        </div>
                    </div>

                    <button type="button"
                            class="btn md-btn-save"
                            id="md-save-distribution">
                        <i class="bi bi-check2-circle"></i>
                        Guardar distribución
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection