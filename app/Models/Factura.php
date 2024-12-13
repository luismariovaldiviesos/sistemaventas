<?php

namespace App\Models;

use App\Traits\FuncionesTrait;
use App\Traits\PdfTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DOMDocument;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Unique;
use phpseclib3\File\X509 as FileX509;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use phpseclib\File\X509;
use phpseclib\Crypt\RSA;
use nusoap_client;
use Exception;
use PhpParser\Node\Stmt\Break_;
//require_once "/vendor/econea/nusoap/src/nusoap.php";
use soap_server;

class Factura extends Model
{
    use HasFactory;
    use WithFileUploads;
    use FuncionesTrait;
    use PdfTrait;

    //const $PASSCERTIFICADO =  Certificado::first('pass');

    protected $fillable = ['secuencial','numeroAutorizacion','fechaAutorizacion','codDoc','claveAcceso','customer_id',
                            'user_id','subtotal','subtotal0','subtotal12','ice','descuento','iva12','total','formaPago'
                            ];


    // validaciones

    public static function rules($id)
    {
        if($id < 0) // crear
        {
            return[

                'secuencial' => 'required',
                'codDoc' => 'required',
                'claveAcceso'=> 'required',
                'customer_id'=> 'required',
                'user_id'=> 'required',
                // 'subtotal'=> 'required',
                // 'subtotal0'=> 'required',
                // 'subtotal12'=> 'required',
                // 'ice'=> 'required',
                // 'iva12'=> 'required',
                // 'total'=> 'required',
                // 'formaPago'=> 'required',

            ];

        }
    }

    public static $messages =[
        'user_id.required' => 'usuario requerido',
        'customer_id.required' => 'cliente es requerido',
        'codDoc.required' => 'Código es  requerido',
        'claveAcceso.required' => 'calve de acceso  requerido'
        // 'subtotal.required' => 'subtotal  requerido',
        // 'subtotal0.required' => 'subtotal0  requerido',
        // 'subtotal12.required' => 'subtotal12  requerido',
        // 'ice.required' => 'ice  requerido',
        // 'descuento.required' => 'descuento  requerido',
        // 'iva12.required' => 'iva12  requerido',

    ];


    // empresa para sacar datos y formar la clave de acceo

    public function empresa ()
    {
        $empresa =  Setting::get();
        return $empresa;
    }

    public function claveAcceso()
    {
         $fecha =  Carbon::now()->format('dmY'); //1
         $codigo  = "1"; //2
         $parteUno = $fecha.$codigo;   //1+2***********
         $empresa =  $this->empresa();
         $ruc =  $empresa[0]->ruc;  //3
         $ambiente  =  $empresa[0]->ambiente;  //4
         $establecimiento =  $empresa[0]->estab;
         $puntoEmi  =  $empresa[0]->ptoEmi;
         $serie  = $establecimiento.$puntoEmi;  //5
         $parteDos =  $ruc.$ambiente.$serie;  // 3 4 y 5***********
         $cadenaUNo = $parteUno.$parteDos;   /// 1 al 5 *********************
         $secuencial =  $this->secuencial(); //6  aqui hay errror por que suma un digito mas al secuencial y la clave acceo en el xml se forma mal
         $codigoNumerico  =substr($secuencial,-8);  // secuencial 8 desde la derecha
         $tipoEmi  = "1";   //8
         $cadenaDos  = $cadenaUNo.$secuencial.$codigoNumerico.$tipoEmi;   // 1 al 8   **********
         $dig  =  $this->getMod11Dv($cadenaDos);
         $claveFinal = $cadenaDos.$dig;
        return $claveFinal ;
       //return $cadena;
    }

