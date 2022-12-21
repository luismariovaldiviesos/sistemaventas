<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class Customers extends Component
{
    use WithPagination;

    public $businame = "", $typeidenti ="", $valueidenti ="",$address ="",$email ="",$phone ="", $notes='', $selected_id =0;

    public $action = 'Listado', $componentName = 'CLIENTES', $search = '', $form = false;
    private $pagination =10;
    protected $paginationTheme = 'tailwind';

    public function render()
    {
        if (strlen($this->search)> 0) {
            $customers = Customer::where('businame','like',"%{$this->search}%")
            ->orWhere('valueidenti','like',"%{$this->search}%")
            ->orWhere('phone','like',"%{$this->search}%")
            ->orWhere('address','like',"%{$this->search}%")
            ->orderBy('businame', 'asc')
            ->paginate($this->pagination);
        }
        else
        {
            $customers = Customer::orderBy('businame', 'asc')
            ->paginate($this->pagination);
        }
        return view('livewire.customers.component',[
            'customers' => $customers

        ])->layout('layouts.theme.app');
    }

    public function noty($msg, $eventName = 'noty', $reset = true, $action =""){
        $this->dispatchBrowserEvent($eventName, ['msg'=>$msg, 'type' => 'success', 'action' => $action ]);
        if($reset) $this->resetUI();
    }

    public function  addNew()
    {
        $this->resetUI();
        $this->form = true;
        $this->action = 'Agregar';
    }

    public  function  CloseModal()
    {
        $this->resetUI();
        $this->noty(null, 'close-modal');
    }

    public function resetUI()
    {
        $this->resetPage();
        $this->resetValidation();
        $this->reset('businame','typeidenti','valueidenti','address','email','phone','notes','selected_id','search','form');
    }

    public function Edit(Customer $customer){

        $this->selected_id = $customer->id;
        $this->businame = $customer->businame;
        $this->typeidenti = $customer->typeidenti;
        $this->valueidenti = $customer->valueidenti;
        $this->address = $customer->address;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->notes = $customer->notes;
        $this->form = true;

    }

    public $listeners = ['resetUI', 'Destroy'];

    public function Store()
    {
        $this->validate(Customer::rules($this->selected_id), Customer::$messages);

        Customer::updateOrCreate(
            ['id' => $this->selected_id],
            [
                'businame' => $this->businame,
                'typeidenti' => $this->typeidenti,
                'valueidenti' => $this->valueidenti,
                'address' => $this->address,
                'email' => $this->email,
                'phone' => $this->phone,
                'notes' => $this->notes
            ]

            );
            $this->noty($this->selected_id > 0 ? 'Cliente actualizado ' : 'Cliente registrado', 'noty', false, 'close-modal' );
            $this->resetUI();

    }

    public function Destroy(Customer $customer)
    {
        if($customer->orders->count() < 1)
        {
            $customer->delete();
            $this->noty("El cliente <b>$customer->businame </b> ha sido elmininado");
        }else{
            $this->noty("El cliente tiene ventas relacionadas, no es posible eliminarlo");
        }
    }

}
