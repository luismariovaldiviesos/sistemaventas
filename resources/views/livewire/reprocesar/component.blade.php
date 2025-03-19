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
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">ESTADO</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">REPROCESAR</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">ANULAR</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($xmls as $xml )
                                    <tr class=" dark:bg-dark-1 {{ $loop->index % 2> 0 ? 'bg-gray-200' : '' }}">

                                        <td class="text-center font-medium">{{ $xml->secuencial }}</td>
                                        <td class="text-center font-medium">{{ $xml->cliente}}</td>
                                        <td class="text-center font-medium">
                                            @if ($xml->estado === 'enviado')
                                                Recuperar del SRI
                                            @elseif ($xml->estado === 'firmado')
                                                Reenviar al SRI
                                            @elseif ($xml->estado === 'creado')
                                                Pendiente de firmar
                                            @else
                                                {{ ucfirst($xml->estado) }} <!-- Muestra el estado por defecto si no coincide -->
                                            @endif
                                        </td>

                                        @if($xml->error  == null)
                                        <td class="dark:border-dark-5 text-center">
                                            <div class="d-flex justify-content-center">
                                                    <button class="btn btn-warning text-white border-0 ml-3"
                                                    wire:click.prevent="retry({{ $xml->id }})"
                                                    type="button">
                                                        <i class=" fas fa-edit f-2x"></i>
                                                    </button>
                                            </div>
                                        </td>
                                        @else
                                        <td class="dark:border-dark-5 text-center">
                                            <div class="d-flex justify-content-center text-center">
                                                <h2 style="max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                                    title="{{ $xml->error }}" class="mx-auto">
                                                    {{ $xml->error }}
                                                </h2>
                                            </div>
                                        @endif
                                        <td class="dark:border-dark-5 text-center">
                                            <div class="d-flex justify-content-center">

                                                <button class="btn btn-danger text-white border-0 ml-3"
                                                wire:click="confirmDelete({{ $xml->id }})"
                                                type="button"
                                                title="Anular Factura">
                                            <i class="fas fa-trash-alt f-2x"></i>
                                        </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-gray-200 dark:bg-dark-1">
                                        <td colspan="2">
                                            <h6 class="text-center">    NO HAY FACTURAS PENDIENTES DE REPROCESAR </h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $xmls->links() }}
                    </div>
                </div>
            </div>

            <div class="col-spam-12 p-5">
                {{-- {{ $products->links() }} --}}
            </div>
        </div>
    </div>


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



      </script>




</div>