    public function generaClave($param){
        $claveArray = [];
        /*
         * Generar con ceros la tabla de hasta 49 posiciones
         */
        for($x=0;$x<49;$x++) {
          $claveArray[$x] = 0;
        }
        /*
         * Proceso de convertir cada campo en array para adicionar a la array de la clave
         */

        $args['tabla'] = $param['fecha'];
        $args['posini'] = 0;
        $args['posfin'] = 7;
        $args['claveArray'] = $claveArray;
        $claveArray = $this->haceArray($args);
        //echo 'Pasa fecha';

        $args['tabla'] = $param['tipodoc'];
        $args['posini'] = 8;
        $args['posfin'] = 9;
        $args['claveArray'] = $claveArray;
        $claveArray = $this->haceArray($args);
        //echo 'Pasa tipo documento';

        $args['tabla'] = $param['ruc'];
        $args['posini'] = 10;
        $args['posfin'] = 22;
        $args['claveArray'] = $claveArray;
        $claveArray = $this->haceArray($args);
        //echo 'Pasa ruc';


        $args['tabla'] = $param['ambiente'];
        $args['posini'] = 23;
        $args['posfin'] = 23;
        $args['claveArray'] = $claveArray;
        $claveArray = $this->haceArray($args);



        $args['tabla'] = $param['establecimiento'];
        $args['posini'] = 24;
        $args['posfin'] = 26;
        $args['claveArray'] = $claveArray;
        $claveArray = $this->haceArray($args);



        $args['tabla'] = $param['punto'];
        $args['posini'] = 27;
        $args['posfin'] = 29;
        $args['claveArray'] = $claveArray;
        $claveArray = $this->haceArray($args);



        $args['tabla'] = $param['factura'];
        $args['posini'] = 30;
        $args['posfin'] = 38;
        $args['claveArray'] = $claveArray;
        $claveArray = $this->haceArray($args);



        $args['tabla'] = $param['codigo'];
        $args['posini'] = 39;
        $args['posfin'] = 46;
        $args['claveArray'] = $claveArray;
        $claveArray = $this->haceArray($args);



        $args['tabla'] = $param['emision'];
        $args['posini'] = 47;
        $args['posfin'] = 47;
        $args['claveArray'] = $claveArray;
        $claveArray = $this->haceArray($args);
        $digito = $this->poneDigito($claveArray);
        $claveArray[48] = $digito;
        return $claveArray;

    }


    public function haceArray($param)  {

            //    echo 'Viene ';
    //    var_dump($param);
        $paso = str_split($param['tabla']);

        $j = count($paso) - 1;
        $posini = $param['posini'];
        $posfin = $param['posfin'];
        $claveArray = $param['claveArray'];
        $flag = TRUE;
        while ($flag)
        {
            if($posfin >= $posini){
    //        echo 'Esto tiene ini ' . $posini . ' Esto tiene fin ' . $posfin;
            if ($j >= 0) {
                $claveArray[$posfin] = $paso[$j];
                $j--;
            }
            $posfin--;
            } else {
                $flag = FALSE;
            }
        }
        return $claveArray;

    }

    public function poneDigito($param) {
        $posfin = 47;
        $flag = TRUE;
        $j = 2;
        $suma = 0;
        while ($flag) {
            if ($posfin >= 0) {
                $suma = $suma + ($param[$posfin] * $j);
    //            echo $suma;
                $j++;
                if ($j > 7) {
                    $j = 2;
                }
                $posfin--;
            } else {
                $flag = FALSE;
            }
        }
    //    echo 'Esta es la suma ' . $suma;
        $tienecero = $suma % 11;
        if ($tienecero == 0){
            $digito = 0;
        } else {
            $digito = 11 - ($suma % 11);
        }
    //    echo '<br>Este es el digito verificador ' . $digito;
        return $digito;
    }



