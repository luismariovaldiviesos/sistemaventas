<?php

namespace App\Http\Livewire;

use App\Models\Factura;
use App\Models\XmlFile;
use Carbon\Carbon;
use DOMDocument;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class XmlFiles extends Component
{


    use WithPagination;
    use WithFileUploads;



    public $fact_id='', $secuencial ='', $customer='', $directorio='', $estado;
    public $action = 'Listado', $componentName='FACTURAS PENDIENTES DE PROCESAR', $search, $form = false;
    private $pagination =20;
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


        //dd($xml);
        $factura =  new Factura();
        $fac = Factura::where('id', '=', $xml->factura_id)->first();
         $nombre_fact_xml = $fac->customer->valueidenti.'_'.$fac->secuencial;
         $claveAccesoFactura =  $fac->claveAcceso;
         //dd($claveAccesoFactura);
         $estado = strtolower(trim($xml->estado));
        //dd($estado);
        //para el envio de la factura de neuv
        $ruta_creados =  base_path('storage/app/comprobantes/no_firmados/');
        $ruta_si_firmados =  base_path('storage/app/comprobantes/firmados/');

        $nombre_fact_xml_firmada =  $nombre_fact_xml . '.xml';
        $archivo_xml_firmado =  file_get_contents($ruta_creados .  $nombre_fact_xml_firmada);
        //dd($archivo_xml_firmado);
        $data = base64_encode($archivo_xml_firmado);
        $obj = new \StdClass();
        $obj->base64 = $data;
        try {
            if ($estado === 'creado')
             {
                $factura->firmarFactura($nombre_fact_xml, $fac->id);
                $this->updateFact($fac);

            }
            elseif ($estado === 'firmado')
             {
                //dd('reenviar factura y actualizar');
                $fac->recibir($obj, $nombre_fact_xml_firmada, $archivo_xml_firmado, $xml->factura_id);
                $fac->fetch($obj,$nombre_fact_xml_firmada,$archivo_xml_firmado,$xml->factura_id);
                $this->updateFact($fac);
            }
            elseif ($estado === 'enviado')
            {
                //dd('recuperar  y actualizar factura');
                //$fac->fetch($obj,$nombre_fact_xml_firmada,$archivo_xml_firmado,$xml->factura_id);
                $this->Refetch($claveAccesoFactura,$nombre_fact_xml_firmada,$xml->factura_id);
                $this->updateFact($fac);
            }
            else
             {
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


    public function updateFact(Factura $factura) {
        $factura->fechaAutorizacion =  Carbon::now();
        $factura->numeroAutorizacion =  $factura->claveAcceso;
        $factura->save();
        $url  =  route('descargar-pdf',['factura' => $factura->id]);
        $this->noty('FACTURA GENERADA  CORRECTAMENTE !!!!!!');
        return redirect()->to($url);
    }



    public function noty($msg, $eventName = 'noty', $reset = true, $action =""){
        $this->dispatchBrowserEvent($eventName, ['msg'=>$msg, 'type' => 'success', 'action' => $action ]);

        //if($reset) $this->resetUI();
    }

    public function Refetch($claveAcceso, $nombre_fact_xml_firmada,$factura_id) {

        //autorizados
        //dd('vamos a recupearar del srl ',$nombre_fact_xml_firmada);
       //dd($invoiceObj->key);
    $ruta_enviados  =  base_path('storage/app/comprobantes/enviados/');
     $archivo_xml_enviado =  file_get_contents($ruta_enviados .  $nombre_fact_xml_firmada);
    //dd($archivo_xml_enviado);
       $ambiente = "1";
       if ($ambiente == "1") {
           $host = 'https://celcer.sri.gob.ec';
       } else {
           $host = 'https://cel.sri.gob.ec';
       }

       $curl = curl_init();

       curl_setopt_array($curl, array(
           CURLOPT_URL => $host . '/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl',
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => '',
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 0,
           CURLOPT_FOLLOWLOCATION => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => 'POST',
           CURLOPT_POSTFIELDS => '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ec="http://ec.gob.sri.ws.autorizacion">
      <soapenv:Header/>
       <soapenv:Body>
           <ec:autorizacionComprobante>
               <claveAccesoComprobante>' . $claveAcceso . '</claveAccesoComprobante>
             </ec:autorizacionComprobante>
           </soapenv:Body>
         </soapenv:Envelope>',
           CURLOPT_HTTPHEADER => array(
               'Content-Type: text/xml',
               'Accept: text/xml',
               'SOAPAction: '
           ),
       ));

       $response = curl_exec($curl);
       //dd($response);
       file_put_contents("respuesta_sri_fetch.xml", $response);  // aqui iria enviados
       $code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

       curl_close($curl);

       if ($code !== 200) {
           //throw new SriAuthorizeException('Sri está caido.');
           dd("sri caiido en recuperacion");
       }
       //dd( substr($response, 0, 200)); // Imprime los primeros 200 caracteres de $response)
       // if (simplexml_load_string($response) === false) {
       //     throw new Exception('La respuesta no es un XML válido');
       // }
       try {
           $response_utf8 = utf8_encode($response);
           $simpleXml = new \SimpleXMLElement($response_utf8);
           $estado = $simpleXml->xpath('//estado')[0];
           //dd('estado del archivo recuperado del sri : ',$estado, $nombre_fact_xml_firmada);
       } catch (\Exception $e) {
           throw new Exception('Error al parsear el XML: ' . $e->getMessage());
           dd('Error al parsear el XML: ' . $e->getMessage());
       }
       if ('NO AUTORIZADO' === (string)$estado) {
           $comprobante = $simpleXml->xpath('//autorizacion')[0];
          // throw new SriAuthorizeException($comprobante->mensajes[0]->mensaje->mensaje, $comprobante->mensajes[0]->mensaje->informacionAdicional);
          dd($comprobante->mensajes[0]->mensaje->mensaje, $comprobante->mensajes[0]->mensaje->informacionAdicional);
       }
       if ('AUTORIZADO' === (string)$estado) {
           $comprobante = $simpleXml->xpath('//autorizacion')[0];
           $estado = $simpleXml->xpath('//estado')[0];
           $numeroAutorizacion = $simpleXml->xpath('//numeroAutorizacion')[0];
           $fechaAutorizacion = $simpleXml->xpath('//fechaAutorizacion')[0];
           $vfechaauto = substr($fechaAutorizacion, 0, 10) . ' ' . substr($fechaAutorizacion, 11, 5);
           $comprobanteAutorizacion=$simpleXml->xpath('//comprobante')[0];
           // aqui hay que llamar a la funcion xml autorizado *****************

           Storage::disk('comprobantes/autorizados')->put($nombre_fact_xml_firmada,$archivo_xml_enviado);
           //dd('autorizado:', $nombre_fact_xml_firmada,$archivo_xml_firmado);
          $xmlFile = XmlFile::where('factura_id', $factura_id)->firstOrFail();
          $xmlFile->update([
              'directorio' => 'comprobantes/autorizados',
              'estado' => 'autorizado'
          ]);
          $this->XmlAutorizado($estado,$numeroAutorizacion,$vfechaauto,$comprobante,
           $comprobanteAutorizacion, $factura_id);
           //dd($estado,$numeroAutorizacion,$fechaAutorizacion, $vfechaauto, $comprobanteAutorizacion);
         // return $xmlAprobado;

    }

    }

    public  function XmlAutorizado($estado,$numeroAutorizacion,$fechaAutorizacion,
    $comprobanteAutorizacion, $factura_id){
       //dd( $factura_id);
        $xml =  new DOMDocument();
        $xml_autor = $xml->createElement('autorizacion');
        $xml_estad = $xml->createElement('estado', $estado);
        $xml_nauto = $xml->createElement('numeroAutorizacion', $numeroAutorizacion);
        $xml_fauto = $xml->createElement('fechaAutorizacion', $fechaAutorizacion);
        $xml_compr = $xml->createElement('comprobante');
        $xml_autor->appendChild($xml_estad);
        $xml_autor->appendChild($xml_nauto);
        $xml_autor->appendChild($xml_fauto);
        $xml_compr->appendChild($xml->createCDATASection($comprobanteAutorizacion));
        $xml_autor->appendChild($xml_compr);
        $xml->appendChild($xml_autor);
        $xml->preserveWhiteSpace = false;
        //Se ingresa formato de salida
        $xml->formatOutput = true;
        //Se instancia el objeto
        $xml_string =$xml->saveXML();
        //nombre del archivo
        $secuencial =  substr($numeroAutorizacion,30,9);
        $factura = 'facturaNro'.'_'.$secuencial.'.xml'; // nombre de la imagen
        $factura = $secuencial.'.xml'; // nombre de la imagen
        //Y se guarda en el nombre del archivo 'achivo.xml', y el obejto nstanciado
        $ms =  Storage::disk('comprobantes/xmlaprobados')->put($factura,$xml_string);
        //dd($this->claveAcceso());
        //dd($factura,$xml_string);
        // $xmlFile = XmlFile::where('factura_id', $factura_id)->firstOrFail();
        // $xmlFile->update([
        //     'directorio' => 'comprobantes/xmlaprobados',
        //     'estado' => 'xmlaprobado'
        // ]);
        return $ms;

    }
    // necesitamos crear aqi el metodo de  recuperar del sri fetch y pasarle directo la clave de acceso .


}
