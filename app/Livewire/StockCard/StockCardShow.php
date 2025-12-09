<?php

namespace App\Livewire\StockCard;

use App\Models\StockCard;
use Livewire\Component;

class StockCardShow extends Component
{
    public StockCard $stockCard;

    public function mount(StockCard $stockCard)
    {
        $this->stockCard = $stockCard;
    }

    public function getTransactionTypeProperty()
    {
        return match ($this->stockCard->type) {
            'in' => 'Masuk',
            'out' => 'Keluar',
            'adjustment' => 'Penyesuaian',
            'return' => 'Retur',
            default => $this->stockCard->type,
        };
    }

    public function getReferenceTypeProperty()
    {
        return match ($this->stockCard->reference_type) {
            'purchase' => 'Pembelian',
            'sale' => 'Penjualan',
            'adjustment' => 'Penyesuaian',
            'return' => 'Retur',
            'transfer' => 'Pemindahan',
            default => $this->stockCard->reference_type ?? '-',
        };
    }

    public function deleteStockCard()
    {
        $this->stockCard->delete();

        return redirect()->route('stock-card.index')
            ->with('success', 'Kartu stok berhasil dihapus');
    }

    public function render()
    {
        return view('livewire.stock-card.stock-card-show', [
            'transactionType' => $this->transactionType,
            'referenceType' => $this->referenceType,
        ])->layout('layouts.admin');
    }
}
