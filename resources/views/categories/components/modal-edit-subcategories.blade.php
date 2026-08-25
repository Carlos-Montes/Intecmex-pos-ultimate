<div class="modal fade" id="subcategories-{{ $item->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Editar categoria {{ $item->name }}</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ url('/api-js/categorie/'.$item->id.'/edit') }}" method="post" autocomplete="off" files="true" class="form">
      <div class="modal-body">
        @csrf
        <input type="hidden" name="autocomplete" class="autocomplete">
        <label for="name">Nombre: </label>
        <input type="text" name="name" class="disableac form-control" value="{{ $item->name }}">
        <label for="status" class="mtop16">Estado:</label>
        <select name="status" id="status" class="form-select">
            <option value="1" @selected($item->status == 'Activo')>
                Activo
            </option>
            <option value="0" @selected($item->status == 'Inactivo')>
                Inactivo
            </option>
        </select>
        <label for="icon" class="mtop16"> Icono: </label>
        <input type="file" name="icon" id="icon" class="form-control" accept="image/*" >
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Actualizar</button>
      </div>
      </form>
    </div>
  </div>
</div>