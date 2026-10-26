@extends('master')

@section('custom_css')
    <link rel="stylesheet" href="{{ url('/static/css/merma.css?v=' . time()) }}">
@endsection

@section('custom_js')
    <script src="{{ url('/static/js/live_weight.js?v=' . time()) }}"></script>
@endsection

@section('subRouter')inventory_{{ $type }}@endsection

@section('content')
    <div class="merma-app">
        <header class="merma-header">
            <aside class="merma-warning">
                <span class="warning-icon">ⓘ</span>
                <p> Este proceso calculará el peso final (canal) restando el porcentaje de merma al peso vivo registrado. Al guardar, el peso canal se enviará al inventario.</p>
            </aside>
        </header>
        <section class="merma-summary">
            <article class="merma-summary-card d-flex">
                <div class="summary-animal pig">📊</div>
                <div class="summary-label d-flex">
                    <span>Selecciona una fecha para continuar</span>
                    <input type="date" name="search_pig_res" id="search_pig_res" class="form-control">
                </div>
            </article>
            <article class="merma-summary-card">
                <div class="summary-animal pig">🐖</div>
                <div class="summary-label">
                    <span>Cerdos pendientes</span>
                    <span>de procesar</span>
                </div>
                <strong class="summary-number" id="pig-strong"> 0 </strong>
            </article>
            <article class="merma-summary-card">
                <div class="summary-animal cow">🐄</div>
                <div class="summary-label">
                    <span>Reses pendientes</span>
                    <span>de procesar</span>
                </div>
                <strong class="summary-number" id="res-strong"> 0 </strong>
            </article>
        </section>
        <section class="merma-workspace">
            <article class="merma-panel animal-panel">
                <div class="merma-panel-heading">
                    <span class="panel-icon">☷</span>
                    <div>
                        <h2>Seleccionar animal</h2>
                        <p>Elige un animal registrado en peso vivo</p>
                    </div>
                </div>
                <div class="merma-field">
                    <label for="animal-type">Tipo de animal</label>
                    <select id="animal-type" name="animal_type">
                        <option value="0">🐖 Cerdo</option>
                        <option value="1">🐄 Res</option>
                    </select>
                </div>
                <div class="merma-field">
                    <label for="animal-record">Animal (registro)</label>
                    <select id="animal-record" name="animal_id">
                        <option value="">Selecciona una fecha primero</option>
                    </select>
                    <small id="animal-date">Sin animal seleccionado</small>
                </div>
                <div class="animal-detail-card">
                    <h3>
                        <span id="animal-emoji">🐖</span>
                        <span id="animal-title">Selecciona un animal</span>
                    </h3>
                    <div class="animal-detail-row">
                        <span>Fecha de registro</span>
                        <strong id="animal-detail-date">—</strong>
                    </div>
                    <div class="animal-detail-row">
                        <span>Peso vivo</span>
                        <strong>
                            <span id="animal-live-weight">0.00</span> Kg
                        </strong>
                    </div>
                    <div class="animal-detail-row">
                        <span>Estado</span>
                        <span class="merma-badge pending" id="animal-status"> Pendiente de merma </span>
                    </div>
                </div>
            </article>
            <article class="merma-panel calculation-panel">
                <div class="merma-panel-heading">
                    <span class="panel-icon">▦</span>
                    <div>
                        <h2>Cálculo de merma</h2>
                        <p>Ingresa el porcentaje de merma</p>
                    </div>
                </div>
                <div class="merma-metric live">
                    <span class="metric-icon">💧</span>
                    <strong>Peso vivo (registrado)</strong>
                    <output id="calc-live-weight">00 Kg</output>
                </div>

                <div class="merma-field">
                    <label for="merma-percentage">
                        Porcentaje de merma (%)
                    </label>

                    <div class="percentage-input">
                        <input type="number" id="merma-percentage" name="merma_percentage" min="0" max="100" step="0.1" inputmode="decimal">
                        <span>%</span>
                    </div>
                </div>

                <div class="merma-metric loss">
                    <span class="metric-icon">💧</span>
                    <strong>Kg perdidos (merma)</strong>
                    <output id="calc-loss-weight">00.00 Kg</output>
                </div>

                <div class="merma-metric final">
                    <span class="metric-icon">🥩</span>
                    <strong>Peso canal (final)</strong>
                    <output id="calc-final-weight">00.00 Kg</output>
                </div>

                <button type="button" class="btn-save-merma" id="btn-save-merma">
                    ▣ &nbsp; Guardar Merma
                </button>

            </article>

            {{-- REPRESENTACIÓN VISUAL --}}
            <article class="merma-panel visualization-panel">

                <div class="merma-panel-heading">
                    <span class="panel-icon">▥</span>
                    <div>
                        <h2>Representación visual</h2>
                        <p>Distribución del peso</p>
                    </div>
                </div>

                <div class="weight-visual-card">
                    <span class="weight-visual-icon live-icon">🥩</span>
                    <div class="weight-visual-content">
                        <strong>Peso vivo</strong>
                        <h3 id="visual-live-weight">0.00 Kg</h3>
                        <div class="weight-bar">
                            <span id="bar-live" class="bar-live" style="width: 0%"></span>
                        </div>
                    </div>
                    <b>0%</b>
                </div>

                <div class="weight-visual-card">
                    <span class="weight-visual-icon loss-icon">💧</span>
                    <div class="weight-visual-content">
                        <strong>Merma</strong>
                        <h3 id="visual-loss-weight">0.00 Kg</h3>
                        <div class="weight-bar">
                            <span id="bar-loss" class="bar-loss" style="width: 0%"></span>
                        </div>
                    </div>
                    <b id="visual-loss-percent">0%</b>
                </div>

                <div class="weight-visual-card">
                    <span class="weight-visual-icon final-icon">🥩</span>
                    <div class="weight-visual-content">
                        <strong>Peso canal (final)</strong>
                        <h3 id="visual-final-weight">0.00 Kg</h3>
                        <div class="weight-bar">
                            <span id="bar-final" class="bar-final" style="width: 0%"></span>
                        </div>
                    </div>
                    <b id="visual-final-percent">0%</b>
                </div>

                <div class="merma-info">
                    <span>ⓘ</span>
                    <p>
                        El peso canal es el resultado de restar el porcentaje
                        de merma al peso vivo. Este peso se enviará al
                        inventario como producto disponible para su venta.
                    </p>
                </div>

            </article>

        </section>

        {{-- ANIMALES PENDIENTES --}}
        <section class="merma-panel pending-panel">

            <div class="pending-heading">
                <span>◷</span>
                <h2>Animales pendientes de merma</h2>
            </div>

            <div class="pending-table-wrapper">
                <table class="pending-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Registro</th>
                            <th>Fecha de peso vivo</th>
                            <th>Peso vivo (Kg)</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody id="pending-animals">
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    @include('inventory.components.modal-edit-inventory')
@endsection
