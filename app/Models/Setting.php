<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [

        'razonSocial','nombreComercial','ruc','estab','ptoEmi','dirMatriz',
        'dirEstablecimiento','telefono','email','ambiente','tipoEmision',
        'contribuyenteEspecial','obligadoContabilidad','logo','leyend','printer'

            ];
}
