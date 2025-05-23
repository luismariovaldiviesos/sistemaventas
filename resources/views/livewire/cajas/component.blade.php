<div>
    @can('ver_caja')

    @if (!$form)

        <div class="intro-y col-span-12">

            <div class="intro-y box">

            <h2 class="text-lg font-medium text-center text-them-1 py-4">
                {{ $componentName }}
            </h2>

            {{-- AQUI LLAMAMOS AL COMPONENTE SEARH --}}
                @can('crear_caja')
                <x-search />
                @endcan
            {{-- AQUI LLAMAMOS AL COMPONENTE SEARH --}}

            <div class="p-5">
                <div class="preview">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr class="text-theme-1">
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap" >NOMBRE CAJA</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap" >ESTADO CAJA</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap" >USUARIOS</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center" >ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cajas as $caja )
                                    <tr class=" dark:bg-dark-1 {{ $loop->index % 2> 0 ? 'bg-gray-200' : '' }}">

                                        <td class="dark:border-dark-5">
                                            <h6 class="mb-1 font-medium">{{ $caja->nombre }}</h6>
                                        </td>
                                        <td class="dark:border-dark-5">
                                            <h6 class="mb-1 font-medium">{{ $caja->status == 0 ? ('CAJA CERRADA ') :  ('CAJA ABIERTA') }}</h6>
                                        </td>
                                        <td class="dark:border-dark-5">
                                            <h6 class="mb-1 font-medium">{{ $caja->usuario }}</h6>
                                        </td>

                                        <td class="dark:border-dark-5 text-center">
                                            <div class="d-flex justify-content-center">
                                                 @if ($caja->arqueos->count() < 1)
                                                    @can('eliminar_caja')
                                                    <button class="btn btn-danger text-white border-0"
                                                    onclick="destroy('cajas','Destroy', {{ $caja->id }})"
                                                    type="button">
                                                        <i class=" fas fa-trash f-2x"></i>
                                                    </button>
                                                    @endcan
                                                @endif

                                                 @can('editar_caja')
                                                <button class="btn btn-warning text-white border-0 ml-3"
                                                    wire:click.prevent="Edit({{ $caja->id }})"
                                                    type="button">
                                                        <i class=" fas fa-edit f-2x"></i>
                                                </button>
                                                 @endcan


                                                @if ($caja->status == 0 && $caja->user_id == Auth()->user()->id)
                                                @can('abrir_caja')
                                                    <button class="btn btn-primary text-white border-0"
                                                    onclick="Abrircaja({{ $caja->id }})"
                                                    type="button">
                                                    abrir caja
                                                    </button>
                                                @endcan
                                              @endif

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-gray-200 dark:bg-dark-1">
                                        <td colspan="2">
                                            <h6 class="text-center">NO HAY CAJAS</h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-spam-12 p-5">
                {{ $cajas->links() }}
            </div>


            </div>
        </div>
    @else

        @include('livewire.cajas.form')


    @endif

    @include('livewire.sales.keyboard')
    @include('livewire.cajas.modal-valor-inicio')

    @else
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>¡Lo sentimos!</strong> No tienes permisos para ver esta sección.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endcan





    {{-- para el buscador  --}}
    <script>

        document.addEventListener('click', (e) => {
            if(e.target.id == 'search'){
                KioskBoard.run('#search', {})

                // para no hacer click fuera click dentro
                document.getElementById('search').blur()
                document.getElementById('search').focus()

                const inputSearch = document.getElementById('search')
                inputSearch.addEventListener('change', (e) => {
                 @this.search = e.target.value
                 })

            }
        })


        function Abrircaja(idCaja){
            var modal = document.getElementById('modalValorInicio')
            @this.selected_id = idCaja
            modal.classList.add("overflow-y-auto", "show")
		    modal.style.cssText = "margin-top: 0px; margin-left: -100px;  z-index: 10000;"
        }

        function closeModal()
            {
                var modal = document.getElementById('modalValorInicio')
                modal.classList.remove("overflow-y-auto", "show")
                modal.style.cssText = ""
            }


        //liteners que vienen desde el frontend

     // listeners que vienen desde el front -end
     window.addEventListener('close-modal-apertura', event => {
        closeModal()
    })

    </script>

</div>
