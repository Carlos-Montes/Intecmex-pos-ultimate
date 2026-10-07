@extends('master')

@section('subRouter')products_{{ $type }}@endsection

@section('custom_js')
    <script src="{{ url('/static/js/printer_labels.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="panel mtop16 sh">
            <div class="panel-header">
                <div class="inside">
                    <h5><i class="bi bi-printer"></i> Impresión de etiquetas</h5>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="panel mtop16 sh">
                    <div class="panel-header">
                        <div class="inside">
                            <h5><i class="bi bi-gear"></i> Configuración de etiqueta</h5>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="inside">
                            <form action="/" method="post" autocomplete="off" files="true" class="form" id="form_category_add">
                                <label for="code">Código del producto</label>
                                <input type="text" id="code_products" placeholder="Escanea el código o escribe el código..." autocomplete="off">
                                <label for="product" class="mtop16">Nombre del producto</label>
                                <input type="text" id="products_name" autocomplete="off">
                                <label for="no_labels" class="mtop16">No. Etiquetas</label>
                                <input type="number" id="no_labels" autocomplete="off">
                                <button type="button" id="btn_preview" class="btn btn-success mtop16 w-100">Generar vista previa</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="panel mtop16 sh">
                    <div class="panel-header">
                        <div class="inside">
                            <h5> <i class="bi bi-eye"></i>Vista Previa</h5>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="inside">
                            <!-- ÁREA DE PREVISUALIZACIÓN -->
                            <div class="labels-preview">
                                <!-- HOJA -->
                                <div class="labels-page-preview" id="labelsPagePreview" data-company="{{ config('intecmex.app_name') }}">
                                    <!-- AQUÍ SE GENERARÁN LAS ETIQUETAS -->
                                    <div class="label-preview">
                                        <div class="label-company">
                                            {{ config('intecmex.app_name') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="print-labels">
                                <button type="button" id="print-labels" class="btn btn-printer-labels">Imprimir etiquetas</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection

