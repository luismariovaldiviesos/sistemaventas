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
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\Break_;
//require_once "/vendor/econea/nusoap/src/nusoap.php";
use soap_server;

class Factura extends Model
{
    use HasFactory;
    use WithFileUploads;
    use FuncionesTrait;
    use PdfTrait;
    use SoftDeletes;

    //const $PASSCERTIFICADO =  Certificado::first('pass');

    protected $dates = ['deleted_at']; // Indica que deleted_at es una fecha

    protected $fillable = ['secuencial','numeroAutorizacion','fechaAutorizacion','codDoc','claveAcceso','customer_id',
                            'user_id','subtotal','descuento','total','formaPago'
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
        //$empresa =  Cache::get('settings');
        //$empresa = Setting::first();
        $empresa =  empresa();
        return $empresa;
    }

    public function claveAcceso()
    {
         $fecha =  Carbon::now()->format('dmY'); //1
         $codigo  = "01"; //2
         $parteUno = $fecha.$codigo;   //1+2***********
         $empresa =  $this->empresa();
         $ruc =  $empresa->ruc;  //3
         $ambiente  =  $empresa->ambiente;  //4
         $establecimiento =  $empresa->estab;
         $puntoEmi  =  $empresa->ptoEmi;
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
    public function xmlFactura($factura_id, $tipoIdentificadorCli, $razonSocialCli, $identificadorCliente, $direccionCliente,
    $totalSinImpuesto, $totalDescuento, $totalFactura, $detalles, $secuencia, $claveAcce, $totalImpuestos)
{
   // dd($totalImpuestos, $detalles);
    $empresa = $this->empresa();
    $xml = new DOMDocument('1.0', 'utf-8');
    $xml->formatOutput = true;

    // Crear la estructura base del XML
    $xml_fac = $xml->createElement('factura');
    $xml_fac->setAttribute('id', 'comprobante');
    $xml_fac->setAttribute('version', '1.0.0');

    $xml_inf = $xml->createElement('infoTributaria');
    $xml_inf->appendChild($xml->createElement('ambiente', $empresa->ambiente));
    $xml_inf->appendChild($xml->createElement('tipoEmision', $empresa->tipoEmision));
    $xml_inf->appendChild($xml->createElement('razonSocial', $empresa->razonSocial));
    $xml_inf->appendChild($xml->createElement('nombreComercial', $empresa->nombreComercial));
    $xml_inf->appendChild($xml->createElement('ruc', $empresa->ruc));
    $xml_inf->appendChild($xml->createElement('claveAcceso', $claveAcce));
    $xml_inf->appendChild($xml->createElement('codDoc', '01'));
    $xml_inf->appendChild($xml->createElement('estab', $empresa->estab));
    $xml_inf->appendChild($xml->createElement('ptoEmi', $empresa->ptoEmi));
    $xml_inf->appendChild($xml->createElement('secuencial', $secuencia));
    $xml_inf->appendChild($xml->createElement('dirMatriz', $empresa->dirMatriz));

    $xml_fac->appendChild($xml_inf);

    // Información de la factura
    $xml_def = $xml->createElement('infoFactura');
    $xml_def->appendChild($xml->createElement('fechaEmision', date('d/m/Y')));
    $xml_def->appendChild($xml->createElement('dirEstablecimiento', $empresa->dirEstablecimiento));
    $xml_def->appendChild($xml->createElement('obligadoContabilidad', $empresa->obligadoContabilidad));
    $xml_def->appendChild($xml->createElement('tipoIdentificacionComprador', $tipoIdentificadorCli));
    $xml_def->appendChild($xml->createElement('razonSocialComprador', $razonSocialCli));
    $xml_def->appendChild($xml->createElement('identificacionComprador', $identificadorCliente));
    $xml_def->appendChild($xml->createElement('direccionComprador', $direccionCliente));
    $xml_def->appendChild($xml->createElement('totalSinImpuestos', number_format((float)$totalSinImpuesto, 2, '.', '')));
    $xml_def->appendChild($xml->createElement('totalDescuento', number_format((float)$totalDescuento, 2, '.', '')));


    //agrupamos impesustos
    $impuestosAgrupados = [];
    foreach($totalImpuestos as $impuesto){
        $clave = $impuesto['codigo_impuesto'] . '-' . $impuesto['codigo_porcentaje'];
        if (!isset($impuestosAgrupados[$clave])) {
            $impuestosAgrupados[$clave] = [
                'codigo_impuesto'   => $impuesto['codigo_impuesto'],
                'codigo_porcentaje' => $impuesto['codigo_porcentaje'],
                'base_imponible'    => 0,
                'valor_impuesto'    => 0
            ];
        }
        $impuestosAgrupados[$clave]['base_imponible'] += $impuesto['base_imponible'];
        $impuestosAgrupados[$clave]['valor_impuesto'] += $impuesto['valor_impuesto'];
    }


     // 🔹 Sección totalConImpuestos con los impuestos agrupados
    $xml_imp = $xml->createElement('totalConImpuestos');
    foreach ($impuestosAgrupados as $impuesto) {
        $xml_tim = $xml->createElement('totalImpuesto');
        $xml_tim->appendChild($xml->createElement('codigo', (int) $impuesto['codigo_impuesto']));
        $xml_tim->appendChild($xml->createElement('codigoPorcentaje', (int) $impuesto['codigo_porcentaje']));
        $xml_tim->appendChild($xml->createElement('baseImponible', number_format((float)$impuesto['base_imponible'], 2, '.', '')));
        $xml_tim->appendChild($xml->createElement('valor', number_format((float)$impuesto['valor_impuesto'], 2, '.', '')));
        $xml_imp->appendChild($xml_tim);
    }
    $xml_def->appendChild($xml_imp);



    $xml_def->appendChild($xml->createElement('propina', '0.00'));
    $xml_def->appendChild($xml->createElement('importeTotal', number_format((float)$totalFactura, 2, '.', '')));
    $xml_def->appendChild($xml->createElement('moneda', 'DOLAR'));

    // Pagos
    $xml_pgs = $xml->createElement('pagos');
    $xml_pag = $xml->createElement('pago');
    $xml_pag->appendChild($xml->createElement('formaPago', '01'));
    $xml_pag->appendChild($xml->createElement('total', number_format((float)$totalFactura, 2, '.', '')));
    $xml_pag->appendChild($xml->createElement('plazo', '90'));
    $xml_pag->appendChild($xml->createElement('unidadTiempo', 'dias'));
    $xml_pgs->appendChild($xml_pag);
    $xml_def->appendChild($xml_pgs);

       // Agregar elementos de retención (requeridos por el SRI)
    //    $xml_def->appendChild($xml->createElement('valorRetIva', '0.00'));
    //    $xml_def->appendChild($xml->createElement('valorRetRenta', '0.00'));

    $xml_fac->appendChild($xml_def);

    // Detalles de la factura
    $xml_dts = $xml->createElement('detalles');

    foreach ($detalles as $d) {
        $xml_det = $xml->createElement('detalle');
        $xml_det->appendChild($xml->createElement('codigoPrincipal', $d['id']));
        $xml_det->appendChild($xml->createElement('descripcion', $d['name']));
        $xml_det->appendChild($xml->createElement('cantidad', $d['qty']));
        $xml_det->appendChild($xml->createElement('precioUnitario', number_format((float)$d['price'], 2, '.', '')));
        $xml_det->appendChild($xml->createElement('descuento', number_format((float)$d['descuento'], 2, '.', '')));

         // 🔹 Base imponible corregida
         $baseImponible = round(($d['price'] * $d['qty']) - (($d['price'] * $d['qty']) * ($d['descuento'] / 100)), 2);
         $xml_det->appendChild($xml->createElement('precioTotalSinImpuesto', number_format($baseImponible, 2, '.', '')));

         // 🔹 Agregar impuestos de cada producto sin repetir
         $xml_ips = $xml->createElement('impuestos');
         $impuestosAgrupadosProd = [];

        foreach ($d['impuestos'] as $imp) {
                $key = $imp['codigo'] . '-' . $imp['codigo_porcentaje'];
                $valorImpuesto = round($baseImponible * ($imp['porcentaje'] / 100), 2);
                if (!isset($impuestosAgrupadosProd[$key])) {
                    $impuestosAgrupadosProd[$key] = [
                        'codigo' => $imp['codigo'],
                        'codigo_porcentaje' => $imp['codigo_porcentaje'],
                        'tarifa' => $imp['porcentaje'],
                        'baseImponible' => 0,
                        'valor' => 0
                    ];
                }

                $impuestosAgrupadosProd[$key]['baseImponible'] += $baseImponible;
                $impuestosAgrupadosProd[$key]['valor'] += $valorImpuesto;
        }

        // ✅ Agregar impuestos únicos al XML
        foreach ($impuestosAgrupadosProd as $imp) {
            $xml_ipt = $xml->createElement('impuesto');
            $xml_ipt->appendChild($xml->createElement('codigo', $imp['codigo']));
            $xml_ipt->appendChild($xml->createElement('codigoPorcentaje', $imp['codigo_porcentaje']));
            $xml_ipt->appendChild($xml->createElement('tarifa', $imp['tarifa']));
            $xml_ipt->appendChild($xml->createElement('baseImponible', number_format($imp['baseImponible'], 2, '.', '')));
            $xml_ipt->appendChild($xml->createElement('valor', number_format($imp['valor'], 2, '.', '')));
            $xml_ips->appendChild($xml_ipt);
        }
        $xml_det->appendChild($xml_ips);
        $xml_dts->appendChild($xml_det);
    }

    // ✅ Solo agregamos los detalles una vez

    $xml_fac->appendChild($xml_dts);

    // Información adicional
    $xml_ifa = $xml->createElement('infoAdicional');
    $xml_cp1 = $xml->createElement('campoAdicional', $empresa->email);
    $xml_cp1->setAttribute('nombre', 'email');
    $xml_ifa->appendChild($xml_cp1);
    $xml_fac->appendChild($xml_ifa);

    $xml->appendChild($xml_fac);
    //Se eliminan espacios en blanco
    $xml->preserveWhiteSpace = false;
    $xml->formatOutput = true;
    $archivo_factura_xml = $xml->saveXML();

    $nombre_fact_xml = $identificadorCliente . '_' . $secuencia . '.xml';
    // dd($nombre_fact_xml,$archivo_factura_xml);
    try {
        Storage::disk('comprobantes/no_firmados')->put($nombre_fact_xml,$archivo_factura_xml);
        XmlFile::create([
            'factura_id' => $factura_id,
            'secuencial' => $secuencia,
            'cliente'    => $razonSocialCli,
            'directorio' => 'comprobantes/no_firmados',
            'estado'     => 'creado',
            //paso 1
            ]);
        if (!Storage::disk('comprobantes/no_firmados')->exists($nombre_fact_xml)) {
            throw new Exception("El archivo XML no firmado no fue guardado: $nombre_fact_xml");
        }
    } catch (\Exception $e) {
        Log::error("Error en la creación del XML: " . $e->getMessage());
        $this->noty('ERROR AL CREAR EL XML ','noty','error');
        return false; // Termina el flujo o realiza un rollback.
    }

   //dd('creado archivo xml ', $nombre_fact_xml, $archivo_factura_xml);

   //debemos llamar al metodo aqui y pasarle el nombre del archivo
    $nombre_fact_xml =  substr($nombre_fact_xml, 0, -4); // Remover la extensión .xml
   // dd($nombre_fact_xml);

    $this->firmarFactura($nombre_fact_xml, $factura_id);
}




    public function firmarFactura($nombre_fact_xml, $factura_id)
    {

        // variables generales para autorizar xml sri
        //dd($nombre_fact_xml, $factura_id);
        $empresa = $this->empresa();
        //dd($empresa);
        $vtipoambiente=1;
        $wsdls = $this->wsdl($vtipoambiente);
        $recepcion = $wsdls['recepcion'];
        $autorizacionws = $wsdls['autorizacion'];

        //RUTAS PARA LOS ARCHIVOS XML
        //ruta de la factura
        $archivo_x_firmar =  base_path('storage/app/comprobantes/no_firmados/'.$nombre_fact_xml.'.xml');
        //dd('procedemos a firmar : '. ' ' .$archivo_x_firmar);
         // Ruta donde se guardará el archivo firmado
         $ruta_si_firmados =  base_path('storage/app/comprobantes/firmados/');
        //ruta del certifixcado
        // Ruta del certificado digital (archivo .pfx o .p12)
        //$certPath = base_path('storage/app/certificados/P0000119207.p12');
        $certPath = base_path("storage/app/certificados/{$empresa->cert_file}");
        //dd($certPath);
        // Contraseña del certificado digital
        //$certPass = 'Okz9UqnjX1';
        $certPass = $empresa->cert_password;
        //dd($certPass);
        // Contenido del xml
        $factContent = file_get_contents($archivo_x_firmar);
        //dd($factContent);
        // Cargar el certificado digital
        $certStore = openssl_pkcs12_read(file_get_contents($certPath), $certs, $certPass);
        if (!$certStore) {
            dd('Error al cargar el certificado digital.');
        }
        //dd($certStore);
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
        $nombre_fact_xml_firmada =  $nombre_fact_xml . '.xml';
        // // Crear un nuevo objeto XMLSecurityKey a partir de la clave privada
        // $argumentos = $archivo_x_firmar . ' ' . $ruta_si_firmados . ' ' . $nuevo_xml . ' ' . $firma . ' ' . $clave;
        $argumentos = $archivo_x_firmar . ' ' . $ruta_si_firmados . ' ' . $nombre_fact_xml_firmada . ' ' . $certPath . ' ' . $certPass;
        //$comando = ('java -jar C:\\Comprobantes\\firmaComprobanteElectronico\\dist\\firmaComprobanteElectronico.jar ' . $argumentos);
        //$comando = ('java -jar C:\\Comprobantes\\firmaComprobanteElectronico\\dist\\firmaComprobanteElectronico.jar ' . $argumentos);
        $jarPath =  base_path('storage/jar/dist/firmaComprobanteElectronico.jar');
       $comando = "java -jar \"$jarPath\" $argumentos";
        try {
            $resp = shell_exec($comando);
           //dd($resp);
        } catch (\Exception $e) {
            $xmlFile = XmlFile::where('factura_id', $factura_id)->firstOrFail();
            $xmlFile->update([
                'directorio' => 'comprobantes/no_firmados',
                'estado'     => 'creado',
                //paso dos
            ]);
            dd('Error al buscar java, no se pudo firmar el archivo XML ' . $e->getMessage());
        }

        $claveAcces = simplexml_load_file($ruta_si_firmados . $nombre_fact_xml_firmada);
        //dd($claveAcces);
        $claveAcceso = substr($claveAcces->infoTributaria[0]->claveAcceso, 0, 49);

        $respuesta  =  substr($resp,0,7);
        //dd($respuesta);
        switch($respuesta){
            case  'FIRMADO' :
                //Storage::disk('comprobantes/firmados')->put($nuevo_xml,$xml_firmado);
                $xmlFile = XmlFile::where('factura_id', $factura_id)->firstOrFail();
                $xmlFile->update([
                    'directorio' => 'comprobantes/firmados',
                    'estado'     => 'firmado',
                    //paso dos
                ]);
                $archivo_xml_firmado =  file_get_contents($ruta_si_firmados .  $nombre_fact_xml_firmada);
                //dd($claveAcceso,$archivo_xml_firmado);
                $data = base64_encode($archivo_xml_firmado);
                $obj = new \StdClass();
                $obj->key = $claveAcceso ;
                $obj->base64 = $data;
                $this->recibir($obj, $nombre_fact_xml_firmada, $archivo_xml_firmado, $factura_id);
                //dd('enviado',$claveAcceso,$xml_firmado);
                //sleep(10);
                $respuestaSRI = $this->fetch($obj, $nombre_fact_xml_firmada, $archivo_xml_firmado, $factura_id);
               return($respuestaSRI);
            break;
            default:
            $xmlFile = XmlFile::where('factura_id', $factura_id)->firstOrFail();
            $xmlFile->update([
                'directorio' => 'comprobantes/no_firmados',
                'estado'     => 'creado',
                //paso dos
            ]);
            dd('Error general no se pudo firmar el archivo XML ' . $e->getMessage());

        }
    }



    public function recibir($invoiceObj,$nombre_fact_xml_firmada, $archivo_xml_firmado, $factura_id)
    {
        //Si es ambiente de desarrollo
        //dd($nombre_fact_xml_firmada,$archivo_xml_firmado);
        //Todo: Modificar el código del parametro depende de su sistema.
        $ambiente = '1';
        if ($ambiente == '1') {
            $host = 'https://celcer.sri.gob.ec';
        } else { //Si es producción
            $host = 'https://cel.sri.gob.ec';
        }

        $xmlFile = XmlFile::where('factura_id', $factura_id)->firstOrFail();

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
        //dd($response);
        file_put_contents("respuesta_sri.xml", $response);
        $code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        //dd(curl_close($curl));

        if ($code !== 200) {
            //throw new SriReceiveException('Sri está caido.');
            //paso 3, no hay red no se envio el xml al sri
            //aqui debe ir no enviados
            $xmlFile->update([
                'directorio' => 'comprobantes/no_enviados',
                'estado' => 'no_enviado'

            ]);
            dd("NO SE ENVIÓ EL COMPROBANTE AL SRI, REVISE SU CONEXIÓN A INTERNET O SRI CAÍDO");
        }

        $simpleXml = new \SimpleXMLElement($response);

        $estado = $simpleXml->xpath('//estado')[0];
        //dd($estado);

        if ('DEVUELTA' === (string)$estado)
         {
            $comprobante = $simpleXml->xpath('//comprobante')[0];
            //throw new SriReceiveException($comprobante->mensajes[0]->mensaje->mensaje, $comprobante->mensajes[0]->mensaje->informacionAdicional);
            //dd($comprobante->mensajes[0]->mensaje->mensaje, $comprobante->mensajes[0]->mensaje->informacionAdicional);
            $mensajeError = optional($comprobante->mensajes->mensaje[0]->mensaje)->__toString();
            $infoAdicional = optional($comprobante->mensajes->mensaje[0]->informacionAdicional)->__toString();

                $xmlFile->update([
                    'directorio' => 'comprobantes/devueltos',
                    'estado' => 'devuelto',
                    'error' =>trim($mensajeError . '-' . $infoAdicional)
                    // paso 4 devueltas por el sri aqui debe ir una actualziacion del xml y carpeta devuelta por el sri
                    // aqui debe guardar el error del sri, agregar campo a la tabla xml_files.
                ]);
                Storage::disk('comprobantes/devueltos')->put($nombre_fact_xml_firmada,$archivo_xml_firmado);
                //dd('Devuelta, ya se envió el comprobante al SRI, estado del XML');
                dd('DEVUELTA:', $mensajeError, $infoAdicional);
            }

        Storage::disk('comprobantes/enviados')->put($nombre_fact_xml_firmada,$archivo_xml_firmado);
        //dd('hasta aqui lelga todo ');
        $xmlFile = XmlFile::where('factura_id', $factura_id)->firstOrFail();
        $xmlFile->update([
            'directorio' => 'comprobantes/enviados',
            'estado' => 'enviado'
            //paso 5 si todo va bien hasta aqui esta enviado el comprobante
        ]);
    }

    public function fetch($invoiceObj,$nombre_fact_xml_firmada,$archivo_xml_firmado, $factura_id)
    {
         //autorizados
        //dd('vamos a recupearar del srl ',$nombre_fact_xml_firmada,$archivo_xml_firmado);
       //dd($invoiceObj->key);
        $ambiente = "1";
        $xmlFile = XmlFile::where('factura_id', $factura_id)->firstOrFail();
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
            dd("PROBLEMAS CON EL SRI, REVISE SU CONEXIÓN A INTERNET O SRI CAÍDO");
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
            dd('ERROR AL TRATAR EL XML EN RECUPERACION DEL SRI: ' . $e->getMessage());
        }
        if ('NO AUTORIZADO' === (string)$estado) {
            $comprobante = $simpleXml->xpath('//autorizacion')[0];
           // throw new SriAuthorizeException($comprobante->mensajes[0]->mensaje->mensaje, $comprobante->mensajes[0]->mensaje->informacionAdicional);
           //paso 6 aqui iria no autorizado por el sri se deberia crear el directorio y actualizar el xml y llenar el motivo de no autorizado
           $mensajeError = optional($comprobante->mensajes->mensaje[0]->mensaje)->__toString();
            $infoAdicional = optional($comprobante->mensajes->mensaje[0]->informacionAdicional)->__toString();
           Storage::disk('comprobantes/no_autorizados')->put($nombre_fact_xml_firmada,$archivo_xml_firmado);
           $xmlFile->update([
            'directorio' => 'comprobantes/no_autorizados',
            'estado' => 'no_autorizado',
            'error' =>trim($mensajeError . '-' . $infoAdicional)
        ]);
           //dd($comprobante->mensajes[0]->mensaje->mensaje, $comprobante->mensajes[0]->mensaje->informacionAdicional);
           dd('no autorizado:', $mensajeError, $infoAdicional);
        }
        if ('AUTORIZADO' === (string)$estado) {
            $comprobante = $simpleXml->xpath('//autorizacion')[0];
            $estado = $simpleXml->xpath('//estado')[0];
            $numeroAutorizacion = $simpleXml->xpath('//numeroAutorizacion')[0];
            $fechaAutorizacion = $simpleXml->xpath('//fechaAutorizacion')[0];
            $vfechaauto = substr($fechaAutorizacion, 0, 10) . ' ' . substr($fechaAutorizacion, 11, 5);
            $comprobanteAutorizacion=$simpleXml->xpath('//comprobante')[0];
            // aqui hay que llamar a la funcion xml autorizado *****************

            //paso 7 comprbante autorizado por el sri
            Storage::disk('comprobantes/autorizados')->put($nombre_fact_xml_firmada,$archivo_xml_firmado);
           // dd('autorizado:', $nombre_fact_xml_firmada,$archivo_xml_firmado);

           $xmlFile->update([
               'directorio' => 'comprobantes/autorizados',
               'estado' => 'autorizado'
           ]);
           $this->crearXmlAutorizado($estado,$numeroAutorizacion,$vfechaauto,$comprobante,
            $comprobanteAutorizacion, $factura_id);
            //dd($estado,$numeroAutorizacion,$fechaAutorizacion, $vfechaauto, $comprobanteAutorizacion);
          // return $xmlAprobado;

        }


    }


    public  function crearXmlAutorizado($estado,$numeroAutorizacion,$fechaAutorizacion,
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





    public  function detalles (){

        return $this->hasMany(DetalleFactura::class);
    }


    public  function customer (){
        return $this->belongsTo(Customer::class);
    }

    public function xmlFile()
    {
        return $this->hasOne(XmlFile::class, 'factura_id');
    }


    public function usuario(){
        return $this->belongsTo(User::class);
    }


    public function impuestos(){
        return $this->hasMany(FacturaImpuesto::class, 'factura_id');
    }








}
