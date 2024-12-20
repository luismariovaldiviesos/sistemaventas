<?php

namespace App\Http\Livewire;

use App\Models\Factura;
use App\Models\XmlFile;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class XmlFiles extends Component
{


    use WithPagination;
    use WithFileUploads;


    public $fact_id='', $secuencial ='', $customer='', $directorio='', $estado;
    public $action = 'Listado', $componentName='FACTURAS PENDIENTES DE PROCESAR', $search, $form = false;
    private $pagination =15;
    protected $paginationTheme='tailwind';

    // public function render()
    // {
    //     $facturas = XmlFile::where('estado', '!=', 'aprobado')->get();
    //     return view('livewire.reprocesar.component', compact('facturas' ))->layout('layouts.theme.app');
    // }
    public function render()
    {
        if (strlen($this->search) > 0)
            $info = XmlFile::where('secuencial', 'like', "%{$this->search}%")->paginate($this->pagination);
        else
            $info = XmlFile::where('estado','!=','autorizado')->paginate($this->pagination);


        return view('livewire.reprocesar.component', ['facturas' => $info])
            ->layout('layouts.theme.app');
    }



    public function retry(XmlFile $xml){

        $factura =  new Factura();

       try {
        // Según el estado actual, reprocesar
        switch($xml->estado){
            case 'creado':
                dd('volver a firma');
                break;

                case 'firmado':
                    dd('volver a enviar');
                    break;
        }




       } catch (\Throwable $th) {
        //throw $th;
       }

    }
}
