<?php

namespace App\Livewire\StockCard;

use App\Models\StockCard;
use App\Repositories\StockCardRepository;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class StockCardIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $filter_type = '';
    public $filter_product = '';
    public $per_page = 15;
    protected $queryString = ['search', 'filter_type', 'filter_product'];

    protected $listeners = ['refreshStockCard' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterProduct()
    {
        $this->resetPage();
    }

    public function getStockCardsProperty()
    {
        $query = StockCard::query()
            ->with(['product', 'batch'])
            ->latest();

        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('nama_produk', 'like', "%{$this->search}%")
                    ->orWhere('kode_produk', 'like', "%{$this->search}%");
            })->orWhere('note', 'like', "%{$this->search}%");
        }

        if ($this->filter_type) {
            $query->where('type', $this->filter_type);
        }

        if ($this->filter_product) {
            $query->where('product_id', $this->filter_product);
        }

        return $query->paginate($this->per_page);
    }

    public function getTransactionTypesProperty()
    {
        return [
            'in' => 'Masuk',
            'out' => 'Keluar',
            'adjustment' => 'Penyesuaian',
            'return' => 'Retur',
        ];
    }

    public function getTotalInProperty()
    {
        return StockCard::where('type', 'in')->sum('qty');
    }

    public function getTotalOutProperty()
    {
        return StockCard::where('type', 'out')->sum('qty');
    }

    public function getNetProperty()
    {
        return $this->totalIn - $this->totalOut;
    }

    public function getCommonUnitProperty()
    {
        // Get the most common unit from stock cards
        $commonUnit = StockCard::whereHas('product')
            ->with('product')
            ->latest()
            ->first();

        if ($commonUnit && $commonUnit->product && $commonUnit->product->satuan) {
            return $commonUnit->product->satuan;
        }

        // Fallback to first product's satuan
        $product = \App\Models\Product::whereNotNull('satuan')->first();
        return $product ? $product->satuan : 'unit';
    }

    public function deleteStockCard($id)
    {
        $stockCard = StockCard::find($id);

        if ($stockCard) {
            $stockCard->delete();
            $this->dispatch('toast', [
                'message' => 'Kartu stok berhasil dihapus',
                'type' => 'success'
            ]);
        }
    }

    public function exportToPdf()
    {
        // Akan diimplementasikan dengan library PDF seperti TCPDF atau mPDF
        $this->dispatch('toast', [
            'message' => 'Fitur export PDF sedang dalam pengembangan',
            'type' => 'info'
        ]);
    }

    public function render()
    {
        return view('livewire.stock-card.stock-card-index', [
            'stockCards' => $this->stockCards,
            'transactionTypes' => $this->transactionTypes,
            'totalIn' => $this->totalIn,
            'totalOut' => $this->totalOut,
            'net' => $this->net,
            'commonUnit' => $this->commonUnit,
        ])->layout('layouts.admin');
    }
}
