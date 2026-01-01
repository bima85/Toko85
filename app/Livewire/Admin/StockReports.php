<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Store;
use App\Models\Warehouse;
use App\Models\PurchaseItem;
use App\Models\StockAdjustment;
use App\Models\StockBatch;
use App\Exports\StockReportExport;
use App\Exports\StockReportWithAdjustmentsExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.admin')]
class StockReports extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $searchAdjustments = '';
    public $activeTab = 'store'; // 'store' or 'warehouse'
    public $filterStoreId;
    public $filterWarehouseId;
    public $currentPage = 1;
    public $adjustmentsPage = 1;
    public $perPage = 25;
    public $perPageAdjustments = 10;

    protected $queryString = [
        'activeTab' => ['except' => 'store'],
        'search' => ['except' => ''],
        'searchAdjustments' => ['except' => ''],
        'currentPage' => ['except' => 1],
        'adjustmentsPage' => ['except' => 1],
    ];

    // CRUD properties
    public $showAdjustmentModal = false;
    public $editingAdjustmentId = null;
    public $adjustment_product_id;
    public $adjustment_store_id;
    public $adjustment_warehouse_id;
    public $adjustment_location; // 'store' or 'warehouse'
    public $adjustment_type = 'add';
    public $adjustment_stok_awal;
    public $adjustment_stok_masuk;
    public $adjustment_total_stok = 0;
    public $adjustment_unit;
    public $adjustment_reason;
    public $adjustment_date;
    public $adjustment_product_unit;

    // Selection properties
    public array $selectedAdjustments = [];
    public bool $selectAllAdjustments = false;

    public function mount()
    {
        // Initialize with first store and warehouse
        $this->filterStoreId = Store::first()?->id ?? 1;
        $this->filterWarehouseId = Warehouse::first()?->id ?? 1;

        // Read currentPage from query string
        $this->currentPage = (int) request('currentPage', 1);
    }

    public function updatingSearch()
    {
        $this->currentPage = 1;
    }

    public function updatedSearch()
    {
        $this->currentPage = 1;
    }

    public function updatingSearchAdjustments()
    {
        $this->adjustmentsPage = 1;
    }

    public function updatingPerPage()
    {
        $this->currentPage = 1;
    }

    public function updatingPerPageAdjustments()
    {
        $this->adjustmentsPage = 1;
    }

    public function updatedAdjustmentStokAwal($value)
    {
        $this->adjustment_stok_awal = (float) $value;
        $this->calculateTotalStok();
    }

    public function updatedAdjustmentStokMasuk($value)
    {
        $this->adjustment_stok_masuk = (float) $value;
        $this->calculateTotalStok();
    }

    private function calculateTotalStok()
    {
        $stokAwal = (float) ($this->adjustment_stok_awal ?? 0);
        $stokMasuk = (float) ($this->adjustment_stok_masuk ?? 0);
        $this->adjustment_total_stok = $stokAwal + $stokMasuk;
    }

    public function updatedAdjustmentProductId($value)
    {
        if ($value) {
            $product = Product::find($value);
            if ($product && $product->unit) {
                $this->adjustment_product_unit = $product->unit->nama_satuan;
            }
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->currentPage = 1;
        $this->adjustmentsPage = 1;
    }

    public function setPage($page)
    {
        $this->currentPage = max(1, (int) $page);
        $this->dispatch('pageChanged');
    }

    public function setAdjustmentsPage($page)
    {
        $this->adjustmentsPage = max(1, (int) $page);
        $this->dispatch('adjustmentsPageChanged');
    }

    public function nextPage()
    {
        $this->currentPage++;
    }

    public function previousPage()
    {
        $this->currentPage = max(1, $this->currentPage - 1);
    }

    public function goToAdjustmentsPage($page)
    {
        $this->adjustmentsPage = max(1, (int) $page);
    }

    public function nextAdjustmentsPage()
    {
        $this->adjustmentsPage++;
    }

    public function previousAdjustmentsPage()
    {
        $this->adjustmentsPage = max(1, $this->adjustmentsPage - 1);
    }

    #[Computed]
    public function adjustments()
    {
        return StockAdjustment::with(['product', 'store', 'warehouse', 'user'])
            ->when($this->activeTab === 'store', function ($query) {
                return $query->whereNotNull('store_id')
                    ->where('store_id', $this->filterStoreId);
            })
            ->when($this->activeTab === 'warehouse', function ($query) {
                return $query->whereNotNull('warehouse_id')
                    ->where('warehouse_id', $this->filterWarehouseId);
            })
            ->when($this->searchAdjustments, function ($query) {
                return $query->where(function ($q) {
                    $q->whereHas('product', function ($productQuery) {
                        $productQuery->where('nama_produk', 'like', '%' . $this->searchAdjustments . '%')
                            ->orWhere('kode_produk', 'like', '%' . $this->searchAdjustments . '%');
                    })
                        ->orWhere('reason', 'like', '%' . $this->searchAdjustments . '%');
                });
            })
            ->latest()
            ->paginate($this->perPageAdjustments, ['*'], 'adjustments_page', $this->adjustmentsPage);
    }

    public function render()
    {
        if ($this->activeTab === 'store') {
            $stocks = $this->getStoreStocks();
        } else {
            $stocks = $this->getWarehouseStocks();
        }

        $batchTotals = $this->getStockBatchTotalByProduct();

        // Compute hold totals for products shown on the current stocks page
        $holdTotals = $this->getHoldTotalsForStocks($stocks);

        return view('livewire.admin.stock-reports', [
            'stocks' => $stocks,
            'batchTotals' => $batchTotals,
            'adjustments' => $this->adjustments,
            'holdTotals' => $holdTotals,
        ]);
    }

    /**
     * Compute hold totals (qty) for products in the provided stocks paginator/collection
     * Returns array keyed by product_id => total_hold_qty
     */
    private function getHoldTotalsForStocks($stocks)
    {
        $productIds = collect($stocks->items())->pluck('product_id')->unique()->filter()->toArray();

        if (empty($productIds)) {
            return [];
        }

        return StockBatch::whereIn('product_id', $productIds)
            ->where('status', 'hold')
            ->where('qty', '>', 0)
            ->groupBy('product_id')
            ->selectRaw('product_id, SUM(qty) as total_qty')
            ->pluck('total_qty', 'product_id')
            ->toArray();
    }

    private function getStoreStocks()
    {
        $store = Store::find($this->filterStoreId);

        // Ambil data dari StockBatch (sumber data yang paling akurat untuk stok saat ini)
        // Filter hanya batch dengan qty > 0 (active)
        $batches = StockBatch::active()
            ->with(['product.category', 'product.subcategory'])
            ->where('location_type', 'store')
            ->where('location_id', $this->filterStoreId)
            ->get()
            ->groupBy('product_id')
            ->map(function ($items) use ($store) {
                $product = $items->first()->product;
                $totalQty = $items->sum('qty');

                // Ambil adjustment terakhir untuk produk ini di store ini
                $lastAdjustment = StockAdjustment::where('product_id', $product->id)
                    ->where('store_id', $this->filterStoreId)
                    ->orderBy('adjustment_date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                // Hitung total stok masuk dan keluar dari semua adjustment
                $adjustments = StockAdjustment::where('product_id', $product->id)
                    ->where('store_id', $this->filterStoreId)
                    ->get();

                $totalStokMasuk = $adjustments->where('adjustment_type', 'add')->sum('quantity');
                $totalStokKeluar = $adjustments->where('adjustment_type', 'remove')->sum('quantity');

                // Stok awal bisa diambil dari adjustment pertama, atau 0 jika tidak ada
                $firstAdjustment = StockAdjustment::where('product_id', $product->id)
                    ->where('store_id', $this->filterStoreId)
                    ->orderBy('adjustment_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->first();

                $stokAwal = $firstAdjustment ? $firstAdjustment->stok_awal : 0;

                return (object) [
                    'id' => 'product-' . $product->id,
                    'product_id' => $product->id,
                    'product' => $product,
                    'stok_awal' => $stokAwal,
                    'stok_masuk' => $totalStokMasuk,
                    'stok_keluar' => $totalStokKeluar,
                    'stok_akhir' => $totalQty,
                    'total_stok' => $totalQty,
                    'unit' => $product->satuan,
                    'store' => $store,
                    'type' => 'batch',
                    'created_at' => $lastAdjustment ? $lastAdjustment->adjustment_date : now(),
                ];
            })
            ->values();

        $adjustments = $batches;

        // Apply search filter
        if ($this->search) {
            $adjustments = $adjustments->filter(function ($item) {
                $product = $item->product;
                $search = $this->search;
                return strpos(strtolower($product->nama_produk), strtolower($search)) !== false ||
                    strpos(strtolower($product->kode_produk), strtolower($search)) !== false ||
                    strpos(strtolower($product->category->nama_kategori ?? ''), strtolower($search)) !== false ||
                    strpos(strtolower($product->subcategory->nama_subkategori ?? ''), strtolower($search)) !== false;
            });
        }

        // Sort by created_at descending
        $adjustments = $adjustments->sortByDesc('created_at')->values();

        // Paginate the collection
        $perPage = $this->perPage;
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $adjustments->forPage($this->currentPage, $perPage),
            $adjustments->count(),
            $perPage,
            $this->currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    private function getWarehouseStocks()
    {
        $warehouse = Warehouse::find($this->filterWarehouseId);

        // Ambil data dari StockBatch (sumber data yang paling akurat untuk stok saat ini)
        // Filter hanya batch dengan qty > 0 (active)
        $batches = StockBatch::active()
            ->with(['product.category', 'product.subcategory'])
            ->where('location_type', 'warehouse')
            ->where('location_id', $this->filterWarehouseId)
            ->get()
            ->groupBy('product_id')
            ->map(function ($items) use ($warehouse) {
                $product = $items->first()->product;
                $totalQty = $items->sum('qty');

                // Ambil adjustment terakhir untuk produk ini di warehouse ini
                $lastAdjustment = StockAdjustment::where('product_id', $product->id)
                    ->where('warehouse_id', $this->filterWarehouseId)
                    ->orderBy('adjustment_date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                // Hitung total stok masuk dan keluar dari semua adjustment
                $adjustments = StockAdjustment::where('product_id', $product->id)
                    ->where('warehouse_id', $this->filterWarehouseId)
                    ->get();

                $totalStokMasuk = $adjustments->where('adjustment_type', 'add')->sum('quantity');
                $totalStokKeluar = $adjustments->where('adjustment_type', 'remove')->sum('quantity');

                // Stok awal bisa diambil dari adjustment pertama, atau 0 jika tidak ada
                $firstAdjustment = StockAdjustment::where('product_id', $product->id)
                    ->where('warehouse_id', $this->filterWarehouseId)
                    ->orderBy('adjustment_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->first();

                $stokAwal = $firstAdjustment ? $firstAdjustment->stok_awal : 0;

                return (object) [
                    'id' => 'product-' . $product->id,
                    'product_id' => $product->id,
                    'product' => $product,
                    'stok_awal' => $stokAwal,
                    'stok_masuk' => $totalStokMasuk,
                    'stok_keluar' => $totalStokKeluar,
                    'stok_akhir' => $totalQty,
                    'total_stok' => $totalQty,
                    'unit' => $product->satuan,
                    'warehouse' => $warehouse,
                    'type' => 'batch',
                    'created_at' => $lastAdjustment ? $lastAdjustment->adjustment_date : now(),
                ];
            })
            ->values();

        $adjustments = $batches;

        // Apply search filter
        if ($this->search) {
            $adjustments = $adjustments->filter(function ($item) {
                $product = $item->product;
                $search = $this->search;
                return strpos(strtolower($product->nama_produk), strtolower($search)) !== false ||
                    strpos(strtolower($product->kode_produk), strtolower($search)) !== false ||
                    strpos(strtolower($product->category->nama_kategori ?? ''), strtolower($search)) !== false ||
                    strpos(strtolower($product->subcategory->nama_subkategori ?? ''), strtolower($search)) !== false;
            });
        }

        // Sort by created_at descending
        $adjustments = $adjustments->sortByDesc('created_at')->values();

        // Paginate the collection
        $perPage = $this->perPage;
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $adjustments->forPage($this->currentPage, $perPage),
            $adjustments->count(),
            $perPage,
            $this->currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    public function createAdjustment()
    {
        return redirect()->route('admin.adjustments');
    }

    public function editAdjustment($id)
    {
        $adjustment = StockAdjustment::findOrFail($id);
        $this->editingAdjustmentId = $id;
        $this->adjustment_product_id = $adjustment->product_id;
        $this->adjustment_store_id = $adjustment->store_id;
        $this->adjustment_warehouse_id = $adjustment->warehouse_id;
        // determine which location is used
        if ($adjustment->store_id) {
            $this->adjustment_location = 'store';
        } elseif ($adjustment->warehouse_id) {
            $this->adjustment_location = 'warehouse';
        } else {
            $this->adjustment_location = null;
        }
        $this->adjustment_type = $adjustment->adjustment_type;
        $this->adjustment_total_stok = $adjustment->quantity;
        // For editing, we show the total as stok_awal (cannot split without additional data)
        $this->adjustment_stok_awal = $adjustment->quantity;
        $this->adjustment_stok_masuk = 0;
        $this->adjustment_reason = $adjustment->reason;
        $this->adjustment_date = $adjustment->adjustment_date->format('Y-m-d');
        // Load unit
        $this->adjustment_product_unit = $adjustment->unit_id ?? ($adjustment->product?->unit_id ?? null);
        $this->showAdjustmentModal = true;
        $this->dispatch('show-adjustment-modal');
    }

    public function saveAdjustment()
    {
        // Ensure numeric values
        $this->adjustment_stok_awal = (float) ($this->adjustment_stok_awal ?? 0);
        $this->adjustment_stok_masuk = (float) ($this->adjustment_stok_masuk ?? 0);
        $this->adjustment_total_stok = (float) ($this->adjustment_total_stok ?? 0);

        $this->validate([
            'adjustment_product_id' => 'required|exists:products,id',
            'adjustment_stok_awal' => 'required|numeric|min:0',
            'adjustment_stok_masuk' => 'required|numeric|min:0',
            'adjustment_product_unit' => 'required|exists:units,id',
            'adjustment_type' => 'required|in:add,remove',
            'adjustment_reason' => 'nullable|string|max:255',
            'adjustment_date' => 'required|date',
            'adjustment_location' => 'required|in:store,warehouse',
        ], [
            'adjustment_product_id.required' => 'Produk harus dipilih',
            'adjustment_stok_awal.required' => 'Stok awal harus diisi',
            'adjustment_stok_awal.numeric' => 'Stok awal harus berupa angka',
            'adjustment_stok_masuk.required' => 'Stok masuk harus diisi',
            'adjustment_stok_masuk.numeric' => 'Stok masuk harus berupa angka',
            'adjustment_product_unit.required' => 'Satuan harus dipilih',
            'adjustment_type.required' => 'Tipe penyesuaian harus dipilih',
            'adjustment_date.required' => 'Tanggal penyesuaian harus diisi',
            'adjustment_location.required' => 'Lokasi harus dipilih',
        ]);

        // Validate location-specific IDs
        if ($this->adjustment_location === 'store') {
            $this->validate(['adjustment_store_id' => 'required|exists:stores,id'], [
                'adjustment_store_id.required' => 'Toko harus dipilih',
            ]);
        } elseif ($this->adjustment_location === 'warehouse') {
            $this->validate(['adjustment_warehouse_id' => 'required|exists:warehouses,id'], [
                'adjustment_warehouse_id.required' => 'Gudang harus dipilih',
            ]);
        }

        $data = [
            'product_id' => $this->adjustment_product_id,
            'store_id' => $this->adjustment_location === 'store' ? $this->adjustment_store_id : null,
            'warehouse_id' => $this->adjustment_location === 'warehouse' ? $this->adjustment_warehouse_id : null,
            'adjustment_type' => $this->adjustment_type,
            'quantity' => $this->adjustment_total_stok,
            'stok_awal' => $this->adjustment_stok_awal,
            'stok_masuk' => $this->adjustment_stok_masuk,
            'unit_id' => $this->adjustment_product_unit,
            'reason' => $this->adjustment_reason,
            'adjustment_date' => $this->adjustment_date,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
        ];

        if ($this->editingAdjustmentId) {
            StockAdjustment::findOrFail($this->editingAdjustmentId)->update($data);
            session()->flash('message', 'Penyesuaian stok berhasil diperbarui.');
        } else {
            StockAdjustment::create($data);
            session()->flash('message', 'Penyesuaian stok berhasil dibuat.');
        }

        $this->resetAdjustmentForm();
        $this->showAdjustmentModal = false;
        $this->dispatch('hide-adjustment-modal');
    }

    public function deleteAdjustment($id)
    {
        try {
            StockAdjustment::findOrFail($id)->delete();
            $this->currentPage = 1;      // Reset laporan stok pagination
            $this->adjustmentsPage = 1;  // Reset adjustment pagination
            $this->search = '';           // Clear search filter
            session()->flash('message', 'Penyesuaian stok berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting adjustment: ' . $e->getMessage());
            session()->flash('error', 'Gagal menghapus penyesuaian: ' . $e->getMessage());
        }
    }

    public function showAdjustment($id)
    {
        return redirect()->route('admin.stock-adjustments.show', $id);
    }

    public function closeAdjustmentModal()
    {
        $this->resetAdjustmentForm();
        $this->showAdjustmentModal = false;
        $this->dispatch('hide-adjustment-modal');
    }

    private function resetAdjustmentForm()
    {
        $this->editingAdjustmentId = null;
        $this->adjustment_product_id = null;
        $this->adjustment_store_id = null;
        $this->adjustment_warehouse_id = null;
        $this->adjustment_type = 'add';
        $this->adjustment_stok_awal = null;
        $this->adjustment_stok_masuk = null;
        $this->adjustment_total_stok = 0;
        $this->adjustment_unit = null;
        $this->adjustment_product_unit = null;
        $this->adjustment_reason = null;
        $this->adjustment_date = date('Y-m-d');
        $this->adjustment_location = null;
    }

    public function exportExcel()
    {
        $stokTokoData = [];
        $stokGudangData = [];
        $allAdjustmentData = [];
        $noToko = 1;
        $noGudang = 1;
        $noAdj = 1;

        // ===== STOK TOKO =====
        $adjustmentsToko = StockAdjustment::with(['product.category', 'product.subcategory', 'unit', 'user'])
            ->whereNotNull('store_id')
            ->get();

        $groupedByProductToko = $adjustmentsToko->groupBy('product_id');

        foreach ($groupedByProductToko as $productId => $items) {
            $firstItem = $items->first();
            $product = $firstItem->product;

            $totalStokAwal = $items->sum('stok_awal');
            $totalStokMasuk = $items->sum('stok_masuk');
            $stokKeluar = $items->sum(function ($item) {
                return ($item->adjustment_type === 'remove') ? $item->quantity : 0;
            });
            $stokAkhir = $totalStokAwal + $totalStokMasuk - $stokKeluar;

            $stokTokoData[] = [
                $noToko++,
                $product->kode_produk ?? '-',
                $product->nama_produk ?? '-',
                $product->category->nama_kategori ?? '-',
                $product->subcategory->nama_subkategori ?? '-',
                $firstItem->unit->nama_unit ?? $product->satuan ?? '-',
                $totalStokAwal,
                $totalStokMasuk,
                $stokKeluar,
                $stokAkhir,
            ];
        }

        // ===== STOK GUDANG =====
        $adjustmentsGudang = StockAdjustment::with(['product.category', 'product.subcategory', 'unit', 'user'])
            ->whereNotNull('warehouse_id')
            ->get();

        $groupedByProductGudang = $adjustmentsGudang->groupBy('product_id');

        foreach ($groupedByProductGudang as $productId => $items) {
            $firstItem = $items->first();
            $product = $firstItem->product;

            $totalStokAwal = $items->sum('stok_awal');
            $totalStokMasuk = $items->sum('stok_masuk');
            $stokKeluar = $items->sum(function ($item) {
                return ($item->adjustment_type === 'remove') ? $item->quantity : 0;
            });
            $stokAkhir = $totalStokAwal + $totalStokMasuk - $stokKeluar;

            $stokGudangData[] = [
                $noGudang++,
                $product->kode_produk ?? '-',
                $product->nama_produk ?? '-',
                $product->category->nama_kategori ?? '-',
                $product->subcategory->nama_subkategori ?? '-',
                $firstItem->unit->nama_unit ?? $product->satuan ?? '-',
                $totalStokAwal,
                $totalStokMasuk,
                $stokKeluar,
                $stokAkhir,
            ];
        }

        // ===== ALL ADJUSTMENTS HISTORY =====
        $allAdjustments = StockAdjustment::with(['product', 'unit', 'user', 'store', 'warehouse'])
            ->orderByDesc('created_at')
            ->get();

        foreach ($allAdjustments as $adj) {
            $lokasi = $adj->store ? $adj->store->nama_toko : ($adj->warehouse ? $adj->warehouse->nama_gudang : '-');

            $allAdjustmentData[] = [
                $noAdj++,
                $adj->product->kode_produk ?? '-',
                $adj->product->nama_produk ?? '-',
                $lokasi,
                $adj->adjustment_type === 'add' ? 'Tambah' : 'Kurang',
                $adj->quantity,
                $adj->reason ?? '-',
                $adj->adjustment_date->format('d/m/Y'),
                $adj->created_at->format('H:i:s'),
                $adj->user->name ?? '-',
            ];
        }

        return Excel::download(
            new StockReportWithAdjustmentsExport($stokTokoData, $stokGudangData, $allAdjustmentData),
            'Laporan_Stok_Lengkap_' . date('Y-m-d_His') . '.xlsx'
        );
    }

    public function getTotalStokToko()
    {
        // Gunakan StockBatch sebagai sumber data yang paling akurat
        return StockBatch::where('location_type', 'store')->sum('qty');
    }

    public function getTotalStokGudang()
    {
        // Gunakan StockBatch sebagai sumber data yang paling akurat
        $actual = StockBatch::where('location_type', 'warehouse')
            ->where('status', 'aktual')
            ->where('qty', '>', 0)
            ->sum('qty');

        $hold = StockBatch::where('location_type', 'warehouse')
            ->where('status', 'hold')
            ->where('qty', '>', 0)
            ->sum('qty');

        return $actual - $hold;
    }

    public function getStockBatchTotalByProduct()
    {
        // Calculate per-product available quantities: sum(aktual) - sum(hold)
        $actual = StockBatch::where('qty', '>', 0)
            ->where('status', 'aktual')
            ->get()
            ->groupBy('product_id')
            ->map(function ($items) {
                $product = $items->first()->product;
                return (object) [
                    'product_id' => $product->id,
                    'actual_qty' => $items->sum('qty'),
                ];
            });

        $holds = StockBatch::where('qty', '>', 0)
            ->where('status', 'hold')
            ->get()
            ->groupBy('product_id')
            ->map(function ($items) {
                $product = $items->first()->product;
                return (object) [
                    'product_id' => $product->id,
                    'hold_qty' => $items->sum('qty'),
                ];
            });

        $batches = $actual->mapWithKeys(function ($a) use ($holds) {
            $hold = $holds[$a->product_id]->hold_qty ?? 0;
            return [$a->product_id => (object)[
                'product_id' => $a->product_id,
                'total_qty' => $a->actual_qty - $hold,
            ]];
        });

        // Convert to keyed array for template access
        $result = [];
        foreach ($batches as $productId => $batch) {
            $result[$productId] = $batch;
        }
        return $result;
    }

    // Selection methods for adjustments
    public function toggleSelectAdjustment($adjustmentId)
    {
        if (in_array($adjustmentId, $this->selectedAdjustments)) {
            $this->selectedAdjustments = array_filter($this->selectedAdjustments, fn($id) => $id != $adjustmentId);
        } else {
            $this->selectedAdjustments[] = $adjustmentId;
        }

        $this->updateSelectAllAdjustmentsState();
    }

    public function updatedSelectAllAdjustments()
    {
        if ($this->selectAllAdjustments) {
            // Select all adjustments on current page
            $this->selectedAdjustments = $this->adjustments->pluck('id')->toArray();
        } else {
            $this->selectedAdjustments = [];
        }
    }

    public function updatedSelectedAdjustments()
    {
        // Update state when checkbox changes
        $this->updateSelectAllAdjustmentsState();
    }

    public function updateSelectAllAdjustmentsState()
    {
        $totalAdjustments = $this->adjustments->count();
        $selectedCount = count($this->selectedAdjustments);

        $this->selectAllAdjustments = $totalAdjustments > 0 && $selectedCount === $totalAdjustments;
    }

    public function deleteSelectedAdjustments()
    {
        if (empty($this->selectedAdjustments)) {
            session()->flash('message', 'Pilih minimal satu penyesuaian untuk dihapus');
            return;
        }

        try {
            $adjustmentIds = $this->selectedAdjustments;
            $count = StockAdjustment::whereIn('id', $adjustmentIds)->delete();

            if ($count > 0) {
                $this->selectedAdjustments = [];
                $this->selectAllAdjustments = false;
                $this->adjustmentsPage = 1;
                $this->currentPage = 1;  // Reset laporan stok pagination
                $this->search = '';       // Clear search filter

                session()->flash('message', "$count penyesuaian berhasil dihapus!");
            } else {
                session()->flash('message', 'Tidak ada penyesuaian yang dihapus');
            }
        } catch (\Exception $e) {
            Log::error('Error deleting adjustments: ' . $e->getMessage());
            session()->flash('error', 'Gagal menghapus penyesuaian: ' . $e->getMessage());
        }
    }

    public function clearAdjustmentSelection()
    {
        $this->selectedAdjustments = [];
        $this->selectAllAdjustments = false;
    }

    public function clearSearchAdjustments()
    {
        $this->searchAdjustments = '';
        $this->adjustmentsPage = 1;
    }
}
