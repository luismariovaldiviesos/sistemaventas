<div class="intro-y col-span-12">
    <div class="intro-y box">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
            <h2 class="font-medium text-base mr-auto">
                {{ $componentName  }} | <span class="font-normal">{{ $action }}</span>
            </h2>
        </div>

        <div class="p-5 ">
            <div class="preview">
                <div x-data="{}" x-init="setTimeout(() => { refs.first.focus() }, 900  )">
                    <label class="form-label" >Nombre</label>
                    <input type="text" wire:model="permissionName" x-ref="first" id="categoryName"
                    class="form-control kioskboard {{ $errors->first('permissionName') ?  "border-theme-6" : "" }}"
                    placeholder="nombre del permiso"
                    >
                    @error('permissionName')
                        <x-alert msg="{{ $message }}" />
                    @enderror
                </div>




                <div class="mt-5">

                    {{-- COMPONENTES DE BLADE PARA GUARDAR Y VOLVER --}}
                    <x-back />

                    <x-save />
                </div>

            </div>
        </div>

    </div>


    <script>
        KioskBoard.run('#permissionName', {})
        const inputCatName = document.getElementById('permissionName')
        if(inputCatName){
            inputCatName.addEventListener('change', ()=> {
                @this.permissionName = e.target.value
            })
        }
    </script>

</div>
