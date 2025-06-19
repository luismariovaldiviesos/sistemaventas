<?php

namespace App\Http\Livewire;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class SmtpSettings extends Component
{

    public  $empresa_id,$provider,$mailer,$host,$port,$encryption,$username,$password,$from_address,$from_name;

   public $providers = ['Gmail', 'Outlook', 'Hotmail', 'Yahoo', 'Otro'];

    public function mount(){
        $this->empresa_id  =  empresa()->id;
        //$this->mailer = 'smtp';

        $smtpSetting =  SmtpSetting::first();
        if($smtpSetting){
             $this->provider = $smtpSetting->provider;
             $this->mailer = $smtpSetting->mailer;
             $this->host = $smtpSetting->host;
             $this->port = $smtpSetting->port;
             $this->encryption = $smtpSetting->encryption;
             $this->username = $smtpSetting->username;
             $this->password = $smtpSetting->password;
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



  public function setProvider($provider)
{
    $this->provider = $provider;
    $this->mailer = 'smtp'; // Fijo para todos

    switch (strtolower($provider)) {
        case 'gmail':
            $this->host = 'smtp.gmail.com';
            $this->port = 587;
            $this->encryption = 'tls';
            break;
        case 'outlook':
            $this->host = 'smtp.office365.com';
            $this->port = 587;
            $this->encryption = 'tls';
            break;
        case 'hotmail':
            $this->host = 'smtp.live.com';
            $this->port = 587;
            $this->encryption = 'tls';
            break;
        case 'yahoo':
            $this->host = 'smtp.mail.yahoo.com';
            $this->port = 465;
            $this->encryption = 'ssl';
            break;
        case 'otro':
            // Permitir edición manual
            $this->host = '';
            $this->port = '';
            $this->encryption = '';
            break;
    }
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
        //$this->password = Crypt::encryptString($this->password);
        //  dd($this->empresa_id, $this->provider, $this->mailer,
        //    $this->host,$this->port,$this->encryption, $this->username,
        //    $this->password, $this->from_address, $this->from_name);
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
                    'password'     => $this->password,
                    'from_address' => $this->from_address,
                    'from_name'    => $this->from_name,
                ]
                    );
        $this->dispatchBrowserEvent('noty', ['msg' => 'Configuración guardada correctamente', 'type' => 'success']);
     }


   public function testSmtpConnection()
{
    try {
        $encryption = strtolower($this->encryption);

        // TRUE si es 'ssl', FALSE para 'tls' (STARTTLS)
        $useSsl = ($encryption === 'ssl');

        // Crear transporte SMTP (el 3er parámetro es bool para SSL, no string)
        $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
            $this->host,
            intval($this->port),
            $useSsl
        );

        $transport->setUsername($this->username);
        $transport->setPassword($this->password);
       // dd($this->username, $this->password, $this->host, $this->port, $useSsl);
        // Esto valida la conexión SMTP
        $transport->start();

        $this->dispatchBrowserEvent('noty', [
            'msg' => '✅ Conexión SMTP exitosa',
            'type' => 'success',
        ]);
    } catch (\Exception $e) {
        $this->dispatchBrowserEvent('noty', [
            'msg' => '❌ Error de conexión SMTP: ' . $e->getMessage(),
            'type' => 'error',
        ]);
    }
}


    public function render()
    {
        return view('livewire.smtpsettings.component')
        ->layout('layouts.theme.app');
    }


}
