<div wire:ignore.self id="modalnc" class="modal fade" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header border-b-2">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- <div class="modal-body p-6">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="invoice-container border p-6 rounded-lg shadow-lg bg-white">
                            <div class="preview">
                                <div class="overflow-x-auto">
                                    <header class="mb-6 border-b-2 pb-4">
                                        <h2 class="text-3xl font-bold text-gray-800">Factura Secuencial: {{ optional($factura_detalle)->secuencial ?? 'N/A' }}</h2>
                                        <p class="text-xl font-semibold text-gray-600">Cliente: {{ optional($factura_detalle)->customer->businame ?? 'N/A' }}</p>
                                        <p class="text-lg font-medium text-gray-500">Autorizado: {{ optional($factura_detalle)->fechaAutorizacion ?? 'N/A' }}</p>
                                    </header>
                                    <table class="table w-full border-separate border-spacing-0.5">
                                        <thead class="bg-gray-100">
                                            <tr class="text-left">
                                                <th class="whitespace-nowrap font-semibold p-2 border-b">Cantidad</th>
                                                <th class="whitespace-nowrap font-semibold p-2 border-b">Detalle</th>
                                                <th class="whitespace-nowrap font-semibold p-2 border-b">Precio Unitario</th>
                                                <th class="whitespace-nowrap font-semibold p-2 border-b">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($factura_detalle && $factura_detalle->detalles)
                                                @foreach($factura_detalle->detalles as $detalle)
                                                    <tr class="text-lg {{$loop->index % 2 > 0 ? 'bg-gray-50' : 'bg-white'}}">
                                                        <td class="border-b p-2 text-center">{{ $detalle->cantidad }}</td>
                                                        <td class="border-b p-2">{{ $detalle->descripcion }}</td>
                                                        <td class="border-b p-2 text-center">{{ number_format($detalle->precioUnitario, 2) }}</td>
                                                        <td class="border-b p-2 text-center">{{ number_format($detalle->total, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center py-2 text-red-600">No hay detalles disponibles para esta factura.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="mt-6 text-right">
                        <h3 class="text-xl font-bold text-gray-800">Descuento: {{ optional($factura_detalle)->descuento ?? '0.00' }}</h3>
                        <h3 class="text-2xl font-bold text-gray-800">Total: {{ number_format(optional($factura_detalle)->total ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div> --}}

            {{-- <div class="modal-footer text-right">
                <button onclick="closeModal()" class="btn btn-primary mr-5">Cerrar Ventana</button>
            </div> --}}

        </div>
    </div>
</div>
