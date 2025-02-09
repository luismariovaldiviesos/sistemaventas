<div wire:ignore.self id="modalValorInicio" class="modal" data-backdrop="static" tabindex="-1">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">
                    <b class="text-theme-1"> INGRESE EL VALOR INICIAL</b>
                </h2>
            </div>

            <div class="modal-body grid gap-4">
                <div class="row">
                    <div class="col-sm-12">
                        <b>EFECTIVO:</b>
                        <input type="numbre" id="valorInicio"  class="form-control kioskboard">
                    </div>
                </div>
            </div>

            <div class="modal-footer text-right">
                <button onclick="closeModal()" class="btn btn-outline-secondary mr-5">Cerrar Ventana</button>
                <button type="button" wire:click.prevent="Abrir(document.getElementById('valorInicio').value)"
                class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>

</div>
