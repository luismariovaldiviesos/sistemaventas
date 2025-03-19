<div class="intro-y col-span-12">
    <div class="intro-y box">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
            <h2 class="font-medium text-base mr-auto">
                {{ $componentName  }} | <span class="font-normal">{{ $action }}</span>
            </h2>
        </div>

        <div class="p-5 ">
            <div class="preview">

                <div class="mt-3">
                    <div class="sm:grid grid-cols-3 gap-5">
                        <div>
                            <label  class="form-label">Nombre</label>
                            <input wire:model='nombre' id="nombre" type="text" class="form-control form-control-lg border-start-0 kioskboard" maxlength="250">
                            @error('nombre')
                                <x-alert msg="{{ $message }}" />
                            @enderror
                        </div>
                        <div>
                            <label  class="form-label">Porcentaje</label>
                            <input wire:model='porcentaje' id="porcentaje" type="text" class="form-control form-control-lg border-start-0 kioskboard" maxlength="250">
                            @error('porcentaje')
                                <x-alert msg="{{ $message }}" />
                            @enderror
                        </div>

                        <div>
                            <label  class="form-label">Código SRI</label>
                            <input wire:model='codigo' id="codigo" type="text" class="form-control form-control-lg border-start-0 kioskboard" maxlength="250">
                            @error('codigo')
                                <x-alert msg="{{ $message }}" />
                            @enderror
                        </div>

                        {{-- <div>
                            <label  class="form-label">Tipo</label>
                            <select wire:model='typeidenti' id="typeidenti" class="form-control form-control-lg border-start-0 kioskboard">
                                <option selected>Elegir</option>
                                 <option value="ci">ci</option>
                                <option value="ruc">ruc</option>

                            </select>
                            @error('businame')
                                <x-alert msg="{{ $message }}" />
                            @enderror
                        </div>

                        <div>
                            <label  class="form-label">CI-RUC</label>
                            <input wire:model='valueidenti' id="valueidenti" type="text" data-kioskboard-type="numpad" class="form-control form-control-lg border-start-0 kioskboard" maxlength="13">
                            @error('valueidenti')
                                <x-alert msg="{{ $message }}" />
                            @enderror
                        </div>

                        <div>
                            <label  class="form-label">Dirección</label>
                            <input wire:model='address' id="address" type="text" class="form-control form-control-lg border-start-0 kioskboard" maxlength="250">
                            @error('address')
                                <x-alert msg="{{ $message }}" />
                            @enderror
                        </div> --}}

                    </div>

                </div>

                {{-- <div class="mt-3">
                    <div class="sm:grid grid-cols-3 gap-5">
                        <div>
                            <label  class="form-label">Email</label>
                            <input wire:model='email' id="email" type="text" class="form-control form-control-lg border-start-0 kioskboard" maxlength="250">
                            @error('email')
                                <x-alert msg="{{ $message }}" />
                            @enderror
                        </div>

                        <div>
                            <label  class="form-label">Teléfono</label>
                            <input wire:model='phone' id="phone" type="text" data-kioskboard-type="numpad" class="form-control form-control-lg border-start-0 kioskboard" maxlength="250">
                            @error('phone')
                                <x-alert msg="{{ $message }}" />
                            @enderror
                        </div>

                        <div>
                            <label  class="form-label">Notas</label>
                            <input wire:model='notes' id="notes" type="text" class="form-control form-control-lg border-start-0 kioskboard" maxlength="250">
                            @error('notes')
                                <x-alert msg="{{ $message }}" />
                            @enderror
                        </div>

                    </div>

                </div> --}}

                <div class="mt-5">
                    <x-back />

                    <x-save />
                </div>

            </div>
        </div>

    </div>


    <script>

       KioskBoard.run('.kioskboard', {})

       document.querySelectorAll(".kioskboard").forEach(i => i.addEventListener("change", e =>{

            switch(e.currentTarget.id)
            {
                case 'nombre':
                    @this.nombre = e.target.value
                    break
                case 'porcentaje':
                    @this.porcentaje = e.target.value
                    break
                case 'codigo':
                    @this.codigo = e.target.value
                    break
                // case 'email':
                //     @this.email = e.target.value
                //     break
                // case 'phone':
                //     @this.phone = e.target.value
                //     brea
                // case 'notes':
                //     @this.notes = e.target.value
                //     break
            }

       }))

    </script>

</div>




