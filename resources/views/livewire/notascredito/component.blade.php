<div>
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
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">SRI</th>
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

                                        <td class="text-center font-medium">
                                            @if (!$factura->deleted_at)
                                                <button class="btn btn-warning text-white border-0 ml-3"
                                                    wire:click.prevent="setNC({{ $factura->id }})"
                                                    type="button"
                                                    title="Marcar NC generada">
                                                    <i class="fas fa-edit f-2x"></i> Marcar NC
                                                </button>
                                            @else
                                            <span class="badge badge-success p-2 rounded text-dark bg-warning">
                                                <i class="fas fa-check-circle"></i> NC GENERADA
                                            </span>
                                            @endif
                                        </td>



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

                                                 {{-- <button class="btn btn-warning text-white border-0 ml-3"
                                                        wire:click.prevent="retry({{ $factura->id }})"
                                                        type="button"
                                                        title="Reenviar PDF">
                                                    <i class="fas fa-file-pdf f-2x"></i>
                                                    </button> --}}

                                                <!-- Botón para descargar archivos -->
                                                <button class="btn btn-primary text-white border-0 ml-3"
                                                wire:click.prevent="downloadFiles({{ $factura->id }})"
                                                type="button"
                                                title="Descargar Archivos">
                                            <i class="fas fa-download f-2x text-white"></i>
                                            </button>



                                                {{-- <!-- Botón para anular factura -->
                                                <button class="btn btn-danger text-white border-0 ml-3"
                                                wire:click="confirmDelete({{ $factura->id }})"
                                                type="button"
                                                title="Anular Factura">
                                            <i class="fas fa-trash-alt f-2x"></i>
                                       || </button> --}}

                                             <!-- Botón para emitir nota de crédito -->
                                            {{-- <button class="btn btn-warning text-white border-0 ml-3"
                                                wire:click="confirmNC({{ $factura->id }})"
                                                type="button"
                                                title="Emitir Nota de Crédito">
                                                <i class="fas fa-file-invoice-dollar f-2x"></i>
                                            </button> --}}


                                      </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-gray-200 dark:bg-dark-1">
                                        <td colspan="2">
                                            <h6 class="text-center">    NO HAY NOTAS DE CRÉDITO </h6>
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
    @include('livewire.notascredito.modaldetalles')
    {{-- para el buscador  --}}
    <script>
        const inputSearch = document.getElementById('search')
        inputSearch.addEventListener('change', (e) => {
            @this.search = e.target.value
        })
        // {{--fin  para el buscador  --}}
    window.addEventListener('show_modal', event => {
        openModal()
    })


    function openModal(){

        alert('hola')
    }



    </script>



</div>
