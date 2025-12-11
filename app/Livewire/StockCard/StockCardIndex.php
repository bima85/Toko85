<?php

namespace App\Livewire\StockCard;

use App\Exports\StockCardExport;
use App\Models\StockCard;
use App\Repositories\StockCardRepository;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Maatwebsite\Excel\Facades\Excel;

class StockCardIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $filter_type = '';
    public $filter_product = '';
    public $per_page = 15;
    public $selectAll = false;
    public $selectedCards = [];
    public $groupByProduct = true;
    public $expandedGroups = [];
    protected $queryString = ['search', 'filter_type', 'filter_product', 'groupByProduct'];

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

        if ($this->groupByProduct) {
            // When grouping, order by product first, then by date
            $query->orderBy('product_id')->orderBy('created_at', 'desc');
        }

        return $query->paginate($this->per_page);
    }

    public function getGroupedStockCardsProperty()
    {
        if (!$this->groupByProduct) {
            return null;
        }

        return $this->stockCards->groupBy('product_id');
    }

    public function toggleGroup($productId)
    {
        if (in_array($productId, $this->expandedGroups)) {
            $this->expandedGroups = array_values(array_diff($this->expandedGroups, [$productId]));
        } else {
            $this->expandedGroups[] = $productId;
        }
    }

    public function expandAllGroups()
    {
        $this->expandedGroups = $this->stockCards->pluck('product_id')->unique()->toArray();
    }

    public function collapseAllGroups()
    {
        $this->expandedGroups = [];
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

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedCards = $this->stockCards->pluck('id')->toArray();
        } else {
            $this->selectedCards = [];
        }
    }

    public function toggleCardSelection($id)
    {
        if (in_array($id, $this->selectedCards)) {
            $this->selectedCards = array_diff($this->selectedCards, [$id]);
        } else {
            $this->selectedCards[] = $id;
        }
        $this->selectAll = false;
    }

    public function bulkDelete()
    {
        if (empty($this->selectedCards)) {
            $this->dispatch('toast', [
                'message' => 'Pilih minimal satu kartu stok untuk dihapus',
                'type' => 'warning'
            ]);
            return;
        }

        $count = count($this->selectedCards);
        StockCard::whereIn('id', $this->selectedCards)->delete();
        $this->selectedCards = [];
        $this->selectAll = false;
        $this->resetPage();
        $this->dispatch('toast', [
            'message' => $count . ' kartu stok berhasil dihapus',
            'type' => 'success'
        ]);
    }

    public function exportToPdf()
    {
        // Akan diimplementasikan dengan library PDF seperti TCPDF atau mPDF
        $this->dispatch('toast', [
            'message' => 'Fitur export PDF sedang dalam pengembangan',
            'type' => 'info'
        ]);
    }

    public function exportToExcel()
    {
        $filename = 'kartu-stok-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(
            new StockCardExport($this->search, $this->filter_type, $this->groupByProduct),
            $filename
        );
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
