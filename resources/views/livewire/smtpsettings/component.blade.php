@can('ver_mail_envio')
<div class="intro-y col-span-12">
    <div class="intro-y box">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
            <h2 class="font-medium text-base mr-auto">
               Datos Correo
           </h2>

       </div>
       <div id="vertical-form" class="p-5">
        <div class="preview grid grid-cols-12 gap-5">

            <div class="col-span-4">
                <label  class="form-label">Proveedor</label>
                  <select wire:change='setProvider($event.target.value)' id="" class="form-control form-control-lg border-start-0 kioskboard">
                                <option value>Elegir</option>
                                @foreach($providers as $provider)
                                 <option value="{{ $provider }}" {{ $provider === $provider ? 'selected' : '' }}>{{ $provider }}</option>
                                @endforeach
                </select>
                 @error('provider')
                    <x-alert msg="{{ $message }}" />
                    @enderror

            </div>

            <div class="col-span-4">
                <label  class="form-label">Correo</label>
                <input wire:model.defer="username"  id="username" type="text"
                class="form-control  kioskboard"  placeholder="ejemplo@mail" />
                 @error('username')
                    <x-alert msg="{{ $message }}" />
                    @enderror
            </div>
              <div class="col-span-4">
                <label  class="form-label">Contraseña</label>
                <input wire:model.defer="password"  id="smtp.password" type="password"
                class="form-control  kioskboard" />
                 @error('password')
                    <x-alert msg="{{ $message }}" />
                    @enderror
            </div>


            <div class="col-span-4">
                <label  class="form-label">Nombre del remitente</label>
                <input wire:model.defer="from_name"  type="text"
                class="form-control  kioskboard"  placeholder="from:empresa" />
                 @error('from_name')
                    <x-alert msg="{{ $message }}" />
                    @enderror

            </div>
          <div class="col-span-4">
                <label  class="form-label">Correo que verá el cliente</label>
                <input wire:model.defer="from_address"  type="mail"
                class="form-control  kioskboard"  placeholder="ejemplo@gmail.com" />
                 @error('from_address')
                    <x-alert msg="{{ $message }}" />
                    @enderror

            </div>
            <div class="col-span-4">
                <label  class="form-label">Protocolo por defecto</label>
                <input wire:model.defer="mailer"  type="mail"
                class="form-control  kioskboard"  placeholder="SMTP" />
            </div>

            <div class="col-span-12">
                @can('editar_mail_envio')

                <x-save />
                @endcan

            </div>
        </div>

    </div>
</div>
</div>
@else
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>¡Lo sentimos!</strong> No tienes permisos para ver esta sección.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endcan