    public  function secuencial ()
    {

        // Consultar si la tabla de facturas está vacía
        $ultimaFactura = Factura::latest('secuencial')->first();

        if ($ultimaFactura == null) {
            $numeroSecuencial = 1;
        } else {
            $numeroSecuencial = intval($ultimaFactura->secuencial) + 1;
        }

        // Formatear el número secuencial con ceros a la izquierda y asegurarse de que no exceda 9 dígitos
        $numeroFormateado = str_pad($numeroSecuencial, 9, '0', STR_PAD_LEFT);
        $numeroFormateado = substr($numeroFormateado, -9); // Limitar a 9 dígitos

        return $numeroFormateado;
    }

    public function getMod11Dv($num)
    {

        $digits = str_replace( array( '.', ',' ), array( ''.'' ), strrev($num ) );
        if ( ! ctype_digit( $digits ) )
        {
          return false;
        }

        $sum = 0;
	    $factor = 2;
        for( $i=0;$i<strlen( $digits ); $i++ )
        {
          $sum += substr( $digits,$i,1 ) * $factor;
          if ( $factor == 7 )
          {
            $factor = 2;
          }else{
           $factor++;
         }
        }
        $dv = 11 - ($sum % 11);
        if ( $dv == 10 )
	  {
	    return 1;
	  }
	  if ( $dv == 11 )
	  {
	    return 0;
	  }
	  return $dv;
    }



