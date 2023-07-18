<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DOMDocument;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Unique;

class Factura extends Model
{
    use HasFactory;
    use WithFileUploads;

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
     $totalSinImpuesto, $totalDescuento,$subtotaliva12, $totalIva12)
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
		$xml_pro = $xml->createElement('propina','0.00');  // ******************revisar***************
		$xml_imt = $xml->createElement('importeTotal',20);// ******************revisar***************
		$xml_mon = $xml->createElement('moneda','DOLAR');// ******************revisar***************

		//PARTE PAGOS
		$xml_pgs = $xml->createElement('pagos');// ******************revisar***************
		$xml_pag = $xml->createElement('pago');// ******************revisar***************
		$xml_fpa = $xml->createElement('formaPago','01');// ******************revisar***************
		$xml_tot = $xml->createElement('total',20);// ******************revisar***************
		$xml_pla = $xml->createElement('plazo','1');// ******************revisar***************
		$xml_uti = $xml->createElement('unidadTiempo','dias');// ******************revisar***************


        $xml_dts = $xml->createElement('detalles');// ******************revisar***************
		$xml_det = $xml->createElement('detalle');// ******************revisar***************
		$xml_cop = $xml->createElement('codigoPrincipal',01);// ******************revisar***************
		$xml_dcr = $xml->createElement('descripcion','descrp');// ******************revisar***************
		$xml_can = $xml->createElement('cantidad',20);// ******************revisar***************
		$xml_pru = $xml->createElement('precioUnitario',20);// ******************revisar***************
		$xml_dsc = $xml->createElement('descuento',0.00);// ******************revisar***************
		$xml_tsm = $xml->createElement('precioTotalSinImpuesto',0.00);// ******************revisar***************

        $xml_ips = $xml->createElement('impuestos'); // ******************revisar***************
		$xml_ipt = $xml->createElement('impuesto');// ******************revisar***************
		$xml_cdg = $xml->createElement('codigo','2');// ******************revisar***************
		$xml_cpt = $xml->createElement('codigoPorcentaje','0');// ******************revisar***************
		$xml_trf = $xml->createElement('tarifa','0.00');// ******************revisar***************
		$xml_bsi = $xml->createElement('baseImponible','1.00');// ******************revisar***************
		$xml_vlr = $xml->createElement('valor','0.00');// ******************revisar***************

        $xml_ifa = $xml->createElement('infoAdicional');// ******************revisar***************
		$xml_cp1 = $xml->createElement('campoAdicional','sacta@mail');// ******************revisar***************
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
        $factura = uniqid() . '_.' .$empresa[0]->ruc.'.xml'; // nombre de la imagen
        //Y se guarda en el nombre del archivo 'achivo.xml', y el obejto nstanciado
        Storage::disk('comprobantes/no_firmados')->put($factura,$xml_string);





        //$xml->save('../Comprobantes/no_firmados/prueba.xml');
        //$xml->save('public/comprobantes/no_firmados/prueba.xml');
       //$xml->storeAs('public/comprobantes/no_firmados', $this->secuencial());
        //Storage::put('public/comprobantes/no_firmados/prueba.xml', $xml);

    }





}
