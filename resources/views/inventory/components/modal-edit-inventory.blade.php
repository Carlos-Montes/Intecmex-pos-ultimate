<div class="modal fade" id="modal-edit-weight" tabindex="-1"aria-labelledby="modal-edit-weight-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="form-edit-weight">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-edit-weight-title"> Editar peso vivo </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-weight-id" name="id">
                    <input type="hidden" id="edit-weight-type" name="category">
                    <div class="mb-3">
                        <label class="form-label">Animal</label>
                        <input type="text" id="edit-weight-animal" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit-weight-value" class="form-label"> Peso vivo (Kg) </label>
                        <input type="number" id="edit-weight-value" name="weight" class="form-control" min="0.01" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> Cancelar </button>
                    <button type="submit" class="btn btn-primary">  Guardar cambios </button>
                </div>
            </form>
        </div>
    </div>
</div>