    // public function xmlFactura($fecha,$correo,$secuencial,$codigo,$cantidad,$descripcion,
    //                          $preciou,$descuento,$preciot,$subtotal,$iva12,$total)
    public function xmlFactura($tipoIdentificadorCli, $razonSocialCli, $identificadorCliente,$direccionCliente,
     $totalSinImpuesto, $totalDescuento,$subtotaliva12, $totalIva12,$totalFactura, $detalles,$secuencia,$claveAcce)
    {
        $empresa = $this->empresa();
        //$ultimaFactura = Factura::latest()->first();
        //$secuencial = $ultimaFactura->secuencial;
        $xml =  new DOMDocument('1.0','utf-8');
        $xml->formatOutput = true;
        //PRIMERA PARTE
		$xml_fac = $xml->createElement('factura');
		$cabecera = $xml->createAttribute('id');
		$cabecera->value = 'comprobante';
		$cabecerav = $xml->createAttribute('version');
		$cabecerav->value = '1.0.0';
		$xml_inf = $xml->createElement('infoTributaria');
		$xml_amb = $xml->createElement('ambiente',$empresa[0]->ambiente);
		$xml_tip = $xml->createElement('tipoEmision',$empresa[0]->tipoEmision);
		$xml_raz = $xml->createElement('razonSocial',$empresa[0]->razonSocial);
		$xml_nom = $xml->createElement('nombreComercial',$empresa[0]->nombreComercial);
		$xml_ruc = $xml->createElement('ruc',$empresa[0]->ruc);
		//$fechasf=date('dmY'); /// ******************revisar***************
		// $dig = new modulo();
		// $clave_acceso=$fechasf.'01010376784400110010010000'.$secuencial.'123456781';
		$xml_cla = $xml->createElement('claveAcceso',$claveAcce);
		$xml_doc = $xml->createElement('codDoc','01');  ///simpre va a ser 01 porque es fact
		$xml_est = $xml->createElement('estab',$empresa[0]->estab);
		$xml_emi = $xml->createElement('ptoEmi',$empresa[0]->ptoEmi);
		$xml_sec = $xml->createElement('secuencial',$secuencia);
		$xml_dir = $xml->createElement('dirMatriz',$empresa[0]->dirMatriz);


        $xml_def = $xml->createElement('infoFactura');
		$xml_fec = $xml->createElement('fechaEmision',date('d/m/Y'));
		$xml_des = $xml->createElement('dirEstablecimiento',$empresa[0]->dirEstablecimiento);
		$xml_con = $xml->createElement('contribuyenteEspecial',$empresa[0]->contribuyenteEspecial);
		$xml_obl = $xml->createElement('obligadoContabilidad',$empresa[0]->obligadoContabilidad);
		$xml_ide = $xml->createElement('tipoIdentificacionComprador', $tipoIdentificadorCli);
		$xml_rco = $xml->createElement('razonSocialComprador', $razonSocialCli);
		$xml_idc = $xml->createElement('identificacionComprador', $identificadorCliente);
        $xml_dir_cli = $xml->createElement('direccionComprador',$direccionCliente);
		$xml_tsi = $xml->createElement('totalSinImpuestos', $totalSinImpuesto);// total de factura ok
		$xml_tds = $xml->createElement('totalDescuento', $totalDescuento);// ttal de descuento factura ok


        //SEGUNDA PARTE 2.2
		$xml_imp = $xml->createElement('totalConImpuestos');// inicio de impuestos ok
        // iva 12%
		$xml_tim = $xml->createElement('totalImpuesto');// ok iva 12
		$xml_tco = $xml->createElement('codigo','2');// ok codi¿go impuesto 2  = iva
		$xml_cpr = $xml->createElement('codigoPorcentaje','4');// ok codigo porcentaje iva 12 = 2  iva al 15 = 4
		$xml_bas = $xml->createElement('baseImponible',$subtotaliva12); // ok subtotal iva 12
		$xml_val = $xml->createElement('valor', $totalIva12);// total impuesto 12 %  round($amount, 2);

        //REVISAR AQUI EL ICE

        //PARTE 2.3
		$xml_pro = $xml->createElement('propina','0.00');  // ok
		$xml_imt = $xml->createElement('importeTotal',$totalFactura);// ok
		$xml_mon = $xml->createElement('moneda','DOLAR');// ok

		//PARTE PAGOS
		$xml_pgs = $xml->createElement('pagos');//ok
		$xml_pag = $xml->createElement('pago');// ok
		$xml_fpa = $xml->createElement('formaPago','01');// efectivo ok
		$xml_tot = $xml->createElement('total',$totalFactura);// ok
		$xml_pla = $xml->createElement('plazo','90');// ok
		$xml_uti = $xml->createElement('unidadTiempo','dias');// ok
        $xml_dts = $xml->createElement('detalles');
        foreach ($detalles as $d) {
            $xml_det = $xml->createElement('detalle');
            $xml_cop = $xml->createElement('codigoPrincipal', $d['id']);
            $xml_dcr = $xml->createElement('descripcion', $d['name']);
            $xml_can = $xml->createElement('cantidad', $d['qty']);
            $xml_pru = $xml->createElement('precioUnitario', round($d['price'], 2));
            $xml_dsc = $xml->createElement('descuento', $d['descuento']);
            $xml_tsm = $xml->createElement('precioTotalSinImpuesto', round($d['price'], 2));
            $xml_ips = $xml->createElement('impuestos');
            $xml_ipt = $xml->createElement('impuesto');
            $xml_cdg = $xml->createElement('codigo', '2');
            $xml_cpt = $xml->createElement('codigoPorcentaje', '4');
            $xml_trf = $xml->createElement('tarifa', '15');
            $xml_bsi = $xml->createElement('baseImponible',round($d['price'], 2));
            $xml_vlr = $xml->createElement('valor', round($d['price'], 2));

            $xml_det->appendChild($xml_cop);
            $xml_det->appendChild($xml_dcr);
            $xml_det->appendChild($xml_can);
            $xml_det->appendChild($xml_pru);
            $xml_det->appendChild($xml_dsc);
            $xml_det->appendChild($xml_tsm);
            $xml_det->appendChild($xml_ips);

            $xml_ips->appendChild($xml_ipt);
            $xml_ipt->appendChild($xml_cdg);
            $xml_ipt->appendChild($xml_cpt);
            $xml_ipt->appendChild($xml_trf);
            $xml_ipt->appendChild($xml_bsi);
            $xml_ipt->appendChild($xml_vlr);

            $xml_dts->appendChild($xml_det);
        }

        // Finalmente, agregar el elemento 'detalles' al XML
        $xml->appendChild($xml_dts);
        $xml_ifa = $xml->createElement('infoAdicional');//ok
		$xml_cp1 = $xml->createElement('campoAdicional',$empresa[0]->email);// ok
		$atributo = $xml->createAttribute('nombre');// ******************revisar***************
		$atributo->value = 'email';// ******************revisar***************
         //PRIMERA PARTE
		$xml_inf->appendChild($xml_amb);
		$xml_inf->appendChild($xml_tip);
		$xml_inf->appendChild($xml_raz);
		$xml_inf->appendChild($xml_nom);
		$xml_inf->appendChild($xml_ruc);
		$xml_inf->appendChild($xml_cla);
		$xml_inf->appendChild($xml_doc);
		$xml_inf->appendChild($xml_est);
		$xml_inf->appendChild($xml_emi);
		$xml_inf->appendChild($xml_sec);
		$xml_inf->appendChild($xml_dir);
		$xml_fac->appendChild($xml_inf);

		//SEGUNDA PARTE
		$xml_def->appendChild($xml_fec);
		$xml_def->appendChild($xml_des);
		//$xml_def->appendChild($xml_con);
		$xml_def->appendChild($xml_obl);
		$xml_def->appendChild($xml_ide);
		$xml_def->appendChild($xml_rco);
		$xml_def->appendChild($xml_idc);
		$xml_def->appendChild($xml_dir_cli);
		$xml_def->appendChild($xml_tsi);
		$xml_def->appendChild($xml_tds);
		$xml_def->appendChild($xml_imp);
		$xml_imp->appendChild($xml_tim);
		$xml_tim->appendChild($xml_tco);
		$xml_tim->appendChild($xml_cpr);
		$xml_tim->appendChild($xml_bas);
		$xml_tim->appendChild($xml_val);
		$xml_fac->appendChild($xml_def);



		//SEGUNDA PARTE 2.3

		$xml_def->appendChild($xml_pro);
		$xml_def->appendChild($xml_imt);
		$xml_def->appendChild($xml_mon);



		$xml_def->appendChild($xml_pgs);
		$xml_pgs->appendChild($xml_pag);
		$xml_pag->appendChild($xml_fpa);
		$xml_pag->appendChild($xml_tot);
		$xml_pag->appendChild($xml_pla);
		$xml_pag->appendChild($xml_uti);



		$xml_fac->appendChild($xml_dts);
		$xml_dts->appendChild($xml_det);



		$xml_fac->appendChild($xml_ifa);
		$xml_ifa->appendChild($xml_cp1);
		$xml_cp1->appendChild($atributo);





		$xml_fac->appendChild($cabecera);
		$xml_fac->appendChild($cabecerav);
		$xml->appendChild($xml_fac);
        //Se eliminan espacios en blanco
        $xml->preserveWhiteSpace = false;
        //Se ingresa formato de salida
        $xml->formatOutput = true;
        //Se instancia el objeto
        $xml_string =$xml->saveXML();
        //nombre del archivo
        $name = $identificadorCliente.'_'.$secuencia.'.xml'; // nombre de la imagen
        //Y se guarda en el nombre del archivo 'achivo.xml', y el obejto nstanciado
       $path = Storage::disk('comprobantes/no_firmados')->put($name,$xml_string);
        //dd($path);
        XmlFile::create([
            'name' => $secuencia,
            'status' => 'no_fimado',
            'path' => $path
        ]);



    }

