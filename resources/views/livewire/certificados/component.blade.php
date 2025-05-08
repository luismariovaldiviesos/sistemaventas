<div class="intro-y col-span-12">
    @can('ver_firma')
    <div class="intro-y box">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
            <h2 class="font-medium text-base mr-auto">
                {{ $componentName  }}
            </h2>
        </div>

        <div class="p-5 ">
            <div class="preview">

                <div class="mt-3">
                    <label class="form-label">Archivo en formato p12</label>
                    <input type="file"
                    wire:model='certificado'
                    accept=".p12"
                    class="form-control">
                </div>

                <div x-data="{}" x-init="setTimeout(() => { refs.first.focus() }, 900  )">
                    <label class="form-label" >Contraseña</label>
                    <input type="password" wire:model="password" x-ref="first"
                    class="form-control kioskboard {{ $errors->first('password') ?  "border-theme-6" : "" }}"
                    placeholder="archivo en formato p12"
                    >
                    @error('password')
                        <x-alert msg="{{ $message }}" />
                    @enderror
                </div>




                <div class="mt-5">

                    {{-- COMPONENTES DE BLADE PARA GUARDAR Y VOLVER --}}
                    {{-- <x-back /> --}}

                    <x-save />
                </div>

            </div>
        </div>

    </div>


    <script>
        KioskBoard.run('#categoryName', {})
        const inputCatName = document.getElementById('categoryName')
        if(inputCatName){
            inputCatName.addEventListener('change', ()=> {
                @this.name = e.target.value
            })
        }
    </script>
    @else
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>¡Lo sentimos!</strong> No tienes permisos para ver esta sección.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endcan


</div>
