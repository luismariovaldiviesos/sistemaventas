<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arqueo extends Model
{
    use HasFactory;

    protected $fillable = ['caja_id','user_id','monto_inicial','monto_final','total','observaciones'];


    // reglas aqui




    //relaciones

    // un arqueo pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // un arqueo pertenece a una caja
    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }





}
