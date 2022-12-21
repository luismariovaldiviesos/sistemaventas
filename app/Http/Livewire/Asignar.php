<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Asignar extends Component
{
    use WithPagination;


    public $action = 'Listado', $componentName = 'Asignar permisos', $form = false;
    private $pagination = 10;
    protected $paginationTheme = 'tailwind';

    public  $permisosSelected=[], $oldPermisos=[];
    public $role = 'ELEGIR';




    public function render()
    {

        $permisos = Permission::select('name','id', DB::raw("0 as checked"))
        ->orderBy('id','asc')
        ->paginate($this->pagination);
        //dd($permisos);
        if ($this->role != 'ELEGIR') {
            $list = Permission::join('role_has_permissions as rp', 'rp.permission_id','permission_id')
            ->where('role_id',$this->role)->pluck('permission_id')->toArray();
            $this->oldPermisos = $list;
            //dd($this->oldPermisos);
        }
        if ($this->role != 'ELEGIR') {
            foreach ($permisos as $permiso) {
                $role = Role::find($this->role);
                $tienePermiso = $role->hasPermissionTo($permiso->name);
                if ($tienePermiso) {
                    $permiso->checked = 1;
                }
            }
        }


        return view('livewire.asignar.component',
            [
                'roles' => Role::orderBy('name','asc')->get(),
                'permisos' => $permisos
            ]

        )->layout('layouts.theme.app');;
    }


    public $listeners = [
        'resetUI',
        'Destroy',
        'revokeall' => 'removeAll'
    ];

    public function noty($msg, $eventName = 'noty', $reset = true, $action = '')
    {
        $this->dispatchBrowserEvent($eventName, ['msg' => $msg, 'type' => 'success', 'action' => $action]);
        if ($reset) $this->resetUI();
    }


    public function syncAll()
    {
        if ($this->role == 'ELEGIR') {
            $this->noty('Elige un rol válido', 'noty', false);
            return;
        }
        $role = Role::find($this->role);
        $permisos = Permission::pluck('id')->toArray();
        $role->syncPermissions($permisos);
        //$this->emit('syncall',"Se sincronizaron todos lo permisos al role $role->name");
        $this->noty("Se sincronizaron todos lo permisos al rol $role->name", 'noty', false);
    }

    public function removeAll()
    {
        if ($this->role == 'ELEGIR') {
            $this->noty('Elige un rol válido', 'noty', false);
            return;
        }

        $role = Role::find($this->role);
        $role->syncPermissions([0]);
        $this->noty("Se quitaron todos lo permisos al rol $role->name", 'noty', false);
    }


    public function syncPermiso($state, $permisoName)
    {
        //dd($permisoName);
        if ($this->role != 'ELEGIR') {
            $roleName = Role::find($this->role);
            if ($state) {
                $roleName->givePermissionTo($permisoName);
                //$this->emit('permi', "Permiso asignado al rol: $roleName->name");
                $this->noty("Permiso asignado al rol $roleName->name", 'noty', false);
            }else {
                $roleName->revokePermissionTo($permisoName);
                //$this->emit('permi', "Permiso revocado al rol: $roleName->name");
                $this->noty("Permiso revocado  al rol $roleName->name", 'noty', false);
            }
        }else {
            $this->noty('Elige un rol válido', 'noty', false);
        }
    }

}
