<div class="intro-y col-span-12">
    <div class="intro-y box">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
            <h2 class="font-medium text-base mr-auto">
               Datos Empresa
           </h2>

       </div>
       <div id="vertical-form" class="p-5">
        <div class="preview grid grid-cols-12 gap-5">

            <div class="col-span-4">
                <label  class="form-label">Razon Social</label>
                <input wire:model="razonSocial"  id="razonSocial" type="text"
                class="form-control  kioskboard"  placeholder="" />
                @error('razonSocial')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>

            <div class="col-span-4">
                <label  class="form-label">Nombre del negocio</label>
                <input wire:model="nombreComercial"  id="nombreComercial" type="text"
                class="form-control  kioskboard"  placeholder="" />
                @error('nombreComercial')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>

            <div class="col-span-2">
                <label  class="form-label">Ruc</label>
                <input wire:model="ruc"  id="ruc" type="text"
                class="form-control  kioskboard"  placeholder="" />
                @error('ruc')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>
            <div class="col-span-2">
                <label  class="form-label">Establecimiento</label>
                <input wire:model="estab"  id="estab" type="text"
                class="form-control  kioskboard"  placeholder="eje: 001" />
                @error('estab')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>

            <div class="col-span-4">
                <label  class="form-label">Matriz</label>
                <input wire:model="dirMatriz"  id="dirMatriz" type="text"
                class="form-control  kioskboard"  placeholder="dirección" />
                @error('dirMatriz')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>

            <div class="col-span-4">
                <label  class="form-label">Sucursal</label>
                <input wire:model="dirEstablecimiento"  id="dirEstablecimiento" type="text"
                class="form-control  kioskboard"  placeholder="dirección" />
                @error('dirEstablecimiento')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>

            <div class="col-span-2">
                <label  class="form-label">Pto Emision</label>
                <input wire:model="ptoEmi"  id="ptoEmi" type="text"
                class="form-control  kioskboard"  placeholder="eje:001" />
                @error('ptoEmi')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>
            <div class="col-span-2">
                <label  class="form-label">telefono</label>
                <input wire:model="telefono"  id="telefono" type="text"
                class="form-control  kioskboard"  placeholder="" />
                @error('telefono')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>


            <div class="col-span-2">
                <label  class="form-label">email</label>
                <input wire:model="email"  id="email" type="text"
                class="form-control  kioskboard"  placeholder="" />
                @error('email')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>

            <div class="col-span-2">
                <label  class="form-label">Ambiente 1 pruebas -2 prod  </label>
                <input wire:model="ambiente"  id="ambiente" type="text"
                class="form-control  kioskboard"  placeholder="ejem1" />
                @error('ambiente')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>

            <div class="col-span-2">
                <label  class="form-label">tipo Emision</label>
                <input wire:model="tipoEmision"  id="tipoEmision" type="text"
                class="form-control  kioskboard"  placeholder="eje: fastFOOD" />
                @error('tipoEmision')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>
            <div class="col-span-2">
                <label  class="form-label">Contribuyente especial revisar</label>
                <input wire:model="contribuyenteEspecial"  id="contribuyenteEspecial" type="text"
                class="form-control  kioskboard"  placeholder="eje: 001" />
                @error('contribuyenteEspecial')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>
            <div class="col-span-2">
                <label  class="form-label">Conta select</label>
                <input wire:model="obligadoContabilidad"  id="obligadoContabilidad" type="text"
                class="form-control  kioskboard"  placeholder="eje: 001" />
                @error('obligadoContabilidad')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>

            <div class="col-span-2">
                <label  class="form-label">Impresora</label>
                <input wire:model="printer"  id="printer" type="text"
                class="form-control  kioskboard"  placeholder="eje: 001" />
                @error('printer')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>

            <div class="col-span-4">
                <label  class="form-label">Imágen (Logo Tickets)</label>
                <input wire:model="logo" accept="image/x-png,image/jpeg,image/jpg" class="form-control" type="file">
                @error('logo')
                    <x-alert msg="{{ $message }}" />
                    @enderror
            </div>
            <div class="col-span-4">
                <label  class="form-label">Leyenda:</label>
                <input wire:model="leyend"  id="leyend" type="text"
                class="form-control  kioskboard"  placeholder="eje: gracias por su compra" />
                @error('leyend')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>


            <div class="col-span-12 md:col-span-3 ">
                @if($logoPreview)
                <img class="rounded-lg recent-product-img " data-action="zoom" src="{{ asset($logoPreview) }}" width="150px">
                <h5>Logo actual</h5>
                @endif

            </div>


            <div class="col-span-12 md:col-span-3 flex justify-center" >
                @if($logo)
                <div>
                    <img class="rounded-lg  recent-product-img" src="{{ $logo->temporaryUrl() }}" width="150px">
                    <h5 class="text-center">Nuevo logo</h5>
                </div>

                @endif
            </div>




            <div class="col-span-12">

                <x-save />

            </div>
        </div>

    </div>
</div>

</div>
