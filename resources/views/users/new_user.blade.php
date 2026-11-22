@extends('master')

@section('subRouter')users_{{ 'all' }}@endsection

@section('custom_js')
    <script src="{{ url('/static/js/users.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="panel sh mtop16">
        <div class="panel-header">
            <div class="inside">
                <h5>Registrar nuevo usuario</h5>
            </div>
        </div>

        <div class="panel-body">
            <div class="inside">
                <form action="#" class="form" autocomplete="off" id="form-new-user">
                    <input type="hidden" name="autocomplete">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="type_user">Selecciona un tipo de usuario</label>
                            <select name="type_user" id="type_user" class="form-select">
                                @foreach (getUserRole() as $item => $value)
                                    <option value="{{ $item }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- CAMPOS ADMINISTRADOR Y VENDEDOR --}}
                    <div id="access-user" class="user-fields">
                        <div class="row">
                            <div class="col-md-6 mtop16">
                                <label for="name">Nombre completo</label>
                                <input type="text" name="name" id="name" class="disableac">
                            </div>

                            <div class="col-md-6 mtop16">
                                <label for="email">Correo electrónico</label>
                                <input type="email" name="email" id="email" class="disableac">
                            </div>

                            <div class="col-md-6 mtop16">
                                <label for="phone">Teléfono</label>
                                <input type="text" name="phone" id="phone" class="disableac">
                            </div>

                            <div class="col-md-6 mtop16">
                                <label for="gender">Género</label>
                                <select name="gender" id="gender" class="form-select">
                                    @foreach (gender() as $item => $key)
                                        <option value="{{ $item }}">{{ $key }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mtop16">
                                <label for="password">Contraseña</label>
                                <input type="password" name="password" id="password" class="disableac">
                            </div>

                            <div class="col-md-6 mtop16">
                                <label for="password_confirmation">Confirmar contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="disableac">
                            </div>
                        </div>
                    </div>

                    {{-- CAMPOS CLIENTE --}}
                    <div id="customer-user" class="user-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6 mtop16">
                                <label for="customer_name">Nombre o razón social</label>
                                <input type="text" name="customer_name" id="customer_name" class="disableac">
                            </div>

                            <div class="col-md-6 mtop16">
                                <label for="customer_phone">Teléfono</label>
                                <input type="text" name="customer_phone" id="customer_phone" class="disableac">
                            </div>

                            <div class="col-md-6 mtop16">
                                <label for="customer_email">Correo electrónico</label>
                                <input type="email" name="customer_email" id="customer_email" class="disableac">
                            </div>

                            <div class="col-md-6 mtop16">
                                <label for="customer_discount">Descuento fijo por venta ($)</label>
                                <input type="number" name="customer_discount" id="customer_discount" min="0" step="0.01" placeholder="Ej. 100.00" class="disableac">
                            </div>
                        </div>
                    </div>

                    {{-- CAMPOS PROVEEDOR --}}
                    <div id="supplier-user" class="user-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6 mtop16">
                                <label for="supplier_name">Nombre o razón social</label>
                                <input type="text" name="supplier_name" id="supplier_name" class="disableac">
                            </div>

                            <div class="col-md-6 mtop16">
                                <label for="supplier_phone">Teléfono</label>
                                <input type="number" name="supplier_phone" id="supplier_phone" class="disableac">
                            </div>

                            <div class="col-md-6 mtop16">
                                <label for="supplier_email">Correo electrónico</label>
                                <input type="email" name="supplier_email" id="supplier_email" class="disableac">
                            </div>
                        </div>
                    </div>

                    <div class="row mtop16">
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                Registrar
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
