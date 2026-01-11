<?php

namespace App\Livewire\Admin;

use App\Models\Sale;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class DeliveryNotesIndex extends Component
{
    public function render()
    {
        $sales = Sale::with(['customer', 'saleItems'])->orderByDesc('id')->get();

        return view('livewire.admin.delivery-notes.index', compact('sales'));
    }
}
