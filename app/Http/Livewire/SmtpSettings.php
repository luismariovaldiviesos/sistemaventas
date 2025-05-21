<?php

namespace App\Http\Livewire;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class SmtpSettings extends Component
{

    public  $empresa_id,$provider,$mailer,$host,$port,$encryption,$username,$password,$from_address,$from_name;

    public $providers = ['Gmail', 'Outlook', 'Otro'];


    public function mount(){
        $this->empresa_id  =  empresa()->id;
        $this->mailer = 'smtp';

        $smtpSetting =  SmtpSetting::first();
        if($smtpSetting){
             $this->provider = $smtpSetting->provider;
             $this->mailer = $smtpSetting->mailer;
             $this->host = $smtpSetting->host;
             $this->port = $smtpSetting->port;
             $this->encryption = $smtpSetting->encryption;
             $this->username = $smtpSetting->username;
             $this->password = Crypt::decryptString($smtpSetting->password);
             $this->from_address = $smtpSetting->from_address;
             $this->from_name = $smtpSetting->from_name;
        }else{
             $this->provider = 'Elegir';
             $this->mailer = 'smtp';
             $this->host = 'smtp.gmail.com';
             $this->port = '587';
             $this->encryption = 'tls';
             $this->username = '';
             $this->password = '';
             $this->from_address = '';
             $this->from_name = '';
        };


       // $this->smtp = SmtpSetting::firstOrNew(['empresa_id' => $this->empresa_id]);
    }

      public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'username' => 'required|email',
            'password' => 'required',
            'host' => 'required',
            'port' => 'required',
            'from_address' => 'required|email',
            'from_name' => 'required|string',
        ]);
    }

     public function setProvider($provider){
        $this->provider = $provider;
        //$this->mailer = 'smtp';   // cambiar aqui mailer
        //dd($provider);
        switch ($provider){
            case 'Gmail':
                 $this->host = 'smtp.gmail.com';
                $this->port = '587';
                $this->encryption = 'tls';
                break;
            case 'Outlook':
                 $this->host = 'smtp.office365.com';
                $this->port = '587';
                $this->encryption = 'tls';
                break;
            default:
            $this->host = '';
                $this->port = '';
                $this->encryption = '';
                break;
        }
       // dd($this->host,$this->port,$this->encryption);
     }

     public function  Store (){


        $rules = [
            'username' => 'required|email',
            'password' => 'required',
            'host' => 'required',
            'port' => 'required',
            'from_address' => 'required|email',
            'from_name' => 'required|string',
            'provider' => 'required',
        ];

        $messages = [
            'username.required' => 'El campo :attribute es obligatorio.',
            'username.email' => 'El campo :attribute debe ser un correo electrónico válido.',
            'password.required' => 'El campo :attribute es obligatorio.',
            'host.required' => 'El campo :attribute es obligatorio.',
            'port.required' => 'El campo :attribute es obligatorio.',
            'from_address.required' => 'El campo :attribute es obligatorio.',
            'from_address.email' => 'El campo :attribute debe ser un correo electrónico válido.',
            'from_name.required' => 'El campo :attribute es obligatorio.',
            'provider.required' => 'El campo proovedeor  es obligatorio.',
        ];
        $this->validate($rules, $messages);

        $this->empresa_id = $this->empresa_id;
        $this->password = Crypt::encryptString($this->password);
        // dd($this->empresa_id, $this->provider, $this->mailer,
        //   $this->host,$this->port,$this->encryption, $this->username,
        //   $this->password, $this->from_address, $this->from_name);
      SmtpSetting::updateOrCreate(
        ['id' => 1], // Se asegura que solo exista un registro (ID fijo o puedes usar first() y update manual)
        [
            'empresa_id'   => $this->empresa_id,
            'provider'     => $this->provider,
            'mailer'       => $this->mailer,
            'host'         => $this->host,
            'port'         => $this->port,
            'encryption'   => $this->encryption,
            'username'     => $this->username,
            'password'     => Crypt::encryptString($this->password),
            'from_address' => $this->from_address,
            'from_name'    => $this->from_name,
        ]
    );


        $this->dispatchBrowserEvent('noty', ['msg' => 'Configuración guardada correctamente', 'type' => 'success']);
     }

    public function render()
    {
        return view('livewire.smtpsettings.component')
        ->layout('layouts.theme.app');
    }
}
