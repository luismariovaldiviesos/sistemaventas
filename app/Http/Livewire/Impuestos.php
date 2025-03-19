<?php

namespace App\Http\Livewire;

use App\Models\Impuesto;
use Livewire\Component;
use Livewire\WithPagination;

class Impuestos extends Component
{
    use WithPagination;

    public $nombre = '',$codigo='',$codigo_porcentaje='',$porcentaje='',  $selected_id = 0;
    public $action = 'Listado', $componentName = 'Impuestos', $search, $form = false;
    private $pagination = 10;
    protected $paginationTheme = 'tailwind';

    public function render()
    {
        if (strlen($this->search) > 0)
            $info = Impuesto::where('nombre', 'like', "%{$this->search}%")->paginate($this->pagination);
        else
            $info = Impuesto::paginate($this->pagination);


        return view('livewire.impuestos.component', ['impuestos' => $info])
            ->layout('layouts.theme.app');
    }

    public $listeners = [
        'resetUI',
        'Destroy'
    ];

    public function updatedForm()
    {
        if($this->selected_id > 0)
            $this->action ='Editar';
        else
            $this->action ='Agregar';

    }

    public function noty($msg, $eventnombre = 'noty', $reset = true, $action = '')
    {
        $this->dispatchBrowserEvent($eventnombre, ['msg' => $msg, 'type' => 'success', 'action' => $action]);
        if ($reset) $this->resetUI();
    }

    public function CloseModal()
    {
        $this->resetUI();
        $this->noty(null, 'close-modal');
    }
    public function resetUI()
    {
        // limpiar mensajes rojos de validación
        $this->resetValidation();
        // regresar a la página inicial del componente
        $this->resetPage();
        // regresar propiedades a su valor por defecto
        $this->reset('nombre','codigo','codigo_porcentaje','porcentaje', 'selected_id', 'search', 'action', 'componentName', 'form');
    }

    public function Edit(Impuesto $impuesto)
    {
        $this->selected_id = $impuesto->id;
        $this->nombre = $impuesto->nombre;
        $this->codigo = $impuesto->codigo;
        $this->codigo_porcentaje = $impuesto->codigo_porcentaje;
        $this->porcentaje = $impuesto->porcentaje;
        $this->action = 'Editar';
        $this->form = true;

    }

    public function Store()
    {
        sleep(1);

        $this->validate(Impuesto::rules($this->selected_id), Impuesto::$messages);

        $impuesto = Impuesto::updateOrCreate(

            ['id' => $this->selected_id ],
            [
                'nombre' => $this->nombre,
                'codigo' => $this->codigo,
                'codigo_porcentaje' => $this->codigo_porcentaje,
                'porcentaje' => $this->porcentaje

            ]
        );


        $this->noty($this->selected_id < 1 ? 'Impuesto Registrado' : 'Impuesto Actualizado', 'noty', false, 'close-modal');
        $this->resetUI();
    }

    public function Destroy(Impuesto $impuesto)
    {
        $impuesto->delete();
        $this->noty('Se eliminó El impuesto');
    }
}