    public function firmarUltimaFactura()
    {
        //obtener el id de la ultima factura
        $facturaId = $this->getLastTicket();
        // dd($facturaId);
        // Verificar si hay facturas sin firmar
        // if (!$facturaId) {
        //     return 'No hay facturas sin firmar.';
        // }
         // Llamar al método para firmar la factura utilizando el ID obtenido
         $resultadoFirma = $this->firmarFactura($facturaId);

        dd(gettype($resultadoFirma));
        return $resultadoFirma;
    }

    public function firmarFactura($facturaId)
    {

        // variables generales para autorizar xml sri
        $vtipoambiente=1;
        $wsdls = $this->wsdl($vtipoambiente);
        $recepcion = $wsdls['recepcion'];
        $autorizacionws = $wsdls['autorizacion'];

        //RUTAS PARA LOS ARCHIVOS XML
        //ruta de la factura
        $ruta_no_firmados =  base_path('storage/app/comprobantes/no_firmados/'.$facturaId.'.xml');
        //dd($ruta_no_firmados);
         // Ruta donde se guardará el archivo firmado
         $ruta_si_firmados =  base_path('storage/app/comprobantes/firmados/');

         // ruta enviados
         $ruta_enviados =  base_path('storage/app/comprobantes/enviados/');


         //pdfs
         $pathPdf = base_path('storage/app/comprobantes/pdf/');

         //varaiblers varias
        $tipo='FV';
        $controlError = false;
        $m = '';
        $show = '';




        //ruta del certifixcado
        // Ruta del certificado digital (archivo .pfx o .p12)
        $certPath = base_path('storage/app/certificados/P0000119207.p12');

        // Contraseña del certificado digital
        $certPass = 'Okz9UqnjX1';



        // Contenido del xml
        $factContent = file_get_contents($ruta_no_firmados);

        // Cargar el certificado digital
        $certStore = openssl_pkcs12_read(file_get_contents($certPath), $certs, $certPass);
        if (!$certStore) {
            dd('Error al cargar el certificado digital.');
        }
        // Extraer la clave privada y el certificado del almacenamiento
        $key = openssl_pkey_get_private($certs['pkey']);
        $cert = openssl_x509_read($certs['cert']);

       // dd($key,$cert);
       // Crear el objeto XMLSecurityDSig
        $xmlSecDSig = new XMLSecurityDSig();

        // Cargar el contenido del XML a firmar
        $xmlSecDSig->sigNode = $xmlSecDSig->createNewSignNode('signature', 'nodonuevo');
        $xmlSecDSig->idKeys = ['wsu:Id'];

        $doc = new DOMDocument();
        $doc->loadXML($factContent);

        //dd($doc);
        $nuevo_xml =  $facturaId . '.xml';
        $fac =  Factura::findOrFail($facturaId);
        // // Crear un nuevo objeto XMLSecurityKey a partir de la clave privada
        // $argumentos = $ruta_no_firmados . ' ' . $ruta_si_firmados . ' ' . $nuevo_xml . ' ' . $firma . ' ' . $clave;
        $argumentos = $ruta_no_firmados . ' ' . $ruta_si_firmados . ' ' . $nuevo_xml . ' ' . $certPath . ' ' . $certPass;
        $comando = ('java -jar C:\\Comprobantes\\firmaComprobanteElectronico\\dist\\firmaComprobanteElectronico.jar ' . $argumentos);

        try {
            $resp = shell_exec($comando);

        } catch (\Exception $e) {
            dd('Error al buscar java: ' . $e->getMessage());
        }

        $claveAcces = simplexml_load_file($ruta_si_firmados . $nuevo_xml);
        $claveAcceso = substr($claveAcces->infoTributaria[0]->claveAcceso, 0, 49);
        //dd($claveAcceso);
        //var_dump($claveAcceso);
        //var_dump($comando);
        //var_dump($resp);

        //dd($claveAcceso);

        // *********** DESDE AQUI INICIAMOS CON LA INSTALACION DE LA LIBRERIA ************************
        // *********** LIBRERIA NOSOAP INSTALADA ************************

        //

        $respuesta  =  substr($resp,0,7);
        ///dd($respuesta);
        switch($respuesta){


            case  'FIRMADO' :
                $xml_firmado =  file_get_contents($ruta_si_firmados .  $nuevo_xml);
                $data = base64_encode($xml_firmado);
                $obj = new \StdClass();
                $obj->key = $claveAcceso ;
                $obj->base64 = $data;
                $this->recibir($obj);
                //sleep(10);
                $respuestaSRI = $this->fetch($obj);
               return($respuestaSRI);
            break;
            default:
            dd('no se firmó el documento');

        }
    }

