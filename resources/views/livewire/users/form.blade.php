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
                            <input wire:model='name' id="name" type="text" class="form-control form-control-lg border-start-0 kioskboard" maxlength="250">
                            @error('name')
                                <x-alert msg="{{ $message }}" />
                            @enderror
                        </div>

                        <div>
                            <label  class="form-label">RUC</label>
                            <input wire:model='ci' id="ci" type="text" class="form-control form-control-lg border-start-0 kioskboard" maxlength="250">
                            @error('ci')
                                <x-alert msg="{{ $message }}" />
                            @enderror
                        </div>
                        <div>
                            <label  class="form-label">Teléfono</label>
                            <input wire:model='phone' id="phone" type="text" class="form-control form-control-lg border-start-0 kioskboard" maxlength="250">
                            @error('phone')
                                <x-alert msg="{{ $message }}" />
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                <div class="sm:grid grid-cols-3 gap-5">
                    <div>
                        <label  class="form-label">Email</label>
                        <input wire:model='email' id="email" type="text" class="form-control form-control-lg border-start-0 kioskboard" maxlength="250">
                        @error('email')
                            <x-alert msg="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="grid grid-cols-6">
                        <div class="col-end-2 bg-amber-500">
                            <label class="form-label">Perfil</label>
                            <select wire:model.lazy='profile' class="form-select form-select-lg sm:mr-2">
                                <option value="Elegir" selected>Elegir</option>
                                @foreach ($roles as $role )
                                <option value="{{$role->name}}" >{{$role->name}}</option>
                                @endforeach
                            </select>
                            @error('profile')
                            <x-alert msg="{{ $message }}" />
                        @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-6">
                        <div class="col-end-2 bg-amber-500">
                            <label class="form-label">Estado</label>
                            <select wire:model.lazy='status' class="form-select form-select-lg sm:mr-2">
                                <option value="Elegir" selected>Elegir</option>
                                <option value="ACTIVE" selected>Activo</option>
                                <option value="LOCKED" selected>Bloqueado</option>
                            </select>
                            @error('status') <span class="text-danger er">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label  class="form-label">Password</label>
                        <input wire:model='password' id="password" type="password" data-kioskboard-type="numpad" class="form-control form-control-lg border-start-0 kioskboard" maxlength="13">
                        @error('password')
                            <x-alert msg="{{ $message }}" />
                        @enderror
                    </div>
                </div>

                </div>



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
                case 'name':
                    @this.name = e.target.value
                    break
                case 'email':
                    @this.email = e.target.value
                    break
                case 'password':
                    @this.password = e.target.value
                    break
            }

       }))

    </script>

</div>




