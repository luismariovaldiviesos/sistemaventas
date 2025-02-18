<div>
    <div class="intro-y col-span-12">

        <div class="intro-y box">

            <h2 class="text-lg font-medium text-center text-them-1 py-4">
                {{ $componentName }}
            </h2>

            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2 p-4">
                <button onclick="openPanel('add')" class="btn btn-primary shadow-md mr-2">Agregar</button>
                <div class="hidden md:block mx-auto text-gray-600">
                    --
                </div>

                <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                    <div class="w-56 relative text-gray-700 dark:text-gray-300 ">
                        <input wire:model='search' id="search" class="form-control w-56 box pr-10  placeholder-theme-13 kioskboard" type="text" placeholder="buscar...">
                        <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0 fas fa-search"></i>
                    </div>
                </div>
            </div>



            <div class="p-5">
                <div class="preview">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr class="text-theme-1">
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">SECUENCIAL</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">CLIENTE</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">AUTORIZACION</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">FECHA AUTORIZACION</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($facturas as $factura )
                                    <tr class=" dark:bg-dark-1 {{ $loop->index % 2> 0 ? 'bg-gray-200' : '' }}">

                                        <td class="text-center font-medium">{{ $factura->secuencial }}</td>
                                        <td class="text-center font-medium">{{ $factura->customer->businame}}</td>
                                        <td class="text-center font-medium">{{ $factura->numeroAutorizacion }}</td>
                                        <td class="text-center font-medium">{{ $factura->fechaAutorizacion }}</td>

                                         <td class="dark:border-dark-5 text-center">
                                            <div class="d-flex justify-content-center">
                                                <!-- Botón para detalles    -->
                                                <button class="btn btn-dark text-white border-0 ml-3"
                                                        wire:click.prevent="show({{ $factura->id }})"
                                                        type="button"
                                                        title="Detalles">
                                                    <i class="fas fa-eye
                                                    f-2x"></i>
                                                </button>

                                                 <button class="btn btn-warning text-white border-0 ml-3"
                                                        wire:click.prevent="retry({{ $factura->id }})"
                                                        type="button"
                                                        title="Reenviar PDF">
                                                    <i class="fas fa-file-pdf f-2x"></i>
                                                    </button>

                                                <!-- Botón para descargar archivos -->
                                                <button class="btn btn-primary text-white border-0 ml-3"
                                                wire:click.prevent="downloadFiles({{ $factura->id }})"
                                                type="button"
                                                title="Descargar Archivos">
                                            <i class="fas fa-download f-2x text-white"></i>
                                            </button>

                                            @php

                                                $fechaEmision = \Carbon\Carbon::parse($factura->fechaAutorizacion); // Convertir a Carbon
                                                $fechaLimite = $fechaEmision->addDays($annulmentDays);
                                            @endphp
                                            @if (now()->lessThanOrEqualTo($fechaLimite))
                                                <!-- Botón para anular factura -->
                                                <button class="btn btn-danger text-white border-0 ml-3"
                                                wire:click="confirmDelete({{ $factura->id }})"
                                                type="button"
                                                title="Anular Factura">
                                            <i class="fas fa-trash-alt f-2x"></i>
                                        </button>
                                            @else
                                             <!-- Botón para emitir nota de crédito -->
                                            <button class="btn btn-warning text-white border-0 ml-3"
                                                wire:click="confirmNC({{ $factura->id }})"
                                                type="button"
                                                title="Emitir Nota de Crédito">
                                                <i class="fas fa-file-invoice-dollar f-2x"></i>
                                            </button>
                                            @endif

                                      </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-gray-200 dark:bg-dark-1">
                                        <td colspan="2">
                                            <h6 class="text-center">    NO HAY FACTURAS </h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $facturas->links() }}
                    </div>
                </div>
            </div>

            <div class="col-spam-12 p-5">
                {{-- {{ $$facturas->links() }} --}}
            </div>
        </div>
    </div>


        {{-- @include('livewire.$facturas.panel')
        @include('livewire.sales.keyboard') --}}


        <script>
            window.addEventListener('swal:confirm', event => {
                Swal.fire({
                    title: '¿Estás seguro de anular la factura?',
                    text: "Esta acción no se puede deshacer.",
                    type: 'warning',  // Si tu versión no soporta 'icon', usa 'type'
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#FFC107',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(function (result) {
                    if (result.value) {  // En versiones antiguas de SweetAlert2, se usa 'value' en vez de 'isConfirmed'
                       // console.log("Emitir evento Livewire: delete, con ID:", event.detail.facturaId);
                        Livewire.emit('delete', event.detail.facturaId);
                    }
                });
            });



            window.addEventListener('swal:nc', event => {
                Swal.fire({
                    title: '¿Estás seguro de emitir la nota de credito?',
                    text: "Esta acción no se puede deshacer.",
                    type: 'warning',  // Si tu versión no soporta 'icon', usa 'type'
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#FFC107',
                    confirmButtonText: 'Sí, emitir NC',
                    cancelButtonText: 'Cancelar'
                }).then(function (result) {
                    if (result.value) {  // En versiones antiguas de SweetAlert2, se usa 'value' en vez de 'isConfirmed'
                       // console.log("Emitir evento Livewire: delete, con ID:", event.detail.facturaId);
                        Livewire.emit('nc', event.detail.facturaId);
                    }
                });
            });





        </script>



    {{-- para el buscador  --}}
    <script>
        const inputSearch = document.getElementById('search')
        inputSearch.addEventListener('change', (e) => {
            @this.search = e.target.value
        })

        // abrir modal
        function openPanel(action = ''){
            if(action == 'add'){
                @this.resetUI()
            }
            var modal = document.getElementById('panelProduct')
            modal.classList.add('overflow-y-auto','show')
            modal.style.cssText="margin-top: 0px; margin-left: 0px; padding-left: 17px; z-index: 100"

        }

        //cerrar modal
        function closePanel(action = ''){

            var modal = document.getElementById('panelProduct')
            modal.classList.add('overflow-y-auto','show')
            modal.style.cssText=""

        }

        window.addEventListener('open-modal', event => {
            openPanel()
        })

        window.addEventListener('noty', event => {
            if (event.detail.action == 'close-modal')  closePanel()

        })

        // kioskBoard.run('.kioskboard', {})
    </script>

    <script>
        document.querySelectorAll(".kioskboard").forEach(i => i.addEventListener("change", e => {
            switch (e.currentTarget.id)
            {
                case 'name':
                    @this.name = e.target.value
                    break
                case 'cost':
                    @this.cost = e.target.value
                    break
                case 'code':
                    @this.code = e.target.value
                    break
                case 'price':
                    @this.price = e.target.value
                    break
                case 'price2':
                    @this.price2 = e.target.value
                    break
                case 'pvp':
                    @this.pvp = e.target.value
                    break
                case 'descuento':
                    @this.descuento = e.target.value
                    break
                case 'stock':
                    @this.stock = e.target.value
                    break
                case 'minstock':
                    @this.minstock = e.target.value
                    break
                    case 'ivaporcentaje':
                    @this.ivaporcentaje = e.target.value
                    break
                    case 'iceporcentaje':
                    @this.iceporcentaje = e.target.value
                    break
                    case 'iva':
                    @this.iva = e.target.value
                    break
                    case 'ice':
                    @this.ice = e.target.value
                    break


            }
        }))
    </script>

</div>