    // public  function generaPDF(){
    //     $this->pdfFactura('tiburcio@gmail.com');
    // }

    private function recibir($invoiceObj)
    {
        //Si es ambiente de desarrollo
        //Todo: Modificar el código del parametro depende de su sistema.
        $ambiente = '1';
        if ($ambiente == '1') {
            $host = 'https://celcer.sri.gob.ec';
        } else { //Si es producción
            $host = 'https://cel.sri.gob.ec';
        }


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $host . '/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ec="http://ec.gob.sri.ws.recepcion">
                <soapenv:Header/>
                <soapenv:Body>
                <ec:validarComprobante>
                    <xml>' . $invoiceObj->base64 . '</xml>
                </ec:validarComprobante>
            </soapenv:Body>
            </soapenv:Envelope>',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: text/xml',
                'Accept: text/xml',
                'SOAPAction: '
            ),
        ));

        $response = curl_exec($curl);
        file_put_contents("respuesta_sri.xml", $response);
        $code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        //dd(curl_close($curl));


        if ($code !== 200) {
            //throw new SriReceiveException('Sri está caido.');
            dd("sri caido");
        }

        $simpleXml = new \SimpleXMLElement($response);

        $estado = $simpleXml->xpath('//estado')[0];
        //dd($estado);

        if ('DEVUELTA' === (string)$estado) {
            $comprobante = $simpleXml->xpath('//comprobante')[0];
            //throw new SriReceiveException($comprobante->mensajes[0]->mensaje->mensaje, $comprobante->mensajes[0]->mensaje->informacionAdicional);
            dd($comprobante->mensajes[0]->mensaje->mensaje, $comprobante->mensajes[0]->mensaje->informacionAdicional);
        }

        //var_dump('fin de envio ' , $estado);
    }
    public function fetch($invoiceObj)
    {
         //autorizados

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
                <claveAccesoComprobante>' . $invoiceObj->key . '</claveAccesoComprobante>
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
            $xmlAprobado =    $this->crearXmlAutorizado($estado,$numeroAutorizacion,$vfechaauto,$comprobante,$comprobanteAutorizacion);
            //dd($estado,$numeroAutorizacion,$fechaAutorizacion, $vfechaauto, $comprobanteAutorizacion);
            return $xmlAprobado;

        }


    }


    public  function crearXmlAutorizado($estado,$numeroAutorizacion,$fechaAutorizacion,$comprobanteAutorizacion,$nuevo_xml){
        $ruta_autorizados = 'comprobantes/autorizados/'; // Ruta relativa dentro del storage
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
        //Y se guarda en el nombre del archivo 'achivo.xml', y el obejto nstanciado
        $ms =  Storage::disk('comprobantes/autorizados')->put($factura,$xml_string);
        //dd($this->claveAcceso());
        return $ms;

    }




    public function getLastTicket() { // obtiene la ultima factura generada en no_firmados

        $archivosNoFirmados = Storage::files('comprobantes\no_firmados');
        // Verificar si hay archivos en la carpeta
        if (empty($archivosNoFirmados)) {
            return 'No hay facturas sin firmar.';
        }
        // nombre del ultimo archivo generado
        $last = end($archivosNoFirmados);
        // Extraer el ID de la factura del nombre del archivo
        $rutaInfo = pathinfo($last);
        $nombreArchivo = $rutaInfo['basename'];
        $facturaId = substr($nombreArchivo, 0, -4); // Remover la extensión .xml
        return $facturaId;

    }

    public  function detalles (){

        return $this->hasMany(DetalleFactura::class);
    }


    public  function customer (){
        return $this->belongsTo(Customer::class);
    }





}
