<?php

namespace App\Http\Livewire;

use App\Models\Factura;
use App\Models\XmlFile;
use Exception;
use Illuminate\Support\Facades\Log;
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
        $fac = Factura::where('id', '=', $xml->factura_id)->first();
         $nombre_fact_xml = $fac->customer->valueidenti.'_'.$fac->secuencial;
         $estado = strtolower(trim($xml->estado));
        //dd($estado);
        //para el envio de la factura de neuv
        $ruta_creados =  base_path('storage/app/comprobantes/no_firmados/');
        $ruta_si_firmados =  base_path('storage/app/comprobantes/firmados/');
        $ruta_enviados  =  base_path('storage/app/comprobantes/enviados/');
        $nombre_fact_xml_firmada =  $nombre_fact_xml . '.xml';
        //dd($nombre_fact_xml_firmada);
        //$claveAcces = simplexml_load_file($ruta_si_firmados . $nombre_fact_xml_firmada);
        //dd($claveAcces);
        try {
            if ($estado === 'creado')
             {
                //$archivo_xml_x_firmar =  file_get_contents($ruta_creados .  $nombre_fact_xml);
                $factura->firmarFactura($nombre_fact_xml, $fac->id);
            //dd('firmar de nuevo ');
            }
            elseif ($estado === 'firmado') {
            //$this->reenviarFactura($fac->id);
            //dd('enviar de nuevo ');
            $archivo_xml_firmado =  file_get_contents($ruta_si_firmados .  $nombre_fact_xml_firmada);
            $data = base64_encode($archivo_xml_firmado);
            $obj = new \StdClass();
            $obj->base64 = $data;
            $fac->recibir($obj, $nombre_fact_xml_firmada, $archivo_xml_firmado, $xml->factura_id);

            }
            elseif ($estado === 'enviado') {
                $archivo_xml_enviado =  file_get_contents($ruta_enviados .  $nombre_fact_xml_firmada);
                $data = base64_encode($archivo_xml_enviado);
                $obj = new \StdClass();
                $obj->base64 = $data;
                $fac->fetch($obj, $nombre_fact_xml_firmada, $archivo_xml_enviado, $xml->factura_id);

                }

            else {
            //throw new Exception("Estado no reconocido: $estado");
            dd('no hay estado  de neuvo ');
            }
        } catch (\Throwable $th) {
            // dd('error');
            // Log::error("Error al reprocesar factura: " . $th->getMessage());
            // return response()->json(['error' => 'Ocurrió un error durante el reprocesamiento.'], 500);
            $this->noty('Error al guardar el pedido: ' . $th->getMessage(), 'noty', 'error');
            //$this->noty('no se ha podido reprocesar la factura', 'noty', true);

        }


    }



    public function noty($msg, $eventName = 'noty', $reset = true, $action =""){
        $this->dispatchBrowserEvent($eventName, ['msg'=>$msg, 'type' => 'success', 'action' => $action ]);

        //if($reset) $this->resetUI();
    }

}
