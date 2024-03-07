<?php

namespace App\Models;

use App\Traits\FuncionesTrait;
use App\Traits\PdfTrait;
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
        //  $ultimaFactura = Factura::latest()->first();
        // $secuencial = $ultimaFactura->secuencial;
        //dd($this->secuencial());
        $fecha =  Carbon::now()->format('dmY'); //1
        $codigo  = "01"; //2
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
        $codigoNumerico  = "00000001";  //7
        $tipoEmi  = "1";   //8
        $cadenaDos  = $cadenaUNo.$secuencial.$codigoNumerico.$tipoEmi;   // 1 al 8   **********
        $dig  =  $this->getMod11Dv($cadenaDos);
        $claveFinal = $cadenaDos.$dig;
        return $claveFinal ;
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

       // dd($secuencia,$claveAcce); LLEGA BIEN DESDE EL METODO DEL CONTROLLER
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
		$xml_cpr = $xml->createElement('codigoPorcentaje','2');// ok codigo porcentaje iva 12 = 2
		$xml_bas = $xml->createElement('baseImponible',$subtotaliva12); // ok subtotal iva 12
		$xml_val = $xml->createElement('valor',$totalIva12);// total impuesto 12 %

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

        // $detalles = array(
        //     array('producto_id' => 1, 'descripcion' => 'producto uno', 'cantidad' => 2, 'precioUnitario' => 2.00, 'descuento' => 0.00, 'total' => 2.00),
        //     array('producto_id' => 1, 'descripcion' => 'producto dos', 'cantidad' => 3, 'precioUnitario' => 4.00, 'descuento' => 0.00, 'total' => 12.00),
        //     array('producto_id' => 3, 'descripcion' => 'producto tres', 'cantidad' => 2, 'precioUnitario' => 2.00, 'descuento' => 0.00, 'total' => 8.00),
        //     array('producto_id' => 4, 'descripcion' => 'producto cuatro', 'cantidad' => 2, 'precioUnitario' => 8.00, 'descuento' => 0.00, 'total' => 16.00)
        // );

        foreach ($detalles as $d) {
            $xml_det = $xml->createElement('detalle');

            $xml_cop = $xml->createElement('codigoPrincipal', $d['id']);
            $xml_dcr = $xml->createElement('descripcion', $d['name']);
            $xml_can = $xml->createElement('cantidad', $d['qty']);
            $xml_pru = $xml->createElement('precioUnitario', $d['price']);
            $xml_dsc = $xml->createElement('descuento', $d['descuento']);
            $xml_tsm = $xml->createElement('precioTotalSinImpuesto', $d['price']);
            $xml_ips = $xml->createElement('impuestos');
            $xml_ipt = $xml->createElement('impuesto');
            $xml_cdg = $xml->createElement('codigo', '2');
            $xml_cpt = $xml->createElement('codigoPorcentaje', '2');
            $xml_trf = $xml->createElement('tarifa', '12');
            $xml_bsi = $xml->createElement('baseImponible',$d['price']);
            $xml_vlr = $xml->createElement('valor', $d['price2']);

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
        $factura = $identificadorCliente.'_'.$secuencia.'.xml'; // nombre de la imagen
        //Y se guarda en el nombre del archivo 'achivo.xml', y el obejto nstanciado
        Storage::disk('comprobantes/no_firmados')->put($factura,$xml_string);
        //dd($this->claveAcceso());


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

         //autorizados
         $ruta_autorizados = base_path('storage/app/comprobantes/autorizados/');

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
        // // Crear un nuevo objeto XMLSecurityKey a partir de la clave privada
        // $argumentos = $ruta_no_firmados . ' ' . $ruta_si_firmados . ' ' . $nuevo_xml . ' ' . $firma . ' ' . $clave;
        $argumentos = $ruta_no_firmados . ' ' . $ruta_si_firmados . ' ' . $nuevo_xml . ' ' . $certPath . ' ' . $certPass;
        $comando = ('java -jar C:\\Comprobantes\\firmaComprobanteElectronico\\dist\\firmaComprobanteElectronico.jar ' . $argumentos);

        try {
            $resp = shell_exec($comando);
            //dd($resp);
        } catch (\Exception $e) {
            dd('Error al buscar java: ' . $e->getMessage());
        }

        $claveAcces = simplexml_load_file($ruta_si_firmados . $nuevo_xml);
        $claveAcceso['claveAccesoComprobante'] = substr($claveAcces->infoTributaria[0]->claveAcceso, 0, 49);
        //dd($claveAcceso);
        var_dump($claveAcceso);
        var_dump($comando);
        var_dump($resp);

        //dd($claveAcceso);

        // *********** DESDE AQUI INICIAMOS CON LA INSTALACION DE LA LIBRERIA ************************
        // *********** LIBRERIA NOSOAP INSTALADA ************************

        //

        $respuesta  =  substr($resp,0,7);
        ///dd($respuesta);
        switch($respuesta){


            case  'FIRMADO' :
                dd('aqui inicia el envio al sri') ;

            default:
                dd('no se puede firmar el doc') ;
            break;

            // case 'FIRMADO' :
            //     $xml_firmado =  file_get_contents($ruta_si_firmados .  $nuevo_xml);
            //     //dd($xml_firmado);
            //     $data['xml'] =  base64_encode($xml_firmado);
            //    // dd($data);
            //     try {
            //         $client = new nusoap_client($recepcion, true);
            //         $client->soap_defencoding = 'utf-8';
            //         $client->xml_encoding = 'utf-8';
            //         $client->decode_utf8 = false;
            //         $response = $client->call('validarComprobante', $data);
            //         //dd($response);


            //     } catch (\Exception $e) {
            //         echo "Error!<br />";
            //         echo $e->getMessage();
            //         echo 'Last response: ' . $client->response . '<br />';
            //         var_dump($client->debug_str);
            //     }

            //     $response =  $response["RespuestaRecepcionComprobante"]["estado"];
            //     //dd($response);
            //     switch ($response) {
            //         case false:
            //             dd("me parece que es error del sri ");
            //         break;
            //         case 'RECIBIDA':
            //            $client =  new nusoap_client($autorizacionws, true);
            //            $client->soap_defencoding = 'utf-8';
            //            $client->xml_encoding = 'utf-8';
            //            $client->decode_utf8 = false;
            //             try {
            //                 $responseAut = $client->call('autorizacionComprobante', $claveAcceso);
            //             } catch (\Exception $e) {
            //                 echo "Error!<br>";
            //                       echo $e->getMessage();
            //                       echo 'Last response: ' . $client->response . '<br />';
            //             }
            //             //dd($responseAut);
            //             switch ($responseAut['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado']) {

            //                 case 'AUTORIZADO':
            //                     $autorizacion = $responseAut['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion'];
            //                     $estado = $autorizacion['estado'];
            //                             $numeroAutorizacion = $autorizacion['numeroAutorizacion'];
            //                             $fechaAutorizacion = $autorizacion['fechaAutorizacion'];
            //                             $comprobanteAutorizacion = $autorizacion['comprobante'];
            //                             echo '<script>alert("COMPROBANTE AUTORIZADO Y ENVIADO AL CORREO");location.href="../vistas/index.php";</script>';
            //                             $vfechaauto = substr($fechaAutorizacion, 0, 10) . ' ' . substr($fechaAutorizacion, 11, 5);

            //                         //**********CREAR XML AUTORIZADO Y ENVIAR CORREO ******* */

            //                             // $func->crearXmlAutorizado($estado, $numeroAutorizacion, $fechaAutorizacion, $comprobanteAutorizacion, $ruta_autorizados, $nuevo_xml);
            //                             // $pdf = new pdf();
            //                             // $pdf->pdfFactura($correo);
            //                             // $func->correos($correo);
            //                         //**********CREAR XML AUTORIZADO Y ENVIAR CORREO ******* */
            //                 break;
            //                 case 'EN PROCESO':
            //                             echo "El comprobante se encuentra EN PROCESO:<br>";
            //                             echo $responseAut['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion']['estado'] . '<br>';
            //                             $m .= 'El documento se encuentra en proceso<br>';
            //                             $controlError = true;
            //                 break;
            //                 default:
            //                 if ($responseAut['RespuestaAutorizacionComprobante']['numeroComprobantes'] == "0") {
            //                     echo 'No autorizado</br>';
            //                     echo 'No se encontro informacion del comprobante en el SRI, vuelva an enviarlo.</br>';
            //                 } else if ($responseAut['RespuestaAutorizacionComprobante']['numeroComprobantes'] == "1") {
            //                     echo $responseAut['RespuestaAutorizacionComprobante']["autorizaciones"]["autorizacion"]["estado"].'</br>';
            //                     echo $responseAut['RespuestaAutorizacionComprobante']["autorizaciones"]["autorizacion"]["mensajes"]["mensaje"]["mensaje"].'</br>';
            //                     if(isset($responseAut['RespuestaAutorizacionComprobante']["autorizaciones"]["autorizacion"]["mensajes"]["mensaje"]["mensaje"]["informacionAdicional"])){
            //                         echo $responseAut['RespuestaAutorizacionComprobante']["autorizaciones"]["autorizacion"]["mensajes"]["mensaje"]["mensaje"]["informacionAdicional"].'</br>';
            //                         $ms = $responseAut['RespuestaAutorizacionComprobante']["autorizaciones"]["autorizacion"]["mensajes"]["mensaje"]["mensaje"].' => '.
            //                                 $responseAut['RespuestaAutorizacionComprobante']["autorizaciones"]["autorizacion"]["mensajes"]["mensaje"]["mensaje"]["informacionAdicional"];
            //                     }else{
            //                         $ms = $responseAut['RespuestaAutorizacionComprobante']["autorizaciones"]["autorizacion"]["mensajes"]["mensaje"]["mensaje"];
            //                     }
            //                     //BORRAR EL VAR_DUMP
            //                     echo '<br/><br/>'.var_dump($responseAut).'<br/><br/>';
            //                 } else {
            //                     echo 'No autorizado<br/>';
            //                     echo "Esta es la respuesta de SRI:<br/>";
            //                     echo var_dump($responseAut);
            //                     echo "<br/>";
            //                     echo 'INFORME AL ADMINISTRADOR!</br>';
            //                 }
            //             break;
            //             }
            //             break;

            //         case 'DEVUELTA':
            //             $m .= $response["RespuestaRecepcionComprobante"]["estado"] . '<br>';
            //                     $m .= $response["RespuestaRecepcionComprobante"]["comprobantes"]["comprobante"]["claveAcceso"] . '<br>';
            //                     $m .= $response["RespuestaRecepcionComprobante"]["comprobantes"]["comprobante"]["mensajes"]["mensaje"]["mensaje"] . '<br>';
            //                     if (isset($response["RespuestaRecepcionComprobante"]["comprobantes"]["comprobante"]["mensajes"]["mensaje"]["informacionAdicional"])) {
            //                         $m .= $response["RespuestaRecepcionComprobante"]["comprobantes"]["comprobante"]["mensajes"]["mensaje"]["informacionAdicional"] . '<br>';
            //                         $ms = $response["RespuestaRecepcionComprobante"]["comprobantes"]["comprobante"]["mensajes"]["mensaje"]["mensaje"] . ' => ' . $response["RespuestaRecepcionComprobante"]["comprobantes"]["comprobante"]["mensajes"]["mensaje"]["informacionAdicional"];
            //                     } else {

            //                         $ms = $response["RespuestaRecepcionComprobante"]["comprobantes"]["comprobante"]["mensajes"]["mensaje"]["mensaje"];
            //                     }

            //                     $m .= $response["RespuestaRecepcionComprobante"]["comprobantes"]["comprobante"]["mensajes"]["mensaje"]["tipo"] . '<br><br>';
            //                     echo $response["RespuestaRecepcionComprobante"]["estado"] . '<br>';
            //                     echo $response["RespuestaRecepcionComprobante"]["comprobantes"]["comprobante"]["claveAcceso"] . '<br>';
            //                     echo $response["RespuestaRecepcionComprobante"]["comprobantes"]["comprobante"]["mensajes"]["mensaje"]["mensaje"] . '<br>';
            //                     if (isset($response["RespuestaRecepcionComprobante"]["comprobantes"]["comprobante"]["mensajes"]["mensaje"]["informacionAdicional"])) {
            //                         echo $response["RespuestaRecepcionComprobante"]["comprobantes"]["comprobante"]["mensajes"]["mensaje"]["informacionAdicional"] . '<br>';
            //                     }
            //                     echo $response["RespuestaRecepcionComprobante"]["comprobantes"]["comprobante"]["mensajes"]["mensaje"]["tipo"] . '<br><br>';
            //                     $controlError = true;
            //                 break;


            //         default:
            //         echo "<br>Se ha producido un problema. Vuelve a intentarlo.<br>";
            //         echo "Esta es la respuesta de SRI:<br/>";
            //         //echo var_dump($response).'<br>';
            //         $m .= var_dump($response).'<br>';
            //         echo "<br><br>";
            //         $controlError = true;
            //         break;
            //     }
            //     break;

            // default:
            //     dd('no se puede firmar el doc') ;
            // break;
        }
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





}
