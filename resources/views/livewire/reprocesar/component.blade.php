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
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">ESTADO</th>
                                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($facturas as $product )
                                    <tr class=" dark:bg-dark-1 {{ $loop->index % 2> 0 ? 'bg-gray-200' : '' }}">

                                        <td class="text-center font-medium">{{ $product->secuencial }}</td>
                                        <td class="text-center font-medium">{{ $product->cliente}}</td>
                                        <td class="text-center font-medium">
                                            @if ($product->estado === 'enviado')
                                                Recuperar del SRI
                                            @elseif ($product->estado === 'firmado')
                                                Reenviar al SRI
                                            @elseif ($product->estado === 'creado')
                                                Pendiente de firmar
                                            @else
                                                {{ ucfirst($product->estado) }} <!-- Muestra el estado por defecto si no coincide -->
                                            @endif
                                        </td>

                                         <td class="dark:border-dark-5 text-center">
                                            <div class="d-flex justify-content-center">
                                                {{-- @if ($product->sales->count() < 1)
                                                    <button class="btn btn-danger text-white border-0"
                                                    onclick="destroy('products','Destroy', {{ $product->id }})"
                                                    type="button">
                                                        <i class=" fas fa-trash f-2x"></i>
                                                    </button>
                                                @endif --}}
                                                <button class="btn btn-warning text-white border-0 ml-3"
                                                    wire:click.prevent="retry({{ $product->id }})"
                                                    type="button">
                                                        <i class=" fas fa-edit f-2x"></i>
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
                        {{ $facturas->links() }}
                    </div>
                </div>
            </div>

            <div class="col-spam-12 p-5">
                {{-- {{ $products->links() }} --}}
            </div>
        </div>
    </div>


        {{-- @include('livewire.products.panel')
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