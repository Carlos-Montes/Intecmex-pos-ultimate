@extends('master')

@section('custom_js')
    <script src="{{ url('/static/js/units.js?v=' . time()) }}"></script>
@endsection

@section('subRouter')products_{{ $type }}@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="panel mtop16 sh">
                <div class="panel-header">
                    <div class="inside">
                        <h5><i class="bi bi-tag-fill"></i> Agregar Unidad</h5>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="inside">
                        <form action="/" method="post" autocomplete="off" files="true" class="form" id="form_unit_add">
                            <input type="hidden" name="autocomplete" class="autocomplete">
                            <label for="name">Nombre: </label>
                            <input type="text" name="name" class="disableac" value="{{ old('name') }}">
                            <label for="nomenclatura" class="mtop16">Nomenclatura:</label>
                            <input type="text" name="nomenclatura" class="disableac" value="{{ old('nomenglatura') }}">
                            <button type="submit" class="btn btn-success mtop16 w-100" > Guardar </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="panel mtop16 sh">
                <div class="panel-header">
                    <div class="inside">
                        <h5><i class="bi bi-list-ol"></i> Lista de unidades</h5>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="inside">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td>#</td>
                                    <td>Nombre</td>
                                    <td>Estado</td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listUnits as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td><span @if($item->status == 1) class="table-active" @else class="table-inactive" @endif>{{ getActive($item->status) }}</span></td>
                                    <td class="form-medium">
                                        <div class="opts">
                                            <a href="/" data-toggle="tooltip" data-placement="top" title="Editar" class="edit" data-bs-toggle="modal" data-bs-target="#units-{{ $item->id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <a href="{{ url('/units/' . $item->id . '/delete') }}" data-toggle="tooltip" data-placement="top" title="Eliminar" class="btn-deleted deleted" data-action="delete" data-path="api-js/units" data-object="{{ $item->id }}">
                                                <i class="bi bi-trash2-fill"></i>
                                            </a>
                                            
                                        </div>
                                    </td>
                                </tr>
                                @include('units.components.modal-edit-units', [$item->id, $item->name, $item->status])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


