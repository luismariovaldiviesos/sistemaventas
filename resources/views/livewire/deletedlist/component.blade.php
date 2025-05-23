<div>
    @can('ver_facturas_anuladas')
    <div class="intro-y col-span-12">

        <div class="intro-y box">

            <h2 class="text-lg font-medium text-center text-them-1 py-4">
                {{ $componentName }}
            </h2>

            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2 p-4">
                <p class="btn btn-primary shadow-md mr-2">facturas pendientes de procesar en el SRI {{ $deletedFacturas }}</p>
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
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">RUC</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">CLAVE ACCESO</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">AUTORIZACION</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">ANULADO SRI</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">FECHA ANULACION</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($facturas as $factura )
                                    <tr class=" dark:bg-dark-1 {{ $loop->index % 2> 0 ? 'bg-gray-200' : '' }}">

                                        <td class="text-center font-medium">{{ $factura->secuencial }}</td>
                                        <td class="text-center font-medium">{{ $factura->cliente}}</td>
                                        <td class="text-center font-medium">{{ $factura->ruc_cliente }}</td>
                                        <td class="text-center font-medium">{{ $factura->clave_acceso }}</td>
                                        <td class="text-center font-medium">{{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d-m-Y') }}</td>
                                        <td class="text-center font-medium">{{ $factura->estado }}</td>
                                        <td class="text-center font-medium">{{ $factura->created_at }}</td>

                                         <td class="dark:border-dark-5 text-center">
                                            <div class="d-flex justify-content-center">


                                                <!-- Botón para eliminar -->
                                                @if($factura->estado == 'pendiente')
                                                @can('procesar_factura_anulada')
                                                <button class="btn btn-danger text-white border-0 ml-3"
                                                        wire:click.prevent="deletesri({{ $factura->id }})"
                                                        type="button"
                                                        title="Eliminar Factura">
                                                    <i class="fas fa-trash-alt f-2x"></i>
                                                </button>
                                                @endcan
                                                @else
                                               <small class="text-info">Procesado</small>
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
        @else
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>¡Lo sentimos!</strong> No tienes permisos para ver esta sección.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endcan

        {{-- @include('livewire.$facturas.panel')
        @include('livewire.sales.keyboard') --}}


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
