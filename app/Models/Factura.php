<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Factura extends Model
{
    use HasFactory;

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





}
