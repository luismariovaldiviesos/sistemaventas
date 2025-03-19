<div>

    @if (!$form)

        <div class="intro-y col-span-12">

            <div class="intro-y box">

            <h2 class="text-lg font-medium text-center text-them-1 py-4">
                {{ $componentName }}
            </h2>

            {{-- AQUI LLAMAMOS AL COMPONENTE SEARH --}}
                <x-search />
            {{-- AQUI LLAMAMOS AL COMPONENTE SEARH --}}

            <div class="p-5">
                <div class="preview">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr class="text-theme-1">
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap" >NOMBRE</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap" >PORCENTAJE</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap" >CODIGO SRI</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center" >ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($impuestos as $impuesto )
                                    <tr class=" dark:bg-dark-1 {{ $loop->index % 2> 0 ? 'bg-gray-200' : '' }}">

                                        <td class="dark:border-dark-5">
                                            <h6 class="mb-1 font-medium">{{ $impuesto->nombre }}</h6>
                                            <small class="font-normal">{{ $impuesto->productos->count() }} Productos con este impuesto</small>
                                        </td>
                                        <td class="dark:border-dark-5">
                                            <h6 class="mb-1 font-medium">{{ $impuesto->porcentaje }}</h6>
                                        </td>
                                        <td class="dark:border-dark-5">
                                            <h6 class="mb-1 font-medium">{{ $impuesto->codigo }}</h6>
                                        </td>

                                        <td class="dark:border-dark-5 text-center">
                                            <div class="d-flex justify-content-center">
                                                @if ($impuesto->productos->count() < 1)
                                                    <button class="btn btn-danger text-white border-0"
                                                    onclick="destroy('impuestos','Destroy', {{ $impuesto->id }})"
                                                    type="button">
                                                        <i class=" fas fa-trash f-2x"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-warning text-white border-0 ml-3"
                                                    wire:click.prevent="Edit({{ $impuesto->id }})"
                                                    type="button">
                                                        <i class=" fas fa-edit f-2x"></i>
                                                    </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-gray-200 dark:bg-dark-1">
                                        <td colspan="2">
                                            <h6 class="text-center">    NO HAY IMPUESTOS REGISTRADOS </h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-spam-12 p-5">
                {{ $impuestos->links() }}
            </div>


            </div>
        </div>
    @else

        @include('livewire.impuestos.form')

    @endif

    @include('livewire.sales.keyboard')


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
                 @this.search = e.target.value  // iguala lo que esta en id search con search del comoponente
                 })

            }
        })


    </script>

</div>
