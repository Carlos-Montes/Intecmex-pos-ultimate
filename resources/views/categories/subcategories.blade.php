@extends('master')

@section('custom_js')
    <script src="{{ url('/static/js/category.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel mtop16 sh">
                <div class="panel-header">
                    <div class="inside">
                        <h5><i class="bi bi-list-ol"></i> Lista de Subcategoria</h5>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="inside">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td>Imagen</td>
                                    <td>Nombre</td>
                                    <td>Estatus</td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subcategories as $item)
                                <tr>
                                    <td><img src="{{ getFileUrl($item->icon, '64') }}" width="50" height="50"></td>
                                    <td>{{ $item->name }}</td>
                                    <td><span @if($item->status == 1) class="table-active" @else class="table-inactive" @endif>{{ getActive($item->status) }}</span></td>
                                    <td class="form-medium">
                                        <div class="opts">
                                            <a href="/" data-toggle="tooltip" data-placement="top" title="Editar" class="edit" data-bs-toggle="modal" data-bs-target="#subcategories-{{ $item->id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="{{ url('/category/' . $item->id . '/delete') }}" data-toggle="tooltip" data-placement="top" title="Eliminar" class="btn-deleted deleted" data-action="delete" data-path="api-js/subcategorie" data-object="{{ $item->id }}">
                                                <i class="bi bi-trash2-fill"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @include('categories.components.modal-edit-subcategories', [$item->id, $item->name, $item->icon, $item->status])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
