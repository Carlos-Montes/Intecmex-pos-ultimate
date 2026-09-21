@extends('master')

@section('custom_js')
    <script src="{{ url('/static/js/product.js?v=' . time()) }}"></script>
@endsection

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
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
               </div>
           </div>
        </div>
    </div>
@endsection
