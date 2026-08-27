@extends('master')

@section('custom_js')
    <script src="{{ url('/static/js/category.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="panel mtop16 sh">
                <div class="panel-header">
                    <div class="inside">
                        <h5><i class="bi bi-tag-fill"></i> Agregar Categoria</h5>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="inside">
                        <form action="/" method="post" autocomplete="off" files="true" class="form" id="form_category_add">
                            <input type="hidden" name="autocomplete" class="autocomplete">
                            <label for="name">Nombre: </label>
                            <input type="text" name="name" class="disableac" value="{{ old('name') }}">
                            <label for="parent_id" class="mtop16">Categoría padre:</label>
                            <select name="parent_id" id="parent_id" class="form-select">
                                <option value="0"> Sin categoría padre </option>
                                @foreach ($cats as $cat)
                                    <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }} >
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>  
                            <label for="icon" class="mtop16"> Icono: </label>
                            <input type="file" name="icon" id="icon" class="form-control" accept="image/*" >
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
                        <h5><i class="bi bi-list-ol"></i> Lista de categoria</h5>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="inside">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td>Imagen</td>
                                    <td>Nombre</td>
                                    <td>Estado</td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listCategories as $item)
                                <tr>
                                    <td><img src="{{ getFileUrl($item->icon, '64') }}" width="50" height="50"></td>
                                    <td>{{ $item->name }}</td>
                                    <td><span @if($item->status == 1) class="table-active" @else class="table-inactive" @endif>{{ getActive($item->status) }}</span></td>
                                    <td class="form-medium">
                                        <div class="opts">
                                            <a href="/" data-toggle="tooltip" data-placement="top" title="Editar" class="edit" data-bs-toggle="modal" data-bs-target="#categories-{{ $item->id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <a href="{{ url('/categories/' . $item->id . '/subs') }}" data-toggle="tooltip" data-placement="top" title="Subcategorías" class="inventory">
                                                <i class="bi bi-subtract"></i> 
                                            </a>

                                            @if($item->parent_id != 0 || $item->children->count() == 0)
                                            <a href="{{ url('/categories/' . $item->id . '/delete') }}" data-toggle="tooltip" data-placement="top" title="Eliminar" class="btn-deleted deleted" data-action="delete" data-path="api-js/categorie" data-object="{{ $cat->id }}">
                                                <i class="bi bi-trash2-fill"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @include('categories.components.modal-edit-categories', [$item->id, $item->name, $item->icon, $item->status])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


