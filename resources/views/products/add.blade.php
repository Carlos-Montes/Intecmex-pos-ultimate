@extends('master')

@section('custom_js')
    <script src="{{ url('/static/js/product.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="panel mtop16 sh">
            <div class="panel-header">
                <div class="inside">
                    <h5><i class="bi bi-boxes"></i> Agregar Producto</h5>
                </div>
            </div>
            <div class="panel-body">
                <div class="inside">
                    <form action="{{ url('/api-js/product/add') }}" method="post" class="form" autocomplete="off" files="true" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="autocomplete">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="name">Nombre del producto: </label>
                                <input type="text" name="name" class="disableac" value="{{ old('name') }}"  required>
                            </div>
                        </div>
                        <div class="row mtop16">
                            <div class="col-md-6">
                                <label for="category">Categoría</label>
                                <select name="category" class="form-select" id="category">
                                    @foreach($cats as $value => $label)
                                        <option value="{{ $value }}" {{ $value == 0 ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="subcategory_actual" value="0" id="subcategory_actual">
                            </div>
                            <div class="col-md-6">
                                <label for="subcategory">Subcategoría</label>
                                <select name="subcategory" class="form-select" id="subcategory" required></select>
                            </div>
                        </div>
                        <div class="row mtop16">
                            <div class="col-md-3">
                                <label for="indiscount">¿En Descuento?:</label>
                                <select name="indiscount" class="form-select">
                                    <option value="0" selected> No</option>
                                    <option value="1"> Si</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="discount">Descuento</label>
                                <input type="number" name="discount" min="0.00" step="any" placeholder="0.0" class="disableac">
                            </div>
                            <div class="col-md-3">
                                <label for="code">Codígo de sistema</label>
                                <input type="text" name="code" value="{{ old('code') }}" class="disableac" required>
                            </div>
                            <div class="col-md-3">
                                <label for="image">Imagen Destacada</label>
                                <input type="file" name="icon" id="icon" class="form-control" accept="image/*" >
                            </div>
                        </div>
                        <div class="row mtop16">
                            <div class="col-md-12">
                                <label for="description">Descripción</label>
                                <textarea name="content" id="editor" class="form-control disableac" value="{{ old('content') }}" required></textarea>
                            </div>
                        </div>
                        <div class="row mtop16 d-flex justify-content-end">
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection