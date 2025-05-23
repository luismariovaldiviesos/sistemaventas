
@can('ver_facturacion')

@if ($estadoCaja === 'nocajasasignadas')


<h1 class="text-2xl font-bold"> <i class="fas fa-lock"></i> USUARIO NO TIENE CAJA ASIGNADA</h1>


@else


    <div class="pos intro-y grid grid-cols-12 gap-5 mt-5">
        <div class="intro-y col-span-12 lg:col-span-9">

            <div class="post intro-y overflow-hidden box">
                <div class="post__tabs nav nav-tabs flex-col sm:flex-row bg-gray-300 dark:bg-dark-2 text-gray-600" role="tablist">

                    <a wire:click="setTabActive('tabProducts')"
                    title="Productos Agregados"
                    data-toggle="tab"
                    data-target="#tabProducts"
                    href="javascript:;"
                    class="tooltip w-full sm:w-40 py-4 text-center flex justify-center items-center {{$tabProducts ? 'active' : '' }}"
                    id="content-tab"
                    role="tab" >
                    <i class="fas fa-list mr-2"></i> DETALLE DE VENTA
                    </a>

                @if ($estadoCaja == 0)

                    {{-- @can('abrir_caja') --}}


                    <a
                    title="Abrir caja en sección cajas"
                    data-toggle="tab"
                    data-target="#"
                    href="{{ route('cajas') }}"
                    class="tooltip w-full sm:w-40 py-4 text-center flex justify-center items-center {{$tabProducts ? 'active' : '' }}"
                    id="content-tab"
                    role="tab" >
                    <i class=" fas fa-folder-open f-2x"></i> ABRIR CAJA
                    </a>
                    {{-- @endcan --}}

                @else
                    <a wire:click="setTabActive('tabCategories')" title="Seleccionar Categoría" data-toggle="tab" data-target="#tabCategory" href="javascript:;" class="tooltip w-full sm:w-40 py-4 text-center flex justify-center items-center {{$tabCategories ? 'active' : '' }}" id="meta-title-tab" role="tab" aria-selected="false">
                        <i class="fas fa-th-large mr-2"></i> CATEGORÍAS
                    </a>

                @endif



                </div>

                <div class="post__content tab-content">
                    <div id="tabProducts" class="tab-pane {{$tabProducts ? 'active' : '' }}" role="tabpanel" aria-labelledby="content-tab">
                        <div class="p-5" id="striped-rows-table">
                            <div class="preview">
                                <div class="overflow-x-auto">
                                    <table class="table">
                                        <thead>
                                            <tr class="text-theme-6">
                                                <th class="border-b-2 dark:border-dark-5 whitespace-nowrap font-bold text-center" width="15%">CANT</th>
                                                <th class="border-b-2 dark:border-dark-5 whitespace-nowrap font-bold" width="60%">DESCRIPCIÓN</th>
                                                <th class="border-b-2 dark:border-dark-5 whitespace-nowrap font-bold">P. UNITARIO</th>
                                                <th class="border-b-2 dark:border-dark-5 whitespace-nowrap font-bold">IMPUESTOS</th>
                                                <th class="border-b-2 dark:border-dark-5 whitespace-nowrap font-bold">TOTAL IMPUESTOS</th>
                                                <th class="border-b-2 dark:border-dark-5 whitespace-nowrap font-bold">DSTO</th>
                                                <th class="border-b-2 dark:border-dark-5 whitespace-nowrap font-bold">PVP</th>
                                                <th class="border-b-2 dark:border-dark-5 whitespace-nowrap font-bold">SUBTOTAL</th>

                                                <th class="border-b-2 dark:border-dark-5 whitespace-nowrap font-bold"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($contentCart as $item)
                                            <tr class="bg-gray-200 dark:bg-dark-1 text-lg">


                                                {{-- CANTIDAD --}}

                                                <td class="border-b dark:border-dark-5 text-center">
                                                    <div class="input-group mt-2">
                                                        <input wire:keydown.enter="updateQty({{$item->id}}, $event.target.value )"
                                                        {{-- data-type qty es para que se active el teclado tactil en la cantidad
                                                        el evento esta en el archivo scripts de sales  --}}
                                                        data-type="qty"
                                                        value="{{$item->qty}}" data-kioskboard-type="numpad" data-type="qty" type="text" class="form-control text-center kioskboard" id="r{{$item->id}}">
                                                        <div wire:click="updateQty({{$item->id}}, document.getElementById('r'+ {{$item->id}} ).value )" class="input-group-text {{$item->livestock > 0 ? '' : 'hidden'}} ">
                                                            <i class="fas fa-redo fa-lg"></i>
                                                        </div>
                                                    </div>
                                                    {{-- <div><small class="text-xs text-theme-1">{{$item->livestock}}</small></div> --}}
                                                </td>

                                                   {{--  FIN CANTIDAD --}}

                                                      {{-- DESCRIPCION --}}
                                                    <td class="border-b dark:border-dark-5 ">
                                                        <button onclick="openModal({{$item->id}},'{{$item->changes}}','{{$item->name}}')" class="btn btn-outline-secondary text-theme-1">{{$item->name}}</button>
                                                        <div>
                                                            <small>{{$item->changes}}</small>
                                                        </div>
                                                    </td>

                                                   {{-- FIN DESCRIPCION  --}}

                                                   {{-- PRECIO UNITARIO  --}}

                                                <td class="border-b dark:border-dark-5 text-center">{{number_format($item->price,2)}}</td>

                                                {{-- FIN PRECIO UNITARIO  --}}

                                                {{-- IMPUESTOS --}}

                                                <td class="border-b dark:border-dark-5 text-center">

                                                    @foreach ($item->impuestos as $imp)
                                                       <div>
                                                        {{ $imp->nombre }}
                                                    </div>
                                                    @endforeach

                                                </td>
                                                {{-- FIN IMPUESTOS --}}
                                                 {{-- TOTAL IMPUESTOS --}}

                                                  <td class="border-b dark:border-dark-5 text-center">
                                                            {{-- {{ number_format(($item['price'] * $item['qty'] - ($item['price'] * $item['qty'] * ($item['descuento'] / 100))) * ($imp['porcentaje'] / 100), 2) }}
                                                             --}}
                                                        {{ number_format($item['total_impuesto'],2) }}
                                                   </td>
                                                 {{-- FIN TOTAL IMPUESTOS --}}

                                                     {{-- DESCUENTO --}}

                                                     <td class="border-b dark:border-dark-5 text-center">{{number_format( $item->descuento)}}%</td>
                                                     <td class="border-b dark:border-dark-5 text-center">{{number_format( $item->price2,2)}}</td>
                                                     {{-- FIN DESCUENTO --}}

                                                  {{-- TOTAL --}}
                                                <td class="border-b dark:border-dark-5 text-center">
                                                {{number_format($item->price2 * $item->qty,2) }}
                                                {{-- <small>{{$this->subTotSinImpuesto}}</small> --}}
                                                </td>
                                                  {{-- FIN TOTAL --}}




                                                <td>
                                                    <div class="inline-flex" role="group" style="font-size: 1.6em!important;">
                                                        <button  wire:click.prevent="removeFromCart({{$item->id}})" class=" btn btn-danger"><i class="fas fa-trash "></i></button>
                                                        <button  wire:click.prevent="decreaseQty({{$item->id}})" class="btn btn-warning ml-4"><i class="fas fa-minus "></i></button>


                                                        {{-- desactivamos el boton ams si als existencias son menores --}}
                                                        <button  wire:click.prevent="increaseQty({{$item->id}})"
                                                        class="btn btn-success ml-4 " >
                                                        <i class="fas fa-plus"></i>
                                                        </button>


                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center">AGREGA PRODUCTOS AL CARRITO</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>






                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tabCategory" class="tab-pane p-5 {{$tabCategories ? 'active' : '' }}" role="tabpanel" aria-labelledby="content-tab">

                        <div class="intro-y grid grid-cols-12 gap-3 sm:gap-4 mt-2">

                            @if(!$showListProducts)
                            @foreach($categories as $category)
                            <div class="intro-y col-span-6 sm:col-span-4 md:col-span-3 xxl:col-span-2">
                                <div wire:click="getProductsByCategory({{ $category->id}})" class="file box rounded-md px-5 pt-8 pb-5 px-3 sm:px-5 relative zoom-in">
                                    <h3>{{ $category->name }}</h3>
                                    <a href="javascript:;" class="w-3/5 file__icon file__icon--image mx-auto">

                                        <div class="file__icon--image__preview image-fit ">
                                            <img alt="img" src="{{ asset($category->img)}}">
                                        </div>
                                        <div>

                                        </div>
                                    </a>
                                    {{-- <a href="javascript:;" class="hidden block font-medium mt-4 text-center truncate">{{$category->name}}</a> --}}

                                </div>
                            </div>
                            @endforeach
                            @else
                            @forelse($productsList as $product)
                            <div class="intro-y col-span-6 sm:col-span-4 md:col-span-3 xxl:col-span-2">
                                <div wire:click="add2Cart({{$product->id}})" class="file box rounded-md px-5 pt-8 pb-5 px-3 sm:px-5 relative zoom-in">

                                    <a href="javascript:;" class="w-3/5 file__icon file__icon--image mx-auto">
                                        <div class="file__icon--image__preview image-fit">
                                            <img alt="img" src="{{ asset($product->img) }}">
                                        </div>
                                    </a>
                                    <a href="javascript:;" class="block font-medium mt-4 text-center truncate">{{$product->name}}</a>
                                    <h1 class="text-center">${{ number_format($product->price2,2) }}</h1>
                                </div>
                            </div>
                            @empty
                            <div class="col-span-12">
                                <h1 class="text-center text-theme-6 w-full">No hay productos en la categoría seleccionada</h1>
                            </div>
                            @endforelse
                            @endif

                        </div>
                    </div>
                </div>
            </div>

        </div>
    <!-- END: Post Content -->
    <!-- BEGIN: Post Info -->
        <div class="col-span-12 lg:col-span-3">
            <div class="intro-y box p-5">
                <div>
                    <h2 class="text-2xl text-center mb-3">Resumen de Venta</h2>
                    @if ($estadoCaja != 0)
                    <button onclick="openModalCustomer()" class="btn btn-outline-dark w-full mb-3">{{$customerSelected}}</button>
                    <button onclick="openModalProduct()" class="btn btn-outline-dark w-full mb-3">{{$productSelected}}</button>
                    @endif

                </div>
                <div class="mt-3">
                    <h1 class="text-2x1 font-bold">ITEMS</h1>
                    <h4 class="text-2x5">{{$itemsCart}}</h4>
                </div>


               <!-- Subtotales dinámicos por tipo de impuesto -->
                 @foreach ($subtotales as $nombre => $valor)
                    <div class="mt-3">
                        <h1 class="text-2x1 font-bold">SUBTOTAL {{ $nombre }}</h1>
                        <h4 class="text-2x1">${{ number_format($valor, 2) }}</h4>
                    </div>
                @endforeach
                    <div class="mt-3">
                    <h1 class="text-2x1 font-bold"></h1>
                        @foreach($this->impuestos as $nombre => $valor)
                                <h4>total  <p>{{ $nombre }}: ${{ number_format($valor, 2) }}</p></h4>
                        @endforeach
                    </div>
                {{-- @foreach ($resumenImpuestos as $imp)
                    <div class="mt-3">
                        <h1 class="text-2x1 font-bold">{{ $imp['nombre'] }}</h1>
                        <p>Base imponible: ${{ number_format($imp['base_imponible'], 2) }}</p>
                        <p>Impuesto: ${{ number_format($imp['valor_impuesto'], 2) }}</p>
                    </div>
                @endforeach --}}
                <div class="mt-3">
                    <h1 class="text-2x1 font-bold">SUBTOTAL FACTURA</h1>
                    <h4 class="text-2x1">${{number_format($this->subTotSinImpuesto,2)}}</h4>
                </div>
                <div class="mt-3">
                    <h1 class="text-2x1 font-bold">DESCUENTO FACTURA</h1>
                    <h3 class="text-2x1">${{number_format($this->totalDscto,2)}}</h3>
                </div>
                <div class="mt-3">
                    <h1 class="text-2x1 font-bold">TOTAL</h1>
                    <h3 class="text-2x1">${{number_format($totalCart,2)}}</h3>
                </div>

                <div class="mt-6">
                    <div class="input-group">
                        <div id="input-group-3" class="input-group-text"><i class="fas fa-dollar-sign fa-2x"></i></div>
                        <input wire:model="cash" id="cash" type="number" data-kioskboard-type="numpad"  class="form-control form-control-lg kioskboard" placeholder="0.00">
                    </div>
                    <h1>Ingresar el Efectivo</h1>
                </div>
                <div class="mt-8">
                    @if($totalCart > 0 && ($cash >= $totalCart))
                        {{-- <button wire:loading.attr="disabled" wire:target="storeSale" wire:click.prevent="storeSale" class="btn btn-primary w-full"><i class="fas fa-database mr-2"></i> Guardar Venta</button> --}}
                        <button wire:loading.attr="disabled" wire:target="storeSale" wire:click.prevent="storeSale(true)" class="btn btn-outline-primary w-full mt-5"><i class="fas fa-receipt mr-2"></i> Guardar e Imprimir</button>
                    @endif

                    @if($totalCart >0)
                        <button onclick="Cancel()" class="btn btn-danger w-full mt-5">
                        <i class="fas fa-trash mr-2"> </i>
                        Cancelar Venta</button>
                    @endif

                </div>

            </div>
        </div>
    <!-- END: Post Info -->
        @include('livewire.sales.modal-changes')
        @include('livewire.sales.modal-customers')
        @include('livewire.sales.modal-products')
        @include('livewire.sales.script')
        @include('livewire.sales.keyboard')




    </div>

 @endif

 @else

 <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>¡Lo sentimos!</strong> No tienes permisos para ver esta sección.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

 @endcan


