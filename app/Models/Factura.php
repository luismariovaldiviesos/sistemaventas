<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','customer_id','codDoc','claveAcceso','secuencial','estado'];


    // validaciones

    public static function rules($id)
    {
        if($id < 0) // crear
        {
            return[

                'user_id' => 'required',
                'customer_id' => 'required',
                'codDoc'=> 'required',
                'claveAcceso'=> 'required',
                'secuencial'=> 'required',
                'estado'=> 'required',

            ];

        }
    }

    public static $messages =[
        'user_id.required' => 'usuario requerido',
        'customer_id.required' => 'cliente es requerido',
        'codDoc.required' => 'Código es  requerido',
        'claveAcceso.required' => 'calve de acceso  requerido',
        'secuencial.required' => 'secuencial  requerido',
        'estado.required' => 'estado factura requerido', // por interno va
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
