<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    use HasFactory;
    protected $fillable = ['certificado','password'];

    // public static function rules()
    // {
    //     return [
    //         'certificado' => 'required|mimetypes:application/x-pkcs12',
    //         'password' => 'required',
    //     ];
    // }

    // public static $messages = [
    //     'certificado.required' => 'Archivo PKC requerido',
    //     'certificado.mimetypes' => 'El tipo de archivo no corresponde',
    //     'password.required' => 'contraseña PKC requerido',
    // ];
}
