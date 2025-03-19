<div class="intro-y col-span-12">
    <div class="intro-y box">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
            <h2 class="font-medium text-base mr-auto">
               Datos Empresa
           </h2>

       </div>
       <div id="vertical-form" class="p-5">
        <div class="preview grid grid-cols-12 gap-5">
            <div class="col-span-2">
                <label  class="form-label">Nombre</label>
                <input wire:model="nombre"  id="nombre" type="text"
                class="form-control  kioskboard"  placeholder="IVA, ICE, IRBPNR, etc." />
                @error('nombre')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>

            <div class="col-span-2">
                <label  class="form-label">Código</label>
                <input wire:model="codigo"  id="codigo" type="text"
                class="form-control  kioskboard"  placeholder="Código SRI (Ej: 2 para IVA, 3 para ICE)" />
                @error('codigo')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>
            <div class="col-span-2">
                <label  class="form-label">Codigo Porcentaje</label>
                <input wire:model="codigo_porcentaje"  id="codigo_porcentaje" type="text"
                class="form-control  kioskboard"  placeholder=" Cód del % (Ej: 4 para IVA 15%)" />
                @error('codigo_porcentaje')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>
            <div class="col-span-2">
                <label  class="form-label">Porcentaje</label>
                <input wire:model="porcentaje"  id="porcentaje" type="text"
                class="form-control  kioskboard"  placeholder="Porcentaje (Ej: 15.00 para IVA 15%)" />
                @error('porcentaje')
                <x-alert msg="{{ $message }}" />
                @enderror
            </div>


            <div class="col-span-12">

                <x-save />

            </div>
        </div>

    </div>
</div>

</div>
