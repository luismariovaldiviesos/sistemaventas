<?php

namespace App\Http\Livewire;

use App\Models\Certificado;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Certificados extends Component
{
    use WithFileUploads;

    public $componentName = 'Archvo de firma electrónica';
    public $certificado, $password;


    function mount()  {
        $info = Certificado::first();
        if($info)
        {
            $this->certificado = $info->certificado;
            $this->certificado = $info->password;

            // tenenos que hacer que ssea almacene solo u archivo en la base
        }
    }

    public function render()
    {
       // $this->Store();
        return view('livewire.certificados.component')->layout('layouts.theme.app');;
    }

    public function noty($msg, $eventName = 'noty', $reset = true, $action = '')
    {
        $this->dispatchBrowserEvent($eventName, ['msg' => $msg, 'type' => 'success', 'action' => $action]);
        //if ($reset) $this->resetUI();
    }



    function Store()
    {
        sleep(1);
        Certificado::truncate(); // solo guarda un certificado de firma
        //dd($this->certificado);
        Storage::deleteDirectory('certificados');
        $nombrecert = $this->certificado->getClientOriginalName();
        $certificado  =  $this->certificado->storeAs('certificados',$nombrecert);  //C:\laragon\www\sistemaventas\storage\app\certificados
        Certificado::create([
            'certificado' => $certificado,
            'password' => bcrypt($this->password)
        ]);
        // Limpiar los campos después de guardar el certificado
        $this->reset(['certificado', 'password']);
       // $this->noty('ARCHIVO DE FIRMA GUARDADO', 'noty');
        $this->dispatchBrowserEvent('noty', ['msg'=> ' ARCHIVO DE FIRMA Y CONTRSAEÑA GUARDADOS', 'type' => 'warning']);
    }
}
