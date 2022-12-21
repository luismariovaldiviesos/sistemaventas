<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

use function PHPUnit\Framework\fileExists;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'ci',
        'phone',
        'email',
        'profile',
        'status',
        'password',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public static function rules($id)
    {
        if($id <=0){
            return
            [
                'name' => 'required|min:3|max:50|string|unique:users',
                'ci' => 'required|min:10|max:13|unique:users',
                'phone' => 'required',
                'email' => 'required|email|unique:users',
                'profile'=> 'required|not_in:elegir',
                'status'=> 'required|not_in:elegir'
            ];
        }
        else
        {
            return
            [
                'name' => "required|min:3|max:50|string|unique:users,name,{$id}",
                'ci' => "required|min:10|max:13|unique:users,ci,{$id}",
                'phone' => 'required',
                'email' => "required|email|unique:users,email,{$id}",
                'profile'=> 'required|not_in:elegir',
                'status'=> 'required|not_in:elegir'
            ];
        }
    }

    public static $messages =[
        'name.required' => 'nombre requerido',
        'name.unique' => 'nombre ya esta en uso',
        'name.min' => 'nombre debe tener al menos 3 caracteres',
        'name.max' => 'nombre debe tener maximo 50 caracteres',
        'name.string' => 'nombre debe tener solo letras',

        'ci.required' => 'ci-ruc requerido',
        'ci.unique' => 'ci-ruc ya esta registrado en el sistema',
        'ci.min' => 'ci-ruc debe tener al menos 10 caracteres',
        'ci.max' => 'ci-ruc debe tener maximo 13 caracteres',

        'phone.required' => 'teléfono es requerido requerido',


        'email.required' => 'email es requerido',
        'email.unique' => 'email ya esta en uso',
        'email.email' => 'email es inválido',

        'status.required' => 'estado  es requerido',
        'status.not_in' => 'Seleccione un estado  válido',


        'password.required' => 'password requerido',
        'password.min' => 'password debe tener mínimo 3 caracteres',
        'profile.required' => 'perfil es requerido',
        'profile.not_in' => 'Seleccione un perfil válido'
    ];


 //relaciones

    public function image()
    {
        return $this->morphOne(Image::class, 'model')->withDefault();
    }

    public function sales()
    {
        return $this->hasMany(Order::class);
    }


   // accesores y mutators

   public function getAvatarAttribute()
   {
        $img =  $this->image->file;
        if($img != null )
        {
            if(file_exists('storage/avatars/'. $img))
            return 'storage/avatars' . $img;
            else
            return 'storage/default_avatar.JPG';
        }

        return 'storage/default_avatar.JPG';
   }


   public  function caja ()
   {
    return $this->hasOne(Caja::class);
   }

   // un usuaio tiene variuos arqueos

   public function arqueos()
   {
       return $this->hasMany(Arqueo::class);
   }


}
