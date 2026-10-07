@extends('master')

@section('custom_js')
    <script src="{{ url('/static/js/product.js?v=' . time()) }}"></script>
@endsection

@section('subRouter')products_{{ 'all' }}@endsection

@section('content')
    <div class="container-fluid">
        <div class="panel mtop16 sh">
            <div class="panel-header">
                <div class="inside">
                    <h5><i class="bi bi-box-seam"></i> Lista de productos</h5>
                </div>
                <ul class="mt-9">
                    <li>
                        <a href="{{ url('/product/add') }}">
                            <i class="bi bi-cart-plus"></i> Agregar Producto
                        </a>
                    </li>
                    <li>
                        <a href="#">Filtrar <i class="bi bi-arrow-down-short"></i></a>
                        <ul class="shadow">
                            <li><a href="{{ url('/products/1') }}"><i class="fas fa-globe-americas"></i> Públicos</a></li>
                            <li><a href="{{ url('/products/0') }}"><i class="fas fa-eraser" ></i> Borradores</a></li>
                            <li><a href="{{ url('/products/trash') }}"><i class="fas fa-trash"></i> Papelera</a></li>
                            <li><a href="{{ url('/products/all') }}"><i class="fas fa-list-ul"></i> Todos</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" id="btn_search">
                            <i class="bi bi-search"></i> Buscar
                        </a>
                    </li>
                </ul>
            </div>
            <div class="row mtop16">
                <div class="inside">
                    <div class="form_search" id="form_search">
                        <form action="{{ url('/products/search') }}" class="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="search" placeholder="Ingrese su busqueda" required>
                                </div>
                                <div class="col-md-4">
                                    <select name="filter" class="form-select">
                                        <option value="0">Nombre del producto</option>
                                        <option value="1">Código</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select">
                                        <option value="0">Borrador</option>
                                        <option value="1">Públicos</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
           <div class="panel-body">
               <div class="inside">
                    <table class="table">
                        <thead>
                            <tr>
                                <td><strong>ID</strong></td>
                                <td></td>
                                <td><strong>Nombre</strong></td>
                                <td><strong>Precio Min</strong></td>
                                <td><strong>Inventario</strong></td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $key)
                                <tr>
                                    <td width="50">{{ $key->id }}</td>
                                    <td width="150"><img src="{{ getFileUrl($key->image, '256') }}" width="50" height="50"></td>
                                    <td>{{ $key->name }}</td>
                                    <td>{{ config('intecmex.currency') }} {{ $key->price }}</td>
                                    <td></td>
                                    <td width="180">
                                        <div class="opts">
                                            <a href="{{ url('product/'.$key->id.'/edit') }}" data-toggle="tooltip" data-placement="top" title="Editar" class="edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="{{ url('product/'.$key->id.'/edit') }}" data-toggle="tooltip" data-placement="top" title="Inventario" class="inventory">
                                                <i class="bi bi-file-earmark-arrow-up"></i>
                                            </a>
                                            @if(is_null($key->delete))
                                                <a href="{{ url('/product/' . $key->id . '/delete') }}" data-toggle="tooltip" data-placement="top" title="Eliminar" class="btn-deleted deleted" data-action="delete" data-path="api-js/product" data-object="{{ $key->id }}">
                                                    <i class="bi bi-trash2-fill"></i>
                                                </a>
                                            @else
                                                <a href="{{ url('/product/' . $key->id . '/restore') }}" data-toggle="tooltip" data-placement="top" title="Restaurar" class="btn-restored restored" data-action="restored" data-path="api-js/product" data-object="{{ $key->id }}">
                                                    <i class="bi bi-trash2-fill"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
               </div>
           </div>
        </div>
    </div>
@endsection

