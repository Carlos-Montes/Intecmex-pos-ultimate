@extends('master')

@section('custom_js')
    <script src="{{ url('/static/js/finances.js?v=' . time()) }}"></script>
@endsection

@section('content')

    @if($type == 1)
    <div class="row">

        <div class="col-md-4">
            <div class="panel mtop16 sh">
                <div class="panel-header">
                    <div class="inside">
                        <h5><i class="bi bi-tag-fill"></i> Agregar Cuenta Contable</h5>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="inside">
                        <form action="/" method="post" autocomplete="off" class="form" id="form_account_add">
                            <label for="account_number">Número de Cuenta:</label>
                            <input type="text" name="account_number" placeholder="Ej. 1100-001">

                            <label for="name" class="mtop16">Nombre:</label>
                            <input type="text" name="name" placeholder="Ej. Caja General">

                            <label for="balance" class="mtop16">Balance:</label>
                            <input type="number" name="balance" step="0.01" placeholder="0.00">

                            <label for="status" class="mtop16">Estatus:</label>
                            <select name="status" class="form-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>

                            <button type="submit" class="mtop16">Guardar</button>
                            <button type="button" class="btn btn-secondary mtop16 w-100 hide" id="btn_account_cancel">Cancelar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="panel mtop16 sh">
                <div class="panel-header">
                    <div class="inside">
                        <h5><i class="bi bi-list-ol"></i> Lista de Cuentas Contables</h5>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="inside">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td>Número de Cuenta</td>
                                    <td>Nombre</td>
                                    <td>Balance</td>
                                    <td>Estatus</td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($accounts as $account)
                                <tr>
                                    <td>{{ $account->account_number }}</td>
                                    <td>{{ $account->name }}</td>
                                    <td>${{ number_format((float)$account->balance, 2) }}</td>
                                    <td>
                                        @if($account->status == 1)
                                            <span class="table-active">Activo</span>
                                        @else
                                            <span class="table-inactive">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="form-medium">
                                        <div class="opts">
                                            <a href="#" class="edit btn-edit-account"
                                               data-id="{{ $account->id }}"
                                               data-account_number="{{ $account->account_number }}"
                                               data-name="{{ $account->name }}"
                                               data-balance="{{ $account->balance }}"
                                               data-status="{{ $account->status }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="#" class="deleted btn-deleted" data-action="delete" data-path="api-js/account" data-object="{{ $account->id }}">
                                                <i class="bi bi-trash2-fill"></i>
                                            </a>
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

    </div>
    @endif

@endsection
