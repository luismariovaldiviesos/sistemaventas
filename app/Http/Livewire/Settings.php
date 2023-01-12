<?php

namespace App\Http\Livewire;

use App\Models\Setting;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{

    use WithFileUploads;

    public $razonSocial, $nombreComercial, $ruc,$estab,$ptoEmi,$dirMatriz,$dirEstablecimiento,
    $telefono, $email, $ambiente,$tipoEmision,$contribuyenteEspecial,$obligadoContabilidad,
    $logo, $leyend, $printer, $selected_id, $logoPreview;


    public function mount()
    {
        $info =  Setting::first();
        //dd($info);

        if($info){
            $this->selected_id = $info->id;
            $this->razonSocial = $info->razonSocial;
            $this->nombreComercial = $info->nombreComercial;
            $this->ruc = $info->ruc;
            $this->estab = $info->estab;
            $this->ptoEmi = $info->ptoEmi;
            $this->dirMatriz = $info->dirMatriz;
            $this->dirEstablecimiento = $info->dirEstablecimiento;
            $this->telefono = $info->telefono;
            $this->email = $info->email;
            $this->ambiente = $info->ambiente;
            $this->tipoEmision = $info->tipoEmision;
            $this->contribuyenteEspecial = $info->contribuyenteEspecial;
            $this->obligadoContabilidad = $info->obligadoContabilidad;
            $this->logoPreview = $info->logo;
            $this->leyend = $info->leyend;
            $this->printer = $info->printer;
        }
    }

    public function render()
    {
        return view('livewire.settings.component')
        ->layout('layouts.theme.app');
    }


    public function Store()
    {
        $rules = [
            'razonSocial' => 'required',
            'nombreComercial' => 'required',
            'ruc' => 'required|max:13',
            'estab' => 'required|max:3',
            'ptoEmi' => 'required|max:3',
            'dirMatriz' => 'required',
            'dirEstablecimiento' => 'required',
            'telefono' => 'required',
            'email' => "required|email|unique:settings,email,{$this->selected_id}",
            'ambiente' => 'required|max:1',
            'tipoEmision' => 'required|max:1',
            'contribuyenteEspecial' => 'required|max:13',
            'obligadoContabilidad' => 'required|max:2'

        ];

        $messages =[
            'razonSocial.required' => 'Ingrese la razón social de la empresa',
            'nombreComercial.required' => 'Ingrese el nombre comercial de la empresa',
            'estab.required' => 'Ingrese el código del establecimiento',
            'estab.max' => 'Código del establecimiento debe ser máximo 3  caracteres',
            'ruc.required' => 'Ingrese un ruc ',
            'ruc.max' => 'Ruc debe tener máximo 13 caracteres ',
            'ptoEmi.required' => 'Ingrese un punto de emisión ',
            'ptoEmi.max' => 'Punto emision  debe tener máximo 3 caracteres ',
            'dirMatriz.required' => 'Ingrese la direccion matriz',
            'dirEstablecimiento.required' => 'Ingrese la direccion de establecimiento',
            'telefono.required' => 'Ingrese el teléfono del establecimiento',
            'email.required' => 'Ingrese el correo ',
            'email.email' => 'Ingrese un correo válido',
            'ambiente.required' => 'Ingrese  el ambiente del sistema',
            'ambiente.max' => 'El ambiente debe ser de un solo caracter',
            'tipoEmision.required' => 'Ingrese  el tipo de emision',
            'tipoEmision.max' => 'El tipo de emisión debe ser de un solo caracter',
            'contribuyenteEspecial.required' => 'Ingrese si es contribuyente especial',
            'contribuyenteEspecial.max' => 'El codigo contribuyente especial debe tener máximo 13 caracteres',
            'obligadoContabilidad.required' => 'Campo requerido',
            'obligadoContabilidad.max' => 'El campo debe tener máximo 2 caracteres',


        ];

        $this->validate($rules, $messages);

    sleep(2);
        //guardamos temporalmente el logo
    $tempLogo = Setting::first()->logo ?? null;
        //eliminamos info de la tabla
    Setting::truncate();


    $config = Setting::create(
        [
            'razonSocial' => $this->razonSocial,
            'nombreComercial' => $this->nombreComercial,
            'ruc' => $this->ruc,
            'estab' => $this->estab,
            'ptoEmi' => $this->ptoEmi,
            'dirMatriz' => $this->dirMatriz,
            'dirEstablecimiento' => $this->dirEstablecimiento,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'ambiente' => $this->ambiente,
            'tipoEmision' => $this->tipoEmision,
            'contribuyenteEspecial' => $this->contribuyenteEspecial,
            'obligadoContabilidad' => $this->obligadoContabilidad,
            'logo' => $this->logo,
            'leyend' => $this->leyend,
            'printer' => $this->printer,
        ]
    );

    if ($this->logo != null) {
        //eliminar logo anterior
        if (File::exists(public_path($tempLogo))) {
          File::delete($tempLogo);
      }

      // guardar logo en la db
      $customFileName = uniqid() . '_.' . $this->logo->extension();
      $config->logo = $customFileName;
      $config->save();

      // storage logo in public folder
      $this->logo->storeAs('', $customFileName, 'public2');  //CONFIGURAR DRIVER PUBLIC2

      // display new logo
      $this->logoPreview = $customFileName;

      // clear previous logo
      $this->logo=null;

  }

  $this->dispatchBrowserEvent('noty', ['msg'=> 'CONFIGURACIÓN GUARDADA', 'type' => 'success']);

}


public $sisteners =['refresh' => '$refresh'];
}
