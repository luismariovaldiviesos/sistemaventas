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


        // $tempcertificado =  Certificado::first()->certificado ?? null;
        // dd($tempcertificado);
        // eliminamos info de la tabla
        // Certificado::truncate();
        // $config = Certificado::create([
        //     'password' => $this->password,
        //     'certificado' => $this->certificado,
        // ]);

        // dd($this->certificado);

        // if ($this->certificado != null) {
        //     eliminamos certificado anterior
        //     if (File::exists(storage_path('certificados')) ) {
        //         File::delete($tempcertificado);
        //     }

        //     guardar certificado en la db
        //     dd($this->certificado);
        //     $config->certificado = $this->certificado;
        //     $config->save();

        //     guarda el archivo en la carpeta certificados
        //     nombre del certificado
        //     $cert = uniqid() . '_.' .$this->certificado.'.p12'; // nombre de la imagen
        //     dd($cert);
        //     Y se guarda en el nombre del archivo 'achivo.xml', y el obejto nstanciado
        //    Storage::disk('certificados')->put('nombrecertificado',$this->certificado);
        //    $archivoGuardado = $this->certificado->store('certificados');
        // }
        //


        Certificado::truncate(); // solo guarda un certificado de firma
        //dd($this->certificado);
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
