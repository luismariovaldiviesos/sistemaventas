<div>
    @can('ver_facturas_emitidas')
    <div class="intro-y col-span-12">

        <div class="intro-y box">

            <h2 class="text-lg font-medium text-center text-them-1 py-4">
                {{ $componentName }}
            </h2>

            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2 p-4">
                {{-- <button onclick="openPanel('add')" class="btn btn-primary shadow-md mr-2">Agregar</button> --}}
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
                                        <td class="text-center font-medium">{{  $factura->numeroAutorizacion }}</td>
                                        <td class="text-center font-medium">{{ \Carbon\Carbon::parse($factura->fechaAutorizacion)->format('d-m-Y') }}</td>

                                         <td class="dark:border-dark-5 text-center">
                                            <div class="d-flex justify-content-center">
                                                <!-- Botón para detalles    -->
                                                @can('ver_detalle_factura_emitida')
                                                <button class="btn btn-dark text-white border-0 ml-3"
                                                        wire:click.prevent="show({{ $factura->id }})"
                                                        type="button"
                                                        title="Detalles">
                                                    <i class="fas fa-eye
                                                    f-2x"></i>
                                                </button>
                                                @endcan
                                                <!-- Botón para reenviar PDF -->
                                                @can('reenviar_pdf_factura_emitida')
                                                 <button class="btn btn-warning text-white border-0 ml-3"
                                                        wire:click.prevent="retry({{ $factura->id }})"
                                                        type="button"
                                                        title="Reenviar PDF">
                                                    <i class="fas fa-file-pdf f-2x"></i>
                                                    </button>
                                                @endcan

                                                <!-- Botón para descargar archivos -->
                                                @can('descargar_archivo_factura_emitida')
                                                <button class="btn btn-primary text-white border-0 ml-3"
                                                wire:click.prevent="downloadFiles({{ $factura->id }})"
                                                type="button"
                                                title="Descargar Archivos">
                                                <i class="fas fa-download f-2x text-white"></i>
                                                </button>
                                                @endcan

                                            @php

                                                $fechaEmision = \Carbon\Carbon::parse($factura->fechaAutorizacion); // Convertir a Carbon
                                                $fechaLimite = $fechaEmision->addDays($annulmentDays);
                                            @endphp
                                            @if (now()->lessThanOrEqualTo($fechaLimite))
                                                <!-- Botón para anular factura -->
                                                @can('anular_factura_emitida')
                                                    <button class="btn btn-danger text-white border-0 ml-3"
                                                    wire:click="confirmDelete({{ $factura->id }})"
                                                    type="button"
                                                    title="Anular Factura">
                                                <i class="fas fa-trash-alt f-2x"></i>
                                                </button>
                                                @endcan
                                            @else
                                             <!-- Botón para emitir nota de crédito -->
                                             @can('crear_nota_credito')
                                            <button class="btn btn-warning text-white border-0 ml-3"
                                                wire:click="confirmNC({{ $factura->id }})"
                                                type="button"
                                                title="Emitir Nota de Crédito">
                                                <i class="fas fa-file-invoice-dollar f-2x"></i>
                                            </button>
                                            @endcan
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
            @include('livewire.listadofacturas.modaldetalles')
        </div>

        @else
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>¡Lo sentimos!</strong> No tienes permisos para ver esta sección.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endcan

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
        // function openPanel(action = ''){
        //     if(action == 'add'){
        //         @this.resetUI()
        //     }
        //     var modal = document.getElementById('panelProduct')
        //     modal.classList.add('overflow-y-auto','show')
        //     modal.style.cssText="margin-top: 0px; margin-left: 0px; padding-left: 17px; z-index: 100"

        // }

        window.addEventListener('show_factura', event => {

            openModalDetalle()
        })

        function openModalDetalle() {
                var modal = document.getElementById("modalDetalles")
                modal.classList.add("overflow-y-auto", "show")
                modal.style.cssText = "margin-top: 0px; margin-left: -100px;  z-index: 1000;"
        }

        function closeModal() {
                var modal = document.getElementById("modalDetalles")
                modal.classList.remove("overflow-y-auto", "show")
                modal.style.cssText = ""
            }


    </script>



</div>
