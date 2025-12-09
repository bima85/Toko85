<?php

namespace App\Livewire\StockCard;

use App\Models\StockCard;
use Livewire\Component;
use Livewire\WithPagination;

class StockCardTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = ['search', 'sortBy', 'sortDirection'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function getStockCardsProperty()
    {
        $query = StockCard::with(['product', 'batch']);

        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('sku', 'like', "%{$this->search}%");
            });
        }

        return $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
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

    public function render()
    {
        return view('livewire.stock-card.stock-card-table', [
            'stockCards' => $this->stockCards,
        ]);
    }
}
