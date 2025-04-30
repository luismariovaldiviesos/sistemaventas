<?php

namespace App\Http\Livewire;

use App\Models\Arqueo;
use App\Models\Caja;
use App\Models\Factura;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\Storage;

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
        $totalVenta = Factura::where('user_id', Auth()->user()->id)
        ->whereBetween('created_at', [$fechaIni,$fechaFin])->sum('total');
        return ( [ 'totalVenta' => $totalVenta, 'caja_id' => $arqueo->caja_id  ]);
        //dd(Arr::add($data, $totalVenta, $arqueo->caja_id ));
    }

    public function downloadArqueo(Arqueo $arqueo)
    {
        $url =  route('descargar-arqueo', ['arqueo'=>$arqueo->id]);
        return redirect()->to($url);
        //dd($arqueo);
        //crear pdf
        // Crear PDF
    // $pdf = new Fpdf();
    // $pdf->AddPage();
    // $pdf->SetFont('Arial', 'B', 14);
    // $pdf->Cell(0, 10, 'Reporte de Arqueo de Caja', 0, 1, 'C');

    // $pdf->SetFont('Arial', '', 12);
    // $pdf->Ln(5);

    // $pdf->Cell(50, 10, 'ID Arqueo:', 0, 0);
    // $pdf->Cell(50, 10, $arqueo->id, 0, 1);

    // $pdf->Cell(50, 10, 'Usuario:', 0, 0);
    // $pdf->Cell(50, 10, $arqueo->user->name ?? '---', 0, 1);

    // $pdf->Cell(50, 10, 'Caja:', 0, 0);
    // $pdf->Cell(50, 10, $arqueo->caja->nombre ?? '---', 0, 1);

    // $pdf->Cell(50, 10, 'Fecha de apertura:', 0, 0);
    // $pdf->Cell(50, 10, $arqueo->created_at, 0, 1);

    // $pdf->Cell(50, 10, 'Fecha de cierre:', 0, 0);
    // $pdf->Cell(50, 10, $arqueo->fecha_cierre ?? '---', 0, 1);

    // $pdf->Cell(50, 10, 'Total en caja:', 0, 0);
    // $pdf->Cell(50, 10, '$' . number_format($arqueo->total, 2), 0, 1);

    //  // Obtener el contenido del PDF
    //  $pdfContent = $pdf->Output('S');
    //     //dd($pdfContent);
    //  // Forzar descarga
    //  return response($pdf->Output('D',$arqueo->id.'.pdf'));


    }
}
