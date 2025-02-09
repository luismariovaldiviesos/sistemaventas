<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;


class Diario extends Component
{

    public $numVentas, $totVentas, $day, $clientes  ;


    public function render()
    {
        $this->day =  Carbon::now()->format('d-m-Y');
        $this->getDiario();
        return view('livewire.diarios.component')->layout('layouts.theme.app');
    }


    public function getDiario()
    {

        $from = Carbon::now()->format('Y-m-d') . ' 00:00:00';
        $to = Carbon::now()->format('Y-m-d') . ' 23:59:59';
        if (Auth()->user()->profile == 'Admin') { // si es admin veo todas las ventas diarias

            $orders = Order::whereBetween('created_at', [$from, $to]);
        }
        else{

        $orders = Order::where('orders.user_id', Auth()->user()->id)
        ->whereBetween('created_at', [$from, $to]);
        }
        $this->clientes = Customer::all()->count();
        $this->numVentas = $orders->count();
        $this->totVentas  =  number_format($orders->sum('total'),2);


    }




}
