<script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ asset('dist/js/app.js') }}"></script>
<script src="{{ asset('js/snackbar.min.js') }}"></script>
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
<script src="{{ asset('js/alpine.js') }}"></script>



{{-- PARA Los eventos globales del front hacia el back  --}}

<script>

    // para las notificaciones al usuario
    window.addEventListener('noty', event => {
        Snackbar.show({
            text: event.detail.msg,
            actionText: 'CERRAR',
            actionTextColor: '#fff',
            backgroundColor: event.detail.type == 'success' ? '#2187EC' : '#e7515a',
            pos: 'top-right'
        })
    })

    // para las notificaciones de conf iniciales


    // funcion para destruir en cada uno de los componentes
    function destroy(componentName, methodName =  'destroy', rowId){
        swal({
            title: '¿ confirmas eliminar el registro ?',
            text: '',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            confirmButtonColor: '#e7515a',
            cancelButtonText: 'Cerrar',
            padding: '2em'
        }).then(function(result){
            if(result.value){  // cuando el usuario dice aceptar =  true
                window.livewire.emitTo(componentName, methodName, rowId) // aqui se emiteTO desde el front al back
                swal.close()
            }
        })
    }


    function abrir(componentName, methodName =  'destroy', rowId, $montoInicial){
        swal({
            title: '¿ estas seguro de abrir caja ?',
            text: '',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Abrir caja',
            confirmButtonColor: '#e7515a',
            cancelButtonText: 'Cerrar',
            padding: '2em'
        }).then(function(result){
            if(result.value){
                window.livewire.emitTo(componentName, methodName, rowId)
                swal.close()
            }
        })
    }


    function revoque(componentName, methodName =  'revoque', rowId){
        swal({
            title: '¿ confirmas eliminar el descuento ?',
            text: '',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            confirmButtonColor: '#e7515a',
            cancelButtonText: 'Cerrar',
            padding: '2em'
        }).then(function(result){
            if(result.value){  // cuando el usuario dice aceptar =  true
                window.livewire.emitTo(componentName, methodName, rowId) // aqui se emiteTO desde el front al back
                swal.close()
            }
        })
    }


</script>

