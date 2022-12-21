<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use DB;

class Descuentos extends Component
{
    use WithPagination;

    public $search, $pagination=10, $componentName = 'Productos con Descuentos' ;

    public function render()
    {

        $products = Product::where('descuento','>', 0)
        ->orderBy('id', 'desc')
        ->paginate($this->pagination);

        return view('livewire.descuentos.component', [
            'products' => $products
        ])->layout('layouts.theme.app');
    }


    public function Revoque(Product $product)
    {

            $totalDescuento  =  ($product->price * $product->descuento) /100;
            $precioConDescuento =   $product->price - $totalDescuento;
            $precioSinDescuento = $precioConDescuento + $totalDescuento;
            $impuestos = $product->iva + $product->ice;
            $pvpsindsto = $precioSinDescuento + $impuestos;
            $newDescuent =0.00;
            // dd('descuento:', $totalDescuento, 'precio con descuento:', $precioConDescuento,
            // 'precio sin descuento:', $precioSinDescuento, 'impuestos:', $impuestos, 'pvp sin descuento:', $pvpsindsto);


        //$affectedRows = Product::where('id', '=', $product->id)->update(['price2' => $pvp, 'descuento' => $newDescuent]);
        Product::where('id', '=', $product->id)->update(['price2' => $pvpsindsto, 'descuento'=> $newDescuent]);

        $this->noty('Se eliminó el descuento');

    }

    public function noty($msg, $eventName='noty', $reset =  true, $action = '')
    {
        $this->dispatchBrowserEvent($eventName, ['msg' => $msg, 'type' => 'success', 'action' => $action]);
        //if($reset) $this->resetUI();
    }

    public $listeners = ['Revoque'];
}
