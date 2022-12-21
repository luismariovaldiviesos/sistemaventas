<script>

    // para cancelar venta

    function Cancel() {
		swal({
			title: '¿DESEAS CANCELAR LA VENTA?',
			text: "",
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Eliminar',
			confirmButtonColor: '#e7515a',
			cancelButtonText: 'Cerrar',
			padding: '2em'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('cancelSale')  // este evento se emite al back y ahi hay que escuchar
				swal.close()
			}
		})
	}
    //modal changes
    function openModal(proId, currentChanges, productName){

        var modal = document.getElementById('modalChanges')
        @this.productIdSelected = proId
        @this.productChangesSelected = currentChanges
        @this.productNameSelected = productName

        modal.classList.add("overflow-y-auto", "show")
		modal.style.cssText = "margin-top: 0px; margin-left: -100px;  z-index: 10000;"
    }


    function closeModal()
    {
        var modal = document.getElementById('modalChanges')
        modal.classList.remove("overflow-y-auto", "show")
		modal.style.cssText = ""
    }

    // listeners que vienen desde el front -end
    window.addEventListener('close-modal-changes', event => {
        closeModal()
    })
    window.addEventListener('close-customer-modal', event => {
        closeModalCustomer()
    })
    window.addEventListener('close-product-modal', event => {
        closeModalProduct()
    })

    function openModalCustomer() {
		var modal = document.getElementById("modalCustomer")
		modal.classList.add("overflow-y-auto", "show")
		modal.style.cssText = "margin-top: 0px; margin-left: -100px;  z-index: 1000;"
	}

    function openModalProduct() {
		var modal = document.getElementById("modalProduct")
		modal.classList.add("overflow-y-auto", "show")
		modal.style.cssText = "margin-top: 0px; margin-left: -100px;  z-index: 1000;"
	}


    function closeModalCustomer() {
		var modal = document.getElementById("modalCustomer")
		modal.classList.remove("overflow-y-auto", "show")
		modal.style.cssText = ""
	}
    function closeModalProduct() {
		var modal = document.getElementById("modalProduct")
		modal.classList.remove("overflow-y-auto", "show")
		modal.style.cssText = ""
	}

    //sincronizar valor de propiedades
    const input =  document.getElementById('customer-search')
    input.addEventListener('change', (e) =>{
        @this.searchCustomer = e.target.value
    })

     //sincronizar valor de propiedades
     const input2 =  document.getElementById('product-search')
    input2.addEventListener('change', (e) =>{
        @this.searchProduct = e.target.value
    })

    const inputCash =  document.getElementById('cash')
    inputCash.addEventListener('change', (e) =>{
        @this.cash = e.target.value
    })


    document.addEventListener('click', (e) => {
        if(e.target.getAttribute('data-type') === 'qty'){
            KioskBoard.run('#' + e.target.id, {})
            document.getElementById( e.target.id).blur()
            document.getElementById( e.target.id).focus()
        }
    })


</script>
