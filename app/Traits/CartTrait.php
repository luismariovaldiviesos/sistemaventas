<?php

namespace App\Traits;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Support\Facades\Log;

trait CartTrait {


    public function  getContentCart()
    {
        $cart = new Cart;
        return $cart->getContent()->sortBy('name');

    }

    public  function getTotalCart()
    {
        $cart = new Cart;
        return $cart->totalAmount();
    }

    // FUNCIONES MIAS PARA SACAR IMPUESTOS

    public  function getTotalSICart()
    {
        $cart = new Cart;
        return $cart->totalSinImpuestos();
    }

    public function getIva12()
    {
        $cart =  new Cart;
        return $cart->total12();
    }

    public function getIva0()
    {
        $cart =  new Cart;
        return $cart->total0();
    }

    //total valor iva
    public function getImpuesto12()
    {
        $cart =  new Cart;
        return $cart->totalImpuesto12();
    }

    public function getIce()
    {
        $cart =  new Cart;
        return $cart->totalIce();
    }

    public function getDscto()
    {
        $cart =  new Cart;
        return $cart->totalDsto();
    }


    // FIMM FUNCIONES MIAS PARA SACAR IMPUESTOS

    public function countInCart($id)
    {
        $cart = new Cart;
        return $cart->countInCart($id);
    }

    public function getItemsCart()
    {
        $cart = new Cart;
        return $cart->totalItems();
    }

    public function  updateQtyCart($product, $cant =1 )
    {
        $cart = new Cart;
        $cart->updateQuantity($product->id, $cant);
        $this->noty('CANTIDAD ACTUALIZADA');
    }

    public function addProductCart($product, $cant=1, $changes ='')
    {
        $cart = new Cart;
        if($cart->existsInCart($product->id))
        {
            $cart->updateQuantity($product->id, $cant);
            $this->noty('CANTIDAD ACTUALIZADA');
        } else{
            $cart->addProduct($product, $cant, $changes);
            $this->noty('PRODUCTO AGREGADO');
        }
    }

    public function inCart($id)
    {
        $cart = new Cart;
        return $cart->existsInCart($id);
    }

    public function replaceQuantityCart($id, $cant=1)
    {
        $cart = new Cart;
        $cart->replaceQuantity($id, $cant);
    }

    public function decreaseQtyCart($id)
    {
        $cart = new Cart;
        $cart->decreaseQuantity($id);
        $this->noty('CANTIDAD ACTUALIZADA');
    }

    public function removeProductCart($id)
    {
        $cart = new Cart;
        $cart->removeProduct($id);

    }

    public function addChanges2Product($id, $changes)
    {
        $cart = new Cart;
        $cart->addChanges($id, $changes);
    }

    public function clearChanges($id)
    {
        $cart = new Cart;
        $cart->removeChanges($id);
    }

    public function clearCart()
    {
        $cart = new Cart;
        $cart->clear();
    }


    public function restoreStockFromFacturas($factura){
        //dd($factura->detalles);
        foreach($factura->detalles  as $detalle){
            foreach ($factura->detalles as $detalle) {
                $product = Product::find($detalle->product_id);

                if ($product) {
                    $product->stock += $detalle->cantidad;
                    $product->save();
                    //dd('restaurado');
                } else {
                    Log::warning("Producto con ID {$detalle->product_id} no encontrado al actualizar stock.");
                }
            }
        }
    }

}
