<?php

namespace App\Livewire\Admin\Sales;

use App\Models\Sale;
use Livewire\Component;
use Livewire\WithPagination;

class SalesIndex extends Component
{
    use WithPagination;

    public $kuli = 0;

    public function render()
    {
        $sales = Sale::with('user')->latest()->paginate(10);
        return view('livewire.admin.sales.sales-index', [
            'sales' => $sales,
        ]);
    }
}
