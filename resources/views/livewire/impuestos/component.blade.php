<div>
    @can('ver_impuestos')

    @if (!$form)

        <div class="intro-y col-span-12">

            <div class="intro-y box">

            <h2 class="text-lg font-medium text-center text-them-1 py-4">
                {{ $componentName }}
            </h2>

            {{-- AQUI LLAMAMOS AL COMPONENTE SEARH --}}
            @can('crear_impuestos')
                <x-search />
            @endcan
            {{-- AQUI LLAMAMOS AL COMPONENTE SEARH --}}

            <div class="p-5">
                <div class="preview">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr class="text-theme-1">

                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">CANT PROD x IMP</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">NOMBRE</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">CODIGO</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">CODIGO %</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">PORCENTAJE </th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($impuestos as $impuesto )

                                        <td class="text-center font-medium">{{$impuesto->productos->count()  }}</td>
                                        <td class="text-center font-medium">{{$impuesto->nombre  }}</td>
                                        <td class="text-center font-medium">{{$impuesto->codigo  }}</td>
                                        <td class="text-center font-medium">{{ $impuesto->codigo_porcentaje  }}</td>
                                        <td class="text-center font-medium">{{ $impuesto->porcentaje  }}</td>


                                        <td class="dark:border-dark-5 text-center">
                                            <div class="d-flex justify-content-center">

                                                @if($impuesto->productos->count() <= 0)
                                                    @can('eliminar_impuestos')
                                                    <button class="btn btn-danger text-white border-0"
                                                    onclick="destroy('impuestos','Destroy', {{ $impuesto->id }})"
                                                    type="button">
                                                        <i class=" fas fa-trash f-2x"></i>
                                                    </button>
                                                    @endcan
                                                @endif
                                                @can('editar_impuestos')
                                                <button class="btn btn-warning text-white border-0 ml-3"
                                                    wire:click.prevent="Edit({{ $impuesto->id }})"
                                                    type="button">
                                                        <i class=" fas fa-edit f-2x"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-gray-200 dark:bg-dark-1">
                                        <td colspan="2">
                                            <h6 class="text-center">    NO HAY IMPUESTOS  REGISTRADOS </h6>
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

@else
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>¡Lo sentimos!</strong> No tienes permisos para ver esta sección.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endcan


</div>
