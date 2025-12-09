<?php

namespace App\Livewire\Admin\Sales;

use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SalesCreate extends Component
{
    public $date;
    public $customer_name;
    public $total_amount;

    protected function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function submit()
    {
        $validated = $this->validate();

        $sale = Sale::create([
            'date' => $validated['date'],
            'customer_name' => $validated['customer_name'],
            'total_amount' => $validated['total_amount'],
            'user_id' => Auth::id(),
        ]);

        session()->flash('success', 'Penjualan berhasil dibuat.');
        return redirect()->route('admin.sales');
    }

    public function render()
    {
        return view('livewire.admin.sales.sales-create');
    }
}
