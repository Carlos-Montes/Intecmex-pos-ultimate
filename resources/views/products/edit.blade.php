@extends('master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-9">
                <div class="panel mtop16 sh">
                    <div class="panel-header">
                        <div class="inside">
                            <h5><i class="bi bi-boxes"></i> Editando Producto - {{ $p->name }}</h5>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="inside">
                            <form action="{{ url('/api-js/product/edit') }}" method="post" class="form" autocomplete="off" files="true" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="autocomplete">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="name">Nombre del producto: </label>
                                        <input type="text" name="name" class="disableac" value="{{ $p->name }}"  required>
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
                                        <input type="hidden" name="subcategory_actual" value="{{ $p->subcategory_id }}" id="subcategory_actual">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="subcategory">Subcategoría</label>
                                        <select name="subcategory" class="form-select" id="subcategory" required></select>
                                    </div>
                                </div>
                                <div class="row mtop16">
                                    <div class="col-md-4">
                                        <label for="indiscount">¿En Descuento?:</label>
                                        <select name="indiscount" class="form-select">
                                            <option value="0" selected> No</option>
                                            <option value="1"> Si</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="discount">Descuento</label>
                                        <input type="number" name="discount" min="0.00" value="{{ $p->discount }}" step="any" placeholder="0.0" class="disableac">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="image">Fecha limite de descuento</label>
                                        <input type="date" name="discount_until_date" value="{{ $p->discount_until_date }}">
                                    </div>
                                </div>
                                <div class="row mtop16">
                                    <div class="col-md-3">
                                        <label for="code">Codígo de sistema</label>
                                        <input type="text" name="code" value="{{ $p->code }}" class="disableac" required>
                                    </div>
                                </div>
                                <div class="row mtop16">
                                    <div class="col-md-12">
                                        <label for="description">Descripción</label>
                                        <textarea name="content" id="editor" class="form-control disableac" value="{{ $p->content }}" required></textarea>
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
            <div class="col-md-3">
                <div class="panel mtop16 sh">
                    <div class="panel-header">
                        <div class="inside">
                            <h5><i class="bi bi-card-image"></i> Imagen Destacada</h5>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="inside">
                            <img src="{{ getFileUrl($p->image, '256') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection