<?php

namespace App\Models;

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


class Factura extends Model
{
    use HasFactory;
    use WithFileUploads;

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
        $fecha =  Carbon::now()->format('dmY');
        $codigo  = "01";
        $parteUno = $fecha.$codigo;
        $empresa =  $this->empresa();
        $ruc =  $empresa[0]->ruc;
        $ambiente  =  $empresa[0]->ambiente;
        $establecimiento =  $empresa[0]->estab;
        $puntoEmi  =  $empresa[0]->ptoEmi;
        $serie  = $establecimiento.$puntoEmi;
        $tipoEmi  = "1";
        $parteDos =  $ruc.$ambiente.$serie.$tipoEmi;
        $cadenaUNo = $parteUno.$parteDos;
        $codigoNumerico  = "00000001";
        $cadenaDos  = $cadenaUNo.$this->secuencial().$codigoNumerico;
        $dig  =  $this->getMod11Dv($cadenaDos);
        $claveFinal = $cadenaDos.$dig;
        return $claveFinal ;
    }

    public  function secuencial ()
    {
        $fac = Factura::latest('secuencial')->first(); // ultimo ingresao  registro por el campo secuencial
        if ($fac == null) {
            $fac  = "000000001";
        }
        else{
           $sec_bd=  $fac->secuencial; // secuencial base de datos =  a
           $fac = $sec_bd+1; // se suma uno al secuencial
           $tamano = 9;  // max de ceros a la izquierda
           $fac = substr(str_repeat(0,$tamano).$fac,-$tamano); // se lelna de ceros a la izq
        }
       // codigo numerico es el mismo para toda fac tiene que ser 8 dig
        return $fac;
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
     $totalSinImpuesto, $totalDescuento,$subtotaliva12, $totalIva12,$totalFactura, $detalles)
    {
        $empresa = $this->empresa();
        //dd($empresa);
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
		$xml_cla = $xml->createElement('claveAcceso',$this->claveAcceso());
		$xml_doc = $xml->createElement('codDoc','01');  ///simpre va a ser 01 porque es fact
		$xml_est = $xml->createElement('estab',$empresa[0]->estab);
		$xml_emi = $xml->createElement('ptoEmi',$empresa[0]->ptoEmi);
		$xml_sec = $xml->createElement('secuencial','0000'.$this->secuencial());
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
        $factura = $identificadorCliente.'_'.$this->secuencial().'.xml'; // nombre de la imagen
        //Y se guarda en el nombre del archivo 'achivo.xml', y el obejto nstanciado
        Storage::disk('comprobantes/no_firmados')->put($factura,$xml_string);





        //$xml->save('../Comprobantes/no_firmados/prueba.xml');
        //$xml->save('public/comprobantes/no_firmados/prueba.xml');
       //$xml->storeAs('public/comprobantes/no_firmados', $this->secuencial());
        //Storage::put('public/comprobantes/no_firmados/prueba.xml', $xml);

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

        //ruta de la factura
        $factAFir =  base_path('storage/app/comprobantes/no_firmados/'.$facturaId.'.xml');

        //ruta del certifixcado
        // Ruta del certificado digital (archivo .pfx o .p12)
        $certPath = base_path('storage/app/certificados/P0000119207.p12');

        // Contraseña del certificado digital
        $certPass = 'Okz9UqnjX1';

        // Ruta donde se guardará el archivo firmado
        $signedPdfPath =  base_path('storage/app/comprobantes/firmados/'.$facturaId.'.xml');

        // Contenido del xml
        $factContent = file_get_contents($factAFir);

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
        $nuevo_xml =  $facturaId . 'signed';
        // // Crear un nuevo objeto XMLSecurityKey a partir de la clave privada
        // $argumentos = $ruta_no_firmados . ' ' . $ruta_si_firmados . ' ' . $nuevo_xml . ' ' . $firma . ' ' . $clave;
        $argumentos = $factAFir . ' ' . $signedPdfPath . ' ' . $nuevo_xml . ' ' . $certPath . ' ' . $certPass;
        $comando = ('java -jar C:\\Comprobantes\\firmaComprobanteElectronico\\dist\\firmaComprobanteElectronico.jar ' . $argumentos);
        try {
            $resp = shell_exec($comando);
        } catch (\Exception $e) {
            dd('Error al buscar java: ' . $e->getMessage());
        }

        $claveAcces = simplexml_load_file($signedPdfPath . $nuevo_xml);
        $claveAcceso['claveAccesoComprobante'] = substr($claveAcces->infoTributaria[0]->claveAcceso, 0, 49);
        var_dump($claveAcceso);
        var_dump($comando);
        var_dump($resp);

        dd($resp);
        // switch (substr($resp, 0, 7)){
        //     case 'FIRMADO' :

        // }


        // // Crear el objeto X509 para el certificado
        // $x509 = new X509();
        // $x509->loadX509($certs['cert']);
        // $xmlSecDSig->add509Cert($x509);

        // // Firmar el XML
        // $xmlSecDSig->sign($key);

        // // Guardar el XML firmado en el archivo
        // $signedXml = $xmlSecDSig->getXPathObj()->document->saveXML();

        // // Guardar el XML firmado en un archivo
        // file_put_contents($signedPdfPath, $signedXml);
        //31072023 FECHA
        //010104649843001 RUC EMPRESA
        //1
        //001
        //001
        //1
        //000000136
        //000000016
        //31072023 FECHA
        //010104649843001 RUC EMPRESA
        //1
        //001
        //001
        //1
        //000000136
        //000000016




    }


    // public  function firmarFactura($factura){

    //     // Obtener la ruta del archivo XML de la factura
    //     //$rutaFacturaXml = 'comprobantes/no_firmados/' . $factura . '.xml';
    //     $rutaFacturaXml = storage_path('app\\comprobantes\\no_firmados\\' . $factura . '.xml');
    //     //dd($rutaFacturaXml);
    //    //$rutaCertificado = 'C:/laragon/www/sistemaventas/storage/certificados/P0000119207.p12';
    //    $rutaCertificado = DB::table('certificados')->where('id', 1)->value('certificado');

    //    $contrasenaCertificado = DB::table('certificados')->where('id', 1)->value('password');

    //    // Verificar que el archivo XML de la factura exista
    //    if (!Storage::exists($rutaFacturaXml)) {
    //         dd('no se encuantra lla factura a firmar');
    //         return;
    //    }
    //    dd($contrasenaCertificado);

    //      // Verificar que el archivo de firma electrónica exista
    //      if (!Storage::exists($rutaCertificado)) {
    //         dd('no se encuentra el archivo a firmar');
    //     }
    //     //dd($rutaCertificado);

    //     // Leer el contenido del archivo XML de la factura
    //     $contenidoFacturaXml = file_get_contents($rutaFacturaXml);
    //     dd($contenidoFacturaXml);




    // }





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
