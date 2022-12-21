<?php

namespace App\Http\Livewire;

use App\Models\Arqueo;
use App\Models\Caja;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Arqueos extends Component
{
    use WithPagination;

    public $caja_id='', $user_id ='', $monto_inicial=0, $monto_final=0, $total=0, $observaciones='',$selected_id=0;
    public $action = 'Listado', $componentName='LISTADO DE ARQUEOS DE CAJA', $search, $form = false;
    private $pagination =15;
    protected $paginationTheme='tailwind';


    public function render()
    {
        if(strlen($this->search) > 0){
            $arqueos = Arqueo::join('users as u','u.id','arqueos.user_id')
            ->select('arqueos.*','u.name as usuario')
            ->where('arqueos.created_at','like',"%{$this->search}%")
            ->where('arqueos.user_id', Auth()->user()->id)
            ->orderBy('created_at','desc')
            ->paginate($this->pagination);
        }
        else{
            $arqueos = Arqueo::join('users as u','u.id','arqueos.user_id')
            ->select('arqueos.*','u.name as usuario')
            ->where('arqueos.user_id', Auth()->user()->id)
            ->orderBy('created_at','desc')
            ->paginate($this->pagination);
        }
        return view('livewire.arqueos.component', [
            'arqueos' => $arqueos
        ]
        )->layout('layouts.theme.app');;
    }

    public function noty($msg, $eventName = 'noty', $reset = true, $action =""){
        $this->dispatchBrowserEvent($eventName, ['msg'=>$msg, 'type' => 'success', 'action' => $action ]);
        if($reset) $this->resetUI();
    }

    public  function resetUI()
    {
        $this->resetValidation();
        $this->resetPage();
        $this->reset('caja_id','monto_inicial','monto_final','total','observaciones', 'selected_id','search','componentName', 'user_id','form');
    }



    public function Arqueo($valorFinal, $observaciones)
    {

        $totVentas  =  Arr::get($this->totalVentas(), 'totalVenta');
        $caja_id = Arr::get($this->totalVentas(), 'caja_id');
        //dd($totVentas, $caja_id);

        // fecha de inicio del arqueo ->created_at fecha fin now para sacar el total de ventas
       $arqueo  = Arqueo::where('id', $this->selected_id)
            ->update([
                'monto_final' => $valorFinal,
                'total' => $totVentas,
                'fecha_cierre' =>  Carbon::now(),
                'observaciones' => $observaciones
            ]);

            if($arqueo){
                Caja::where('id', $caja_id) //sacar caja id
                ->update(['status' => 0]);
            }
        $this->dispatchBrowserEvent('close-modal-cierre');
        $this->noty("ARQUEO CAJA GENERADO CON EXITO");

    }

    public function totalVentas()
    {
        $arqueo =  Arqueo::find($this->selected_id);
        $fechaIni = $arqueo->created_at;
        $fechaFin =  Carbon::now();
        // y ventas del usuario de caja sea igual al usuario que cierra caja
        $totalVenta = Order::where('user_id', Auth()->user()->id)
        ->whereBetween('created_at', [$fechaIni,$fechaFin])->sum('total');
        return ( [ 'totalVenta' => $totalVenta, 'caja_id' => $arqueo->caja_id  ]);
        //dd(Arr::add($data, $totalVenta, $arqueo->caja_id ));
    }
}
