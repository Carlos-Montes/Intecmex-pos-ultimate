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
                <h2>Panel de control</h2>
                <p>Todo lo importante de tu negocio, en un solo lugar.</p>
            </div>

            <div class="dashboard-heading-actions">
                <span class="preview-badge">
                    <span></span> Vista previa
                </span>

                <div class="dashboard-date">
                    <i class="bi bi-calendar3"></i>
                    <span id="dashboard-current-date">Panel administrativo</span>
                </div>
            </div>
        </div>

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
                    <small>Revisar productos</small>
                </span>
                <i class="bi bi-arrow-up-right action-arrow"></i>
            </a>
        </div>

        {{-- Indicadores --}}
        <div class="dashboard-section-heading">
            <div>
                <h4>Resumen operativo</h4>
                <p>Indicadores que tendrá disponible tu negocio.</p>
            </div>
            <span class="dashboard-period">
                <i class="bi bi-bar-chart-line"></i> Indicadores principales
            </span>
        </div>

        <div class="dashboard-metrics">

            <article class="metric-card">
                <div class="metric-top">
                    <span class="metric-icon metric-icon-blue">
                        <i class="bi bi-boxes"></i>
                    </span>
                    <span class="metric-tag">Catálogo</span>
                </div>
                <p class="metric-label">Productos registrados</p>
                <div class="metric-value">---</div>
                <div class="metric-footer">
                    <i class="bi bi-info-circle"></i>
                    <span>Total de productos del sistema</span>
                </div>
            </article>

            <article class="metric-card">
                <div class="metric-top">
                    <span class="metric-icon metric-icon-green">
                        <i class="bi bi-speedometer2"></i>
                    </span>
                    <span class="metric-tag">Existencias</span>
                </div>
                <p class="metric-label">Inventario de carne</p>
                <div class="metric-value">--- <small>kg</small></div>
                <div class="metric-footer">
                    <i class="bi bi-info-circle"></i>
                    <span>Kilos disponibles registrados</span>
                </div>
            </article>

            <article class="metric-card">
                <div class="metric-top">
                    <span class="metric-icon metric-icon-orange">
                        <i class="bi bi-arrow-left-right"></i>
                    </span>
                    <span class="metric-tag">Operaciones</span>
                </div>
                <p class="metric-label">Distribuciones realizadas</p>
                <div class="metric-value">---</div>
                <div class="metric-footer">
                    <i class="bi bi-info-circle"></i>
                    <span>Movimientos de carne registrados</span>
                </div>
            </article>

            <article class="metric-card">
                <div class="metric-top">
                    <span class="metric-icon metric-icon-red">
                        <i class="bi bi-activity"></i>
                    </span>
                    <span class="metric-tag">Rendimiento</span>
                </div>
                <p class="metric-label">Merma acumulada</p>
                <div class="metric-value">--- <small>kg</small></div>
                <div class="metric-footer">
                    <i class="bi bi-info-circle"></i>
                    <span>Pérdida de peso registrada</span>
                </div>
            </article>

        </div>

        {{-- Gráficas --}}
        <div class="dashboard-content-grid">

            <section class="dashboard-panel dashboard-inventory-panel">
                <div class="dashboard-panel-heading">
                    <div>
                        <span class="panel-kicker">EXISTENCIAS</span>
                        <h4>Inventario por categoría</h4>
                        <p>Distribución de kilos disponibles por tipo de carne.</p>
                    </div>
                    <span class="panel-heading-icon">
                        <i class="bi bi-pie-chart"></i>
                    </span>
                </div>

                <div class="inventory-comparison">

                    <div class="inventory-category">
                        <div class="category-heading">
                            <div class="category-title">
                                <span class="category-dot pork-dot"></span>
                                <strong>Carne de cerdo</strong>
                            </div>
                            <strong>--- kg</strong>
                        </div>
                        <div class="inventory-track">
                            <div class="inventory-progress pork-progress"></div>
                        </div>
                        <small>Existencia disponible</small>
                    </div>

                    <div class="inventory-category">
                        <div class="category-heading">
                            <div class="category-title">
                                <span class="category-dot beef-dot"></span>
                                <strong>Carne de res</strong>
                            </div>
                            <strong>--- kg</strong>
                        </div>
                        <div class="inventory-track">
                            <div class="inventory-progress beef-progress"></div>
                        </div>
                        <small>Existencia disponible</small>
                    </div>

                </div>

                <div class="dashboard-info-note">
                    <i class="bi bi-lightbulb"></i>
                    <span>Consulta el inventario para conocer las existencias de cada producto.</span>
                </div>
            </section>

            <section class="dashboard-panel dashboard-reports-panel">
                <div class="dashboard-panel-heading">
                    <div>
                        <span class="panel-kicker">ANÁLISIS</span>
                        <h4>Reportes del negocio</h4>
                        <p>Consulta la información que necesitas.</p>
                    </div>
                    <span class="panel-heading-icon">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                    </span>
                </div>

                <div class="dashboard-report-list">
                    <a href="{{ url('/products/all') }}" class="dashboard-report-item">
                        <span class="report-icon report-blue">
                            <i class="bi bi-box-seam"></i>
                        </span>
                        <span class="report-text">
                            <strong>Reporte de inventario</strong>
                            <small>Existencias por producto</small>
                        </span>
                        <i class="bi bi-chevron-right"></i>
                    </a>

                    <div class="dashboard-report-item report-pending">
                        <span class="report-icon report-red">
                            <i class="bi bi-graph-down-arrow"></i>
                        </span>
                        <span class="report-text">
                            <strong>Reporte de mermas</strong>
                            <small>Pesos y rendimiento</small>
                        </span>
                        <span class="report-status">Por integrar</span>
                    </div>

                    <div class="dashboard-report-item report-pending">
                        <span class="report-icon report-orange">
                            <i class="bi bi-truck"></i>
                        </span>
                        <span class="report-text">
                            <strong>Reporte de distribución</strong>
                            <small>Kilos asignados y disponibles</small>
                        </span>
                        <span class="report-status">Por integrar</span>
                    </div>

                    <div class="dashboard-report-item report-pending">
                        <span class="report-icon report-green">
                            <i class="bi bi-receipt"></i>
                        </span>
                        <span class="report-text">
                            <strong>Reporte de ventas</strong>
                            <small>Ingresos y productos vendidos</small>
                        </span>
                        <span class="report-status">Fase POS</span>
                    </div>
                </div>
            </section>

        </div>

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
@endsection
