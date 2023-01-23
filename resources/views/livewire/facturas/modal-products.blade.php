<div wire:ignore.self id="modalProduct" class="modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">
                    <b class="text-theme-1">Elegir Producto</b>
                </h2>
            </div>

            <div class="modal-body grid gap-4">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="p-5" id="striped-rows-table">
                            <div class="preview">
                                <div class="overflow-x-auto">

                                    <div class="input-group">
                                        <div id="input-group-3" class="input-group-text"><i class="fas fa-search"></i></div>
                                        <input wire:model="searchProduct" id="product-search" type="text" class="form-control form-control-lg kioskboard" placeholder="Buscar producto" >
                                    </div>


                                    <table class="table">
                                        <thead>
                                            <tr class="text-theme-6">
                                                <th class="border-b-2 dark:border-dark-5 whitespace-nowrap font-bold" width="80%">nombre</th>
                                               <th class="border-b-2 dark:border-dark-5 whitespace-nowrap font-bold">PVP</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($products as $product)
                                            <tr class="dark:bg-dark-1 text-lg {{$loop->index % 2 > 0 ? 'bg-gray-200' : ''}}">
                                                <td class="border-b dark:border-dark-5 ">
                                                    {{$product->name}}
                                                </td>
                                                <td class="border-b dark:border-dark-5 ">
                                                    {{ number_format($product->price2, 2) }}
                                                </td>
                                                <td>
                                                    <button wire:click="searchManualProduct({{ $product->id }})" class="btn btn-outline-primary">Seleccionar</button>
                                                </td>

                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center">NO HAY PRODUCTOS REGISTRADOS</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer text-right">
                <button onclick="closeModalProduct()" class="btn btn-outline-secondary mr-5">Cerrar Ventana</button>
            </div>

        </div>
    </div>
</div>
