<?php

namespace App\Livewire\StockCard;

use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockCard;
use Livewire\Component;

class StockCardForm extends Component
{
    public $stockCard = null;
    public $product_id = '';
    public $batch_id = '';
    public $type = 'in';
    public $qty = '';
    public $from_location = '';
    public $to_location = '';
    public $reference_type = '';
    public $reference_id = '';
    public $note = '';

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'batch_id' => 'nullable|exists:stock_batches,id',
        'type' => 'required|in:in,out,adjustment,return',
        'qty' => 'required|numeric|min:0.01',
        'from_location' => 'nullable|string|max:255',
        'to_location' => 'nullable|string|max:255',
        'reference_type' => 'nullable|string|max:50',
        'reference_id' => 'nullable|integer',
        'note' => 'nullable|string|max:500',
    ];

    public function mount($stockCard = null)
    {
        if ($stockCard) {
            $this->stockCard = $stockCard;
            $this->fill($stockCard->only(
                'product_id',
                'batch_id',
                'type',
                'qty',
                'from_location',
                'to_location',
                'reference_type',
                'reference_id',
                'note'
            ));
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->stockCard) {
            $this->stockCard->update($this->validated());
            $message = 'Kartu stok berhasil diperbarui';
        } else {
            StockCard::create($this->validated());
            $message = 'Kartu stok berhasil dibuat';
        }

        $this->dispatch('toast', [
            'message' => $message,
            'type' => 'success'
        ]);

        return redirect()->route('stock-card.index');
    }

    public function getProductsProperty()
    {
        return Product::orderBy('nama_produk')->get();
    }

    public function getBatchesProperty()
    {
        if (!$this->product_id) {
            return [];
        }

        return StockBatch::where('product_id', $this->product_id)
            ->orderBy('batch_number')
            ->get();
    }

    public function render()
    {
        return view('livewire.stock-card.stock-card-form', [
            'products' => $this->products,
            'batches' => $this->batches,
        ])->layout('layouts.admin');
    }
}
