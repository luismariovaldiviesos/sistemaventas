<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;
use App\Models\User;
use DB;

class Permisos extends Component
{
    use WithPagination;
    public $permissionName = '', $selected_id = 0;
    public $action = 'Listado', $componentName = 'Permisos del sistema', $search, $form = false;
    private $pagination = 10;
    protected $paginationTheme = 'tailwind';


    public function render()
    {
        if (strlen($this->search) > 0)
        $info = Permission::where('name', 'like', "%{$this->search}%")->paginate($this->pagination);
    else
        $info = Permission::paginate($this->pagination);
        return view('livewire.permisos.component', ['permisos' => $info])->layout('layouts.theme.app');
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
        $this->reset('permissionName', 'selected_id', 'search', 'action', 'componentName', 'form');
    }

    public function Edit(Permission $permiso)
    {
        $this->selected_id = $permiso->id;
        $this->permissionName = $permiso->name;
        $this->action = 'Editar';
        $this->form = true;

    }
    public function Store()
    {
        $rules = ['permissionName' => "required|min:2|unique:permissions,name, {$this->selected_id}"];

        $messages = [
            'permissionName.required' => 'el nombre del permiso es requerido',
            'permissionName.min' => 'el tamaño del permiso debe ser minimo dos caracteres',
            'permissionName.unique' => 'el nombre del permiso ya existe'
        ];

        $this->validate($rules,$messages);

        $permiso = Permission::updateOrCreate(
            ['id' => $this->selected_id],
            ['name' => $this->permissionName]
        );

        $this->noty($this->selected_id < 1 ? 'Permiso creado' : 'Permiso Actualizado', 'noty', false, 'close-modal');
        $this->resetUI();
    }

    public function Destroy(Permission $permiso)
    {
        $permiso->delete();
        $this->noty('Se eliminó el rol');
    }



}
