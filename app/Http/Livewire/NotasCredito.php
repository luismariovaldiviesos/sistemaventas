<?php

namespace App\Http\Livewire;

use App\Models\Factura;
use Livewire\Component;
use Livewire\WithPagination;

class NotasCredito extends Component
{
    use WithPagination;


    public $action = 'Listado', $componentName='LISTADO DE NOTAS DE CRÉDITO', $search, $form = false;
    private $pagination =20;
    protected $paginationTheme='tailwind';

    public function render()
    {
        if (strlen($this->search) > 0) {
            $info = Factura::withTrashed() // Incluir facturas soft deleted
                ->where('codDoc', '04') // Solo notas de crédito
                ->where(function ($query) {
                    $query->where('secuencial', 'like', "%{$this->search}%")
                        ->orWhereHas('customer', function ($q) {
                            $q->where('businame', 'like', "%{$this->search}%");
                        })
                        ->orWhereDate('fechaAutorizacion', 'like', "%{$this->search}%");
                })
                ->whereNotNull('numeroAutorizacion') // Asegurar que tenga autorización
                ->orderBy('fechaAutorizacion', 'desc')
                ->paginate($this->pagination);
        } else {
            // Si no hay búsqueda, solo traer notas de crédito
            $info = Factura::withTrashed() // Incluir facturas eliminadas lógicamente
                ->where('codDoc', '04')
                ->orderBy('fechaAutorizacion', 'desc')
                ->paginate($this->pagination);
        }

        //dd($info);




        // Devuelve las facturas al componente de Livewire para la vista.
        return view('livewire.notascredito.component', ['facturas' => $info])
            ->layout('layouts.theme.app');
    }


    public function setNC(Factura $factura)
    {
        // Aquí puedes agregar la lógica para establecer la nota de crédito
        // Por ejemplo, podrías cambiar el estado de la factura o agregar una nota de crédito asociada

        // Ejemplo de cambio de estado
        $factura->deleted_at = now();
        $factura->save();
        // Finalmente, puedes actualizar la vista o realizar alguna acción adicional
        $this->emit('Nota de créidto registrada ');
    }


}
