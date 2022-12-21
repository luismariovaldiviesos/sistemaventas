<div wire:ignore.self id="panelProduct" class="modal modal-slide-over" data-backdrop="static" tabindex="-1">

    <div class="modal-dialog modal-lg">

         <div class="modal-content">

            <a href="javascript:;" data-dismiss="modal">
                <i class="fas fa-times fa-4x w-8 h-8 text-gray-500"></i>
            </a>

            <div class="modal-header p-5">
                <h2 class="font-medium text-base mr-auto">Gestión de Productos</h2>

                @if ($this->ivaporcentaje != 'elegir' && $this->iceporcentaje != 'elegir')

                    <x-save class="mt-4 mr-5"/>

                @endif


            </div>

            <div class="modal-body mr-5">
                <div>
                    <div class="input-group">
                        <div class="input-group-text">Nombre</div>
                        <input type="text" wire:model='name' id="name" class="form-control form-control-lg kioskboard" placeholder="Nombre del producto">
                    </div>
                    @error('name')
                        <x-alert msg="{{ $message  }}" />
                    @enderror
                </div>


                <div class="mt-4">
                    <div class="sm:grid grid-cols-2 gap-2">
                        <div class="input-group">
                            <div class="input-group-text">Costo</div>
                            <input type="number" id="cost" wire:model='cost'
                            class="form-control form-control-lg kioskboard" data-kioskboard-type="numpad"  placeholder="">
                        </div>
                        <div class="input-group">
                            <div class="input-group-text">Código</div>
                            <input type="text" id="code" wire:model='code'
                            class="form-control form-control-lg kioskboard"  placeholder="">
                        </div>
                        @error('cost')
                        <x-alert msg="{{ $message  }}" />
                        @enderror
                        @error('code')
                        <x-alert msg="{{ $message  }}" />
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <div class="input-group">
                        <div class="input-group-text">Precio1</div>
                        <input type="number"  id="price" wire:model='price' class="form-control form-control-lg kioskboard"  data-kioskboard-type="numpad" placeholder="">
                    </div>
                    @error('price')
                    <x-alert msg="{{ $message  }}" />
                    @enderror
                </div>


                <div class="mt-4">
                    <div class="input-group">
                        <div class="input-group-text">Precio2</div>
                        <input type="number" readonly  id="price2" wire:model='price2' class="form-control form-control-lg kioskboard"  data-kioskboard-type="numpad" placeholder="">
                    </div>
                    @error('price2')
                    <x-alert msg="{{ $message  }}" />
                    @enderror
                </div>

                <div class="mt-4">
                    <div class="sm:grid grid-cols-2 gap-2">
                        <div class="input-group">
                            <div class="input-group-text">Stock</div>
                            <input type="number" id="stock" wire:model='stock'
                            class="form-control form-control-lg kioskboard" data-kioskboard-type="numpad"  placeholder="">
                        </div>
                        <div class="input-group">
                            <div class="input-group-text">Mínimos</div>
                            <input type="text" id="minstock" wire:model='minstock'
                            class="form-control form-control-lg kioskboard"  placeholder="">
                        </div>
                        @error('stock')
                        <x-alert msg="{{ $message  }}" />
                        @enderror
                        @error('minstock')
                        <x-alert msg="{{ $message  }}" />
                        @enderror
                    </div>
                </div>


                <div class="mt-4">

                    <div class="input-group">
                        <div class="input-group-text">Categoría</div>
                        <select wire:model='category' class="form-select form-select-lg sm:mr-2" name="" id="">
                            <option value="elegir">Elegir</option>
                            @foreach ($categories  as $category )
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('category')
                    <x-alert msg="{{ $message  }}" />
                    @enderror
                </div>


                <div class="mt-4">
                    <div class="sm:grid grid-cols-2 gap-2">
                        <div class="input-group">
                            <div class="input-group-text">IVA {{ number_format($iva,2)  }}</div>
                            <select wire:model='ivaporcentaje' class="form-select form-select-lg sm:mr-2" name="" id="">
                                <option value="elegir">Elegir</option>
                                <option value="0">0%</option>
                                <option value="12">12%</option>

                             </select>
                        </div>
                        <div class="input-group">
                            <div class="input-group-text">ICE {{ number_format($ice,2)  }}</div>
                            <select wire:model='iceporcentaje' class="form-select form-select-lg sm:mr-2" name="" id="">
                                <option value="elegir">Elegir</option>
                                <option value="0">0%</option>
                                <option value="16">16%</option>
                            </select>
                        </div>
                        @error('ice')
                        <x-alert msg="{{ $message  }}" />
                        @enderror
                        @error('iva')
                        <x-alert msg="{{ $message  }}" />
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <div class="input-group">
                        <div class="input-group-text">Descuento</div>
                        <input type="number"  id="descuento"  wire:model='descuento' class="form-control form-control-lg kioskboard"
                         data-kioskboard-type="numpad" placeholder="" step=".01">
                    </div>
                    @error('descuento')
                    <x-alert msg="{{ $message  }}" />
                    @enderror
                </div>



                <div class="mt-4">
                    <div class="grid grid-flow-col auto-cols-max md:auto-cols-min gap-2">
                        <div>
                            <label for="">Imágenes</label>
                            <input type="file" class="form-control" wire:model.defer='gallery'   accept="image/x-png,image/jpeg,image/jpg" multiple>
                            @error('gallery')
                            <span style="color:red; ">{{ $message }}</span>
                            @enderror
                        </div>
                        <div wire:loading wire:target='gallery'>Subiendo imágenes...</div>
                    </div>
                    @if (!empty($gallery))
                        <div class="sm:grid-cols-12 md-grid-cols-2 grid grid-cols-3 gap-2 pt-2 overflow-y-auto">
                            @foreach ($gallery as $photo )
                                <div>
                                    <img class="rounded-lg" src="{{ $photo->temporaryUrl() }}" alt="image">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

             </div>

        </div>

</div>
