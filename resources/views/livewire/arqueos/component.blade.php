<div>
    <div class="intro-y col-span-12">

        <div class="intro-y box">

            <h2 class="text-lg font-medium text-center text-them-1 py-4">
                {{ $componentName }}
            </h2>

            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2 p-4">
                {{-- <button onclick="openPanel('add')" class="btn btn-primary shadow-md mr-2">Agregar</button>--}}
                <div class="hidden md:block mx-auto text-gray-600">
                    --
                </div>

                <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                    <div class="w-56 relative text-gray-700 dark:text-gray-300 ">
                        <input wire:model='search' id="search" class="form-control w-56 box pr-10  placeholder-theme-13 kioskboard" type="text" placeholder="aa-mm-dd">
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
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">CAJA</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">USUARIO</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">MONTO INICIAL</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">MONTO FINAL</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">TOTAL VENTAS</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">TOTAL CAJA</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">OBSERVACIONES</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">FECHA APERTURA</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">FECHA CIERRE</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center" >ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($arqueos as $arqueo )
                                    <tr class=" dark:bg-dark-1 {{ $loop->index % 2> 0 ? 'bg-gray-200' : '' }}">

                                        <td class="text-center font-medium">{{ $arqueo->caja_id }}</td>
                                        <td class="text-center font-medium">{{ $arqueo->usuario }}</td>
                                        <td class="text-center font-medium">{{ number_format($arqueo->monto_inicial, 2 ) }}</td>
                                        <td class="text-center font-medium">{{ number_format($arqueo->monto_final,2 ) }}</td>
                                        <td class="text-center font-medium">{{ number_format($arqueo->total,2 ) }}</td>
                                        <td class="text-center font-medium">{{ number_format($arqueo->monto_final + $arqueo->total,2 ) }}</td>
                                        <td class="text-center font-medium">{{ $arqueo->observaciones  }}</td>

                                        <td class="text-center font-medium">
                                            <h6>

                                                {{ $arqueo->created_at->isoFormat(' H:mm') }}
                                            </h6>
                                            <small class="font-normal">
                                                {{\Carbon\Carbon::parse($arqueo->created_at)->isoFormat('LL')}}
                                            </small>

                                        </td>

                                        @if ($arqueo->created_at == $arqueo->updated_at)
                                        <td class="text-center text-theme-1 font-medium">CIERRE PENDIENTE</td>
                                        @else
                                        {{-- <td class="text-center font-medium">{{\Carbon\Carbon::parse($arqueo->fecha_cierre)->format('d-m-Y H:m')}}</td>
                                        <small class="font-normal">Caja cerrada</small> --}}
                                        <td class="text-center font-medium">
                                            <h6>

                                                {{\Carbon\Carbon::parse($arqueo->fecha_cierre)->isoFormat('H:mm')}}
                                            </h6>
                                            <small class="font-normal">
                                                {{\Carbon\Carbon::parse($arqueo->fecha_cierre)->isoFormat('LL')}}
                                            </small>

                                        </td>
                                        @endif



                                        <td class="dark:border-dark-5 text-center">
                                            @if ($arqueo->created_at == $arqueo->updated_at)
                                            <button class="btn btn-danger text-white border-0"
                                            onclick="CerrarCaja({{ $arqueo->id }})"
                                            type="button">
                                                Cerrar caja
                                            </button>
                                                    {{-- <small class="font-normal ">Cerrar Caja</small> --}}
                                            @else
                                                <button class="btn btn-success text-white border-0"
                                                    onclick="detalleArqueo({{ $arqueo->caja_id }})"
                                                    type="button">
                                                        <i class=" fas fa-lock f-2x "></i>
                                                        <small class="font-normal">Detalle arqueo</small>
                                                </button>
                                            @endif
                                        </td>



                                    </tr>
                                @empty
                                    <tr class="bg-gray-200 dark:bg-dark-1">
                                        <td colspan="2">
                                            <h6 class="text-center">    NO HAY ARQUEOS REGISTRADOS </h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $arqueos->links() }}
                    </div>
                </div>
            </div>

            <div class="col-spam-12 p-5">
                {{-- {{ $products->links() }} --}}
            </div>
        </div>
    </div>



        @include('livewire.sales.keyboard')
        @include('livewire.arqueos.modal-cierre')

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


        function CerrarCaja(id){
            console.log(id);
            var modal = document.getElementById('modal-cierre')
            @this.selected_id = id
            modal.classList.add("overflow-y-auto", "show")
		    modal.style.cssText = "margin-top: 0px; margin-left: -100px;  z-index: 10000;"
        }

        function closeModal()
            {
                var modal = document.getElementById('modal-cierre')
                modal.classList.remove("overflow-y-auto", "show")
                modal.style.cssText = ""
            }


        //liteners que vienen desde el frontend

     // listeners que vienen desde el front -end
     window.addEventListener('close-modal-cierre', event => {
        closeModal()
    })
    </script>



</div>
