<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('My orders - DCodeMania')]
class MyOrdersPage extends Component
{
    use WithPagination;

    public function render()
    {
        $myOrders = Order::where('user_id', auth()->id())->latest();
        return view('livewire.my-orders-page', [
            'orders' => $myOrders->paginate(10)
        ]);
    }
}
