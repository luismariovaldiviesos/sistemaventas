<div wire:ignore.self id="modalDetalles" class="modal fade" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body grid gap-4">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="p-5" id="invoice-details">
                            <div class="preview">
                                <div class="overflow-x-auto">
                                    <header class="mb-4">
                                        <h2 class="text-2xl font-bold text-red-600">Factura Secuencial: {{ $factura->secuencial }}</h2>
                                        <p class="text-lg font-semibold text-gray-700">Cliente: {{ $factura->customer->businame }}</p>

                                      </header>
                                      <table class="table w-full">

                                        <thead>
                                            <tr class="text-theme-6 border-b-2 dark:border-dark-5">
                                                <th class="whitespace-nowrap font-bold p-2">Cantidad</th>
                                                <th class="whitespace-nowrap font-bold p-2">Detalle</th>
                                                <th class="whitespace-nowrap font-bold p-2">Precio Unitario</th>
                                                <th class="whitespace-nowrap font-bold p-2">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                             @foreach($factura->detalles  as $detalle)
                                            <tr class="dark:bg-dark-1 text-lg {{$loop->index % 2 > 0 ? 'bg-gray-200' : ''}}">
                                                <td class="border-b dark:border-dark-5 ">
                                                    {{ $detalle->cantidad }}
                                                </td>
                                                <td>
                                                    {{ $detalle->descripcion }}
                                                </td>
                                                <td>
                                                    {{ $detalle->precioUnitario }}
                                                </td>
                                                <td>
                                                    {{ $detalle->total }}
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
            </div>

            <div class="modal-footer text-right">
                <button onclick="closeModal()" class="btn btn-primary mr-5">Cerrar Ventana</button>
                {{-- <button class="btn btn-warning text-white border-0 ml-3"
                wire:click="Pagar()"
                type="button">
                   Cancelar Saldos
                </button> --}}

            </div>

        </div>
    </div>
</div>


