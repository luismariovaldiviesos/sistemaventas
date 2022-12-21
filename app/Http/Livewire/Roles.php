<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;




class Roles extends Component
{

    use WithPagination;

    public $roleName = '', $selected_id = 0;
    public $action = 'Listado', $componentName = 'Roles del sistema', $search, $form = false;
    private $pagination = 4;
    protected $paginationTheme = 'tailwind';


    public function render()
    {
        if (strlen($this->search) > 0)
        $info = Role::where('name', 'like', "%{$this->search}%")->paginate($this->pagination);
    else
        $info = Role::paginate($this->pagination);
        return view('livewire.roles.component', ['roles' => $info])->layout('layouts.theme.app');
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

    public function noty($msg, $eventName = 'noty', $reset = true, $action = '')
    {
        $this->dispatchBrowserEvent($eventName, ['msg' => $msg, 'type' => 'success', 'action' => $action]);
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
        $this->reset('roleName', 'selected_id', 'search', 'action', 'componentName', 'form');
    }

    public function Edit(Role $rol)
    {
        $this->selected_id = $rol->id;
        $this->roleName = $rol->name;
        $this->action = 'Editar';
        $this->form = true;

    }

    public function Store()
    {
        $rules = ['roleName' => "required|min:2|unique:roles,name, {$this->selected_id}"];

        $messages = [
            'roleName.required' => 'el nombre del rol es requerido',
            'roleName.min' => 'el tamaño del rol debe ser minimo dos caracteres',
            'roleName.unique' => 'el nombre del rol ya existe'
        ];

        $this->validate($rules,$messages);

        $role = Role::updateOrCreate(
            ['id' => $this->selected_id],
            ['name' => $this->roleName]
        );

        $this->noty($this->selected_id < 1 ? 'Rol creado' : 'Rol Actualizado', 'noty', false, 'close-modal');
        $this->resetUI();
    }

    public function Destroy(Role $rol)
    {
        $rol->delete();
        $this->noty('Se eliminó el rol');
    }


}
