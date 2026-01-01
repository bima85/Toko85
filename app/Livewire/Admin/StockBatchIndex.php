<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\Warehouse;
use App\Models\StockBatch;
use App\Models\StockCard;
use App\Models\Subcategory;
use App\Models\Unit;
use App\Services\StockBatchService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class StockBatchIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $location = '';

    #[Url]
    public string $satuan = '';

    #[Url]
    public int $per_page = 15;

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    // Inline create form properties
    public bool $showCreateForm = false;
    public bool $isCreatingBatch = false; // Flag untuk prevent double submission
    public ?int $createProductId = null;
    public string $createProductSearch = '';
    // Use untyped properties so a sentinel value like '__add__' can be selected in the dropdown
    public $createCategoryId = null;
    public $createSubcategoryId = null;

    // Inline create category/subcategory support
    public bool $showCreateCategoryInline = false;
    public bool $showCreateSubcategoryInline = false;
    public string $newCreateCategoryName = '';
    public string $newCreateSubcategoryName = '';
    public string $newCreateCategoryCode = '';
    public string $newCreateSubcategoryCode = '';

    public string $createLocationType = 'store';
    public ?int $createLocationId = null;
    public string $createNamaTumpukan = '';
    public float $createQty = 0;
    public string $createNote = '';
    public string $createSatuan = '';
    public string $createDate = '';

    // Quick add product modal
    public bool $showQuickAddProductModal = false;
    public string $quickProductName = '';
    public string $quickProductCode = '';
    public ?int $quickProductCategoryId = null;
    public ?int $quickProductSubcategoryId = null;
    public string $quickProductUnit = '';

    // Edit form properties
    public bool $showEditModal = false;
    public ?int $editBatchId = null;
    public ?int $editProductId = null;
    public ?int $editCategoryId = null;
    public ?int $editSubcategoryId = null;
    public string $editLocationType = 'store';
    public ?int $editLocationId = null;
    public string $editNamaTumpukan = '';
    public float $editQty = 0;
    public string $editNote = '';
    public string $editSatuan = '';
    public string $editDate = '';

    // Pagination for total per product
    public int $productPerPage = 10;
    public int $productPage = 1;

    // Tab for Total Stok section
    public string $stockSummaryTab = 'product'; // 'product' or 'location'

    // Filter tanggal untuk Ringkasan Stok
    public string $summaryDateFrom = '';
    public string $summaryDateTo = '';

    // Hold batch properties
    public bool $showCreateHoldForm = false;
    public bool $isCreatingHoldBatch = false;
    public ?int $holdProductId = null;
    public string $holdProductSearch = '';
    public $holdCategoryId = null;
    public $holdSubcategoryId = null;
    public string $holdLocationType = 'store';
    public ?int $holdLocationId = null;
    public string $holdNamaTumpukan = '';
    public float $holdQty = 0;
    public string $holdNote = '';
    public string $holdSatuan = '';
    public string $holdReason = '';

    // Selection properties
    public array $selectedBatches = [];
    public bool $selectAll = false;

    public function mount()
    {
        $this->holdProducts = [];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLocation()
    {
        $this->resetPage();
    }

    public function updatingSatuan()
    {
        $this->resetPage();
    }

    public function updatedCreateProductSearch($value)
    {
        try {
            // Only search if createProductId is not already set (i.e., user is typing manually)
            if (!empty($value) && !$this->createProductId) {
                // Extract product code from format [CODE] Name if present
                if (preg_match('/^\[([^\]]+)\]/', $value, $matches)) {
                    $productCode = $matches[1];
                    $product = Product::where('kode_produk', $productCode)->first();
                } else {
                    // Search by name or code directly
                    $product = Product::where('nama_produk', 'LIKE', '%' . $value . '%')
                        ->orWhere('kode_produk', 'LIKE', '%' . $value . '%')
                        ->first();
                }

                if ($product) {
                    $this->createProductId = $product->id;
                    $this->createCategoryId = $product->category_id;
                    $this->createSubcategoryId = $product->subcategory_id;
                    $this->createSatuan = $product->satuan ?? '';
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in updatedCreateProductSearch: ' . $e->getMessage());
        }
    }

    public function selectProduct($value)
    {
        try {
            // Cari produk berdasarkan nama
            $product = Product::where('nama_produk', $value)->first();

            if ($product) {
                $this->createProductId = $product->id;
                $this->createProductSearch = $value;
                $this->createCategoryId = $product->category_id;
                $this->createSubcategoryId = $product->subcategory_id;
                $this->createSatuan = $product->satuan ?? '';
            }
        } catch (\Exception $e) {
            Log::error('Error in selectProduct: ' . $e->getMessage());
        }
    }


    public function updatedCreateCategoryId($value)
    {
        // If user selected the special add option, open inline add and clear selection
        if ($value === '__add__') {
            $this->showCreateCategoryInline = true;
            $this->createCategoryId = null;
            return;
        }

        // Reset subcategory and product when category changes
        $this->createSubcategoryId = null;
        $this->createProductId = null;
        $this->createProductSearch = '';
        $this->createSatuan = '';
    }

    public function updatedCreateSubcategoryId($value)
    {
        // If user selected the special add option, open inline add and clear selection
        if ($value === '__add__') {
            $this->showCreateSubcategoryInline = true;
            $this->createSubcategoryId = null;
            return;
        }

        // Reset product when subcategory changes
        $this->createProductId = null;
        $this->createProductSearch = '';
        $this->createSatuan = '';
    }

    public function updatedEditCategoryId()
    {
        // Reset subcategory and product when category changes
        $this->editSubcategoryId = null;
        $this->editProductId = null;
    }

    public function updatedEditSubcategoryId()
    {
        // Reset product when subcategory changes
        $this->editProductId = null;
    }

    public function updatedCreateLocationType()
    {
        // Reset location ID ketika lokasi berubah
        $this->createLocationId = null;
    }

    public function updatedEditLocationType()
    {
        // Reset location ID ketika lokasi berubah di form edit
        $this->editLocationId = null;
    }

    #[Computed]
    public function filteredSubcategories()
    {
        if (!$this->createCategoryId) {
            return Subcategory::all();
        }
        return Subcategory::where('category_id', $this->createCategoryId)->get();
    }

    #[Computed]
    public function filteredProducts()
    {
        $query = Product::query();

        if ($this->createCategoryId) {
            $query->where('category_id', $this->createCategoryId);
        }

        if ($this->createSubcategoryId) {
            $query->where('subcategory_id', $this->createSubcategoryId);
        }

        return $query->get();
    }

    #[Computed]
    public function tumpukanSummary()
    {
        $locType = $this->location ?: null;
        return StockBatch::getTumpukanSummary($locType);
    }

    #[Computed]
    public function totalAllTumpukan()
    {
        $locType = $this->location ?: null;
        return StockBatch::getTotalQtyAllTumpukan($locType);
    }

    #[Computed]
    public function locationLabel()
    {
        $locations = ['store' => 'Toko', 'warehouse' => 'Gudang'];
        return $this->location ? $locations[$this->location] : 'Semua Lokasi';
    }

    #[Computed]
    public function totalPerProduct()
    {
        // Only include aktual stock, not hold batches
        $query = StockBatch::where('qty', '>', 0)->where('status', 'aktual');

        // Filter berdasarkan lokasi
        if ($this->location) {
            $query->byLocation($this->location);
        }

        // Filter berdasarkan tanggal ringkasan
        if ($this->summaryDateFrom) {
            $query->whereDate('created_at', '>=', $this->summaryDateFrom);
        }

        if ($this->summaryDateTo) {
            $query->whereDate('created_at', '<=', $this->summaryDateTo);
        }

        // Group by product_id dan hitung total qty (aktual)
        $totals = $query->with('product')
            ->get()
            ->groupBy('product_id')
            ->map(function ($batches) {
                $product = $batches->first()->product;
                $totalQty = $batches->sum('qty');
                $latestBatch = $batches->sortByDesc('created_at')->first();

                return (object) [
                    'product_id' => $product->id,
                    'product' => $product,
                    'total_qty' => $totalQty,
                    'satuan' => $product->satuan ?? 'N/A',
                    'category' => $product->category?->nama_kategori ?? '-',
                    'subcategory' => $product->subcategory?->nama_subkategori ?? '-',
                    'latest_date' => $latestBatch->created_at,
                ];
            })
            ->sortBy(fn($item) => $item->product->nama_produk)
            ->values();

        // Kurangi dengan jumlah HOLD untuk tiap produk (agar menampilkan stok tersedia)
        $holdQuery = StockBatch::where('status', 'hold')->where('qty', '>', 0);
        if ($this->location) {
            $holdQuery->byLocation($this->location);
        }
        if ($this->summaryDateFrom) {
            $holdQuery->whereDate('created_at', '>=', $this->summaryDateFrom);
        }
        if ($this->summaryDateTo) {
            $holdQuery->whereDate('created_at', '<=', $this->summaryDateTo);
        }

        $holdSums = $holdQuery->get()->groupBy('product_id')->map(fn($g) => $g->sum('qty'));

        $available = $totals->map(function ($item) use ($holdSums) {
            $hold = $holdSums[$item->product_id] ?? 0;
            $item->available_qty = (float) $item->total_qty - (float) $hold;
            return $item;
        });

        return $available;
    }

    public function getTotalPerProductPaginatedProperty()
    {
        $items = $this->totalPerProduct;
        $total = count($items);
        $start = ($this->productPage - 1) * $this->productPerPage;
        $end = $start + $this->productPerPage;

        return [
            'items' => $items->slice($start, $this->productPerPage),
            'total' => $total,
            'currentPage' => $this->productPage,
            'perPage' => $this->productPerPage,
            'lastPage' => ceil($total / $this->productPerPage),
            'from' => $total == 0 ? 0 : $start + 1,
            'to' => min($end, $total),
        ];
    }

    public function gotoProductPage($page)
    {
        $this->productPage = $page;
    }

    public function setStockSummaryTab($tab)
    {
        $this->stockSummaryTab = $tab;
    }

    // Computed properties for hold form
    #[Computed]
    public function categories()
    {
        return \App\Models\Category::orderBy('nama_kategori')->get();
    }

    #[Computed]
    public function subcategories()
    {
        return \App\Models\Subcategory::with('category')->orderBy('nama_subkategori')->get();
    }

    #[Computed]
    public function holdBatchOptions()
    {
        if (!$this->holdProductId) {
            return [];
        }

        // Ambil semua nama_tumpukan untuk produk ini, lalu normalisasi
        // (trim whitespace, hilangkan string kosong, dan unique secara case-insensitive)
        $names = StockBatch::where('product_id', $this->holdProductId)
            ->orderBy('nama_tumpukan')
            ->pluck('nama_tumpukan');

        $normalized = $names->map(function ($n) {
            return is_string($n) ? trim($n) : $n;
        })
            ->filter(function ($n) {
                return $n !== null && $n !== '';
            })
            // unique case-insensitive
            ->unique(function ($n) {
                return mb_strtolower($n);
            })
            ->values()
            ->sort()
            ->toArray();

        return $normalized;
    }

    public $holdProducts = [];

    public function updatedHoldProductSearch()
    {
        $this->updateHoldProducts();
    }

    private function updateHoldProducts()
    {
        $query = \App\Models\Product::query();

        if (!empty($this->holdProductSearch)) {
            $search = trim($this->holdProductSearch);
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'LIKE', '%' . $search . '%')
                    ->orWhere('kode_produk', 'LIKE', '%' . $search . '%');
            });
        }

        $this->holdProducts = $query->orderBy('nama_produk')
            ->limit(100)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'kode_produk' => $product->kode_produk,
                    'nama_produk' => $product->nama_produk,
                    'satuan' => $product->satuan,
                ];
            })
            ->toArray();
    }

    #[Computed]
    public function stores()
    {
        return \App\Models\Store::orderBy('nama_toko')->get();
    }

    #[Computed]
    public function warehouses()
    {
        return \App\Models\Warehouse::orderBy('nama_gudang')->get();
    }

    #[Computed]
    public function totalPerLocation()
    {
        // Only include aktual stock, not hold batches
        $query = StockBatch::where('qty', '>', 0)->where('status', 'aktual');

        // Filter berdasarkan tanggal ringkasan
        if ($this->summaryDateFrom) {
            $query->whereDate('created_at', '>=', $this->summaryDateFrom);
        }

        if ($this->summaryDateTo) {
            $query->whereDate('created_at', '<=', $this->summaryDateTo);
        }

        $batches = $query->with(['product'])->get();

        // Preload stores and warehouses for efficiency
        $storeIds = $batches->where('location_type', 'store')->pluck('location_id')->unique();
        $warehouseIds = $batches->where('location_type', 'warehouse')->pluck('location_id')->unique();

        $stores = Store::whereIn('id', $storeIds)->pluck('nama_toko', 'id');
        $warehouses = Warehouse::whereIn('id', $warehouseIds)->pluck('nama_gudang', 'id');

        $locations = [];

        foreach ($batches as $batch) {
            $locationType = $batch->location_type;
            $locationId = $batch->location_id;

            // Get location name based on location type
            $locationName = 'Unknown';
            if ($locationType === 'store') {
                $locationName = $stores[$locationId] ?? 'Unknown Store';
            } else {
                $locationName = $warehouses[$locationId] ?? 'Unknown Warehouse';
            }

            $key = $locationType . '_' . $locationId;

            if (!isset($locations[$key])) {
                $locations[$key] = [
                    'type' => $locationType,
                    'type_label' => $locationType === 'store' ? 'Toko' : 'Gudang',
                    'name' => $locationName,
                    'total_products' => 0,
                    'total_batches' => 0,
                    'total_qty' => 0,
                    'products' => [],
                ];
            }

            $productId = $batch->product_id;
            if (!in_array($productId, $locations[$key]['products'])) {
                $locations[$key]['products'][] = $productId;
                $locations[$key]['total_products']++;
            }

            $locations[$key]['total_batches']++;
            $locations[$key]['total_qty'] += $batch->qty;
        }

        // Sort by location name
        usort($locations, fn($a, $b) => strcmp($a['name'], $b['name']));

        return collect($locations);
    }

    public function updatingProductPerPage()
    {
        $this->productPage = 1;
    }

    public function render()
    {
        // Urutkan berdasarkan nama produk (A-Z), lalu created_at terbaru
        // Ini memastikan produk terurut alfabetis dan tumpukan terbaru di atas dalam setiap produk
        // EXCLUDE hold batches - hanya tampilkan stok aktual yang tersedia
        $query = StockBatch::where('qty', '>', 0)
            ->where('status', 'aktual') // Only show actual stock, not hold
            ->with(['product', 'product.category', 'product.subcategory'])
            ->join('products', 'stock_batches.product_id', '=', 'products.id')
            ->orderBy('products.nama_produk', 'asc')
            ->orderBy('stock_batches.created_at', 'desc')
            ->select('stock_batches.*');

        // Filter berdasarkan search (nama produk)
        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('nama_produk', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_produk', 'like', '%' . $this->search . '%');
            });
        }

        // Filter berdasarkan lokasi
        if ($this->location) {
            $query->byLocation($this->location);
        }

        // Filter berdasarkan satuan
        if ($this->satuan) {
            $query->whereHas('product', function ($q) {
                $q->where('satuan', $this->satuan);
            });
        }

        // Filter berdasarkan tanggal
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $batches = $query->paginate($this->per_page);

        // Jika halaman saat ini lebih besar dari total halaman, reset ke halaman 1
        if ($batches->currentPage() > $batches->lastPage() && $batches->lastPage() > 0) {
            $this->resetPage();
            $batches = $query->paginate($this->per_page);
        }

        // Buat query terpisah untuk mendapatkan urutan produk yang konsisten di semua halaman
        // Ambil product_id unik dengan ordering yang sama
        // Only include aktual stock, not hold batches
        $productNumberQuery = StockBatch::where('qty', '>', 0)
            ->where('status', 'aktual')
            ->join('products', 'stock_batches.product_id', '=', 'products.id')
            ->select('stock_batches.product_id', 'products.nama_produk')
            ->groupBy('stock_batches.product_id', 'products.nama_produk')
            ->orderBy('products.nama_produk', 'asc');

        // Terapkan filter yang sama seperti query utama
        if ($this->search) {
            $productNumberQuery->where(function ($q) {
                $q->where('products.nama_produk', 'like', '%' . $this->search . '%')
                    ->orWhere('products.kode_produk', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->location) {
            if (method_exists(StockBatch::class, 'byLocation')) {
                $productNumberQuery->where(function ($q) {
                    (new StockBatch)->scopeByLocation($q, $this->location);
                });
            }
        }

        if ($this->satuan) {
            $productNumberQuery->where('products.satuan', $this->satuan);
        }

        if ($this->dateFrom) {
            $productNumberQuery->whereDate('stock_batches.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $productNumberQuery->whereDate('stock_batches.created_at', '<=', $this->dateTo);
        }

        // Ambil semua product_id yang sudah difilter dan diurutkan
        $orderedProductIds = $productNumberQuery->pluck('stock_batches.product_id')->toArray();

        // Buat mapping nomor urut untuk setiap product_id
        $productNumbers = [];
        foreach ($orderedProductIds as $index => $productId) {
            $productNumbers[$productId] = $index + 1;
        }

        $products = Product::select('id', 'kode_produk', 'nama_produk', 'satuan', 'category_id', 'subcategory_id')
            ->orderBy('nama_produk')
            ->get();

        // Prepare products JSON for JavaScript (safe mapping)
        $productsJson = $products->map(function ($product) {
            return [
                'id' => (int)$product->id,
                'kode_produk' => $product->kode_produk ?? '',
                'nama_produk' => $product->nama_produk ?? '',
                'satuan' => $product->satuan ?? '',
                'category_id' => $product->category_id ? (int)$product->category_id : null,
                'subcategory_id' => $product->subcategory_id ? (int)$product->subcategory_id : null,
            ];
        });

        $locations = ['store' => 'Toko', 'warehouse' => 'Gudang'];

        // Get all units from Unit model
        $units = Unit::all();

        // Get categories and subcategories
        $categories = Category::all();
        $subcategories = Subcategory::all();

        // Get stores and warehouses
        $stores = Store::orderBy('nama_toko')->get();
        $warehouses = Warehouse::orderBy('nama_gudang')->get();

        return view('livewire.admin.stock-batch-index', [
            'batches' => $batches,
            'productNumbers' => $productNumbers,
            'products' => $products,
            'productsJson' => $productsJson,
            'locations' => $locations,
            'units' => $units,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'stores' => $stores,
            'warehouses' => $warehouses,
            'totalPerProduct' => $this->totalPerProduct,
            'holdBatchOptions' => $this->holdBatchOptions,
        ]);
    }

    private function refreshData()
    {
        // Clear computed property cache
        $this->resetPage();
        // Force component re-render
        $this->dispatch('$refresh');
    }

    #[On('delete-batch')]
    public function deleteBatch($batchId)
    {
        try {
            $batch = StockBatch::findOrFail($batchId);
            $batch->delete();

            $this->dispatch('notify', message: 'Batch berhasil dihapus!', type: 'success');
            $this->dispatch('batch-created'); // Trigger table reload
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    #[On('edit-batch')]
    public function editBatch($id)
    {
        $batchId = is_array($id) ? ($id['id'] ?? $id[0] ?? $id) : $id;
        $batch = StockBatch::findOrFail($batchId);

        $this->editBatchId = $batch->id;
        $this->editProductId = $batch->product_id;
        $this->editLocationType = $batch->location_type;
        $this->editLocationId = $batch->location_id;
        $this->editNamaTumpukan = $batch->nama_tumpukan;
        $this->editQty = $batch->qty;
        $this->editNote = $batch->note ?? '';
        $this->editSatuan = $batch->product->satuan ?? '';

        $this->showEditModal = true;
    }

    public function updateBatch()
    {
        $this->validate([
            'editNamaTumpukan' => 'required|string|max:255',
            'editQty' => 'required|numeric|min:0',
            'editSatuan' => 'required|string|max:20',
        ], [
            'editNamaTumpukan.required' => 'Nama tumpukan tidak boleh kosong',
            'editQty.required' => 'Kuantitas harus diisi',
            'editQty.min' => 'Kuantitas minimal 0',
            'editSatuan.required' => 'Satuan harus diisi',
        ]);

        try {
            $batch = StockBatch::findOrFail($this->editBatchId);

            // Update batch
            $batch->update([
                'nama_tumpukan' => $this->editNamaTumpukan,
                'qty' => $this->editQty,
                'note' => $this->editNote,
            ]);

            // Update satuan di product
            if ($batch->product) {
                $batch->product->update([
                    'satuan' => $this->editSatuan,
                ]);
            }

            session()->flash('message', 'Batch berhasil diperbarui!');
            $this->showEditModal = false;
            $this->resetEditForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetEditForm();
    }

    public function resetMainFilters(): void
    {
        // Reset filter utama dan kembalikan pagination ke halaman pertama
        $this->search = '';
        $this->location = '';
        $this->per_page = 10;
        $this->resetPage();
    }

    public function clearSearchFilter(): void
    {
        // Hapus hanya pencarian produk dan kembali ke halaman pertama
        $this->search = '';
        $this->resetPage();
    }

    private function resetEditForm()
    {
        $this->editBatchId = null;
        $this->editProductId = null;
        $this->editLocationType = 'store';
        $this->editLocationId = null;
        $this->editNamaTumpukan = '';
        $this->editQty = 0;
        $this->editNote = '';
        $this->editSatuan = '';
        $this->clearValidation();
    }

    public function openCreateForm()
    {
        $this->showCreateForm = true;
        $this->resetCreateForm();
    }

    public function closeCreateForm()
    {
        $this->showCreateForm = false;
        $this->resetCreateForm();
    }

    public function createStockBatch()
    {
        // Prevent double submission
        if ($this->isCreatingBatch) {
            return;
        }
        $this->isCreatingBatch = true;

        try {
            Log::info('Creating stock batch with data', [
                'createQty' => $this->createQty,
                'createProductId' => $this->createProductId,
                'createNamaTumpukan' => $this->createNamaTumpukan,
            ]);

            $this->validate([
                'createCategoryId' => 'required|numeric|exists:categories,id',
                'createSubcategoryId' => 'required|numeric|exists:subcategories,id',
                'createProductId' => 'required|numeric|min:1|exists:products,id',
                'createLocationType' => 'required|in:store,warehouse',
                'createLocationId' => 'required|numeric|min:1',
                'createNamaTumpukan' => 'required|string|max:255',
                'createQty' => 'required|numeric|min:0.01',
                'createSatuan' => 'nullable|string|max:50',
                'createDate' => 'nullable|date',
            ], [
                'createCategoryId.required' => 'Kategori harus dipilih',
                'createCategoryId.exists' => 'Kategori tidak ditemukan',
                'createSubcategoryId.required' => 'Subkategori harus dipilih',
                'createSubcategoryId.exists' => 'Subkategori tidak ditemukan',
                'createProductId.required' => 'Produk harus dipilih',
                'createProductId.exists' => 'Produk tidak ditemukan',
                'createLocationType.required' => 'Lokasi harus dipilih',
                'createLocationType.in' => 'Lokasi tidak valid',
                'createLocationId.required' => 'Toko/Gudang harus dipilih',
                'createLocationId.numeric' => 'Toko/Gudang tidak valid',
                'createNamaTumpukan.required' => 'Nama tumpukan tidak boleh kosong',
                'createNamaTumpukan.max' => 'Nama tumpukan maksimal 255 karakter',
                'createQty.required' => 'Kuantitas harus diisi',
                'createQty.min' => 'Kuantitas minimal 0.01',
                'createSatuan.max' => 'Satuan maksimal 50 karakter',
                'createDate.date' => 'Tanggal harus format yang valid',
            ]);

            // Update satuan product jika diisi
            if ($this->createSatuan) {
                $product = Product::find($this->createProductId);
                if ($product && $product->satuan !== $this->createSatuan) {
                    $product->update(['satuan' => $this->createSatuan]);
                }
            }

            // Buat batch baru dengan locationId menggunakan service
            // Parse createDate: jika kosong gunakan now(), jika ada parse menjadi Carbon
            $batchDate = null;
            if (!empty($this->createDate)) {
                try {
                    $batchDate = \Carbon\Carbon::parse($this->createDate);
                } catch (\Exception $e) {
                    $batchDate = null;
                }
            }

            $batch = app(StockBatchService::class)->addStock(
                $this->createProductId,
                $this->createLocationType,
                $this->createNamaTumpukan,
                $this->createQty,
                $this->createLocationId,
                $this->createNote ?: null,
                $batchDate
            );

            Log::info('Stock batch created successfully', [
                'batch_id' => $batch->id,
                'product_id' => $this->createProductId,
                'qty' => $this->createQty,
            ]);

            session()->flash('message', 'Stok tumpukan berhasil dibuat!');

            // Reset form fields only, keep form open
            $this->resetCreateForm();

            // Reset page to first page to show new data
            $this->resetPage();

            // Dispatch event ke frontend untuk clear form inputs
            $this->dispatch('batch-created');

            // Dispatch browser event untuk reload DataTable Total Stok Per Produk di frontend
            $this->dispatch('reloadTotalStokTable');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        } finally {
            // Always reset flag
            $this->isCreatingBatch = false;
        }
    }

    public function createHoldBatch()
    {
        // Prevent double submission
        if ($this->isCreatingHoldBatch) {
            return;
        }
        $this->isCreatingHoldBatch = true;

        try {
            $this->validate([
                'holdCategoryId' => 'required|numeric|exists:categories,id',
                'holdSubcategoryId' => 'required|numeric|exists:subcategories,id',
                'holdProductId' => 'required|numeric|min:1|exists:products,id',
                'holdLocationType' => 'required|in:store,warehouse',
                'holdLocationId' => 'required|numeric|min:1',
                'holdNamaTumpukan' => 'required|string|max:255',
                'holdQty' => 'required|numeric|min:0.01',
                'holdSatuan' => 'nullable|string|max:50',
                'holdReason' => 'required|string|max:255',
            ], [
                'holdCategoryId.required' => 'Kategori harus dipilih',
                'holdCategoryId.exists' => 'Kategori tidak ditemukan',
                'holdSubcategoryId.required' => 'Subkategori harus dipilih',
                'holdSubcategoryId.exists' => 'Subkategori tidak ditemukan',
                'holdProductId.required' => 'Produk harus dipilih',
                'holdProductId.exists' => 'Produk tidak ditemukan',
                'holdLocationType.required' => 'Lokasi harus dipilih',
                'holdLocationType.in' => 'Lokasi tidak valid',
                'holdLocationId.required' => 'Toko/Gudang harus dipilih',
                'holdLocationId.numeric' => 'Toko/Gudang tidak valid',
                'holdNamaTumpukan.required' => 'Nama tumpukan tidak boleh kosong',
                'holdNamaTumpukan.max' => 'Nama tumpukan maksimal 255 karakter',
                'holdQty.required' => 'Kuantitas harus diisi',
                'holdQty.min' => 'Kuantitas minimal 0.01',
                'holdSatuan.max' => 'Satuan maksimal 50 karakter',
                'holdReason.required' => 'Alasan hold harus diisi',
                'holdReason.max' => 'Alasan hold maksimal 255 karakter',
            ]);

            // Update satuan product jika diisi
            if ($this->holdSatuan) {
                $product = Product::find($this->holdProductId);
                if ($product && $product->satuan !== $this->holdSatuan) {
                    $product->update(['satuan' => $this->holdSatuan]);
                }
            }

            // Check if identical batch already exists (prevent duplicates)
            $existingHold = StockBatch::where('product_id', $this->holdProductId)
                ->where('nama_tumpukan', $this->holdNamaTumpukan)
                ->where('location_type', $this->holdLocationType)
                ->where('location_id', $this->holdLocationId)
                ->where('status', 'hold')
                ->where('qty', $this->holdQty)
                ->first();

            if ($existingHold) {
                session()->flash('error', 'Hold batch yang sama sudah ada! Tidak perlu membuat duplikat.');
                $this->isCreatingHoldBatch = false;
                return;
            }

            // Buat batch hold baru dengan transaction
            $batch = \DB::transaction(function () {
                return StockBatch::create([
                    'product_id' => $this->holdProductId,
                    'location_type' => $this->holdLocationType,
                    'location_id' => $this->holdLocationId,
                    'nama_tumpukan' => $this->holdNamaTumpukan,
                    'qty' => $this->holdQty,
                    'status' => 'hold',
                    'note' => $this->holdNote ?: "Hold: {$this->holdReason}",
                ]);
            });

            // Catat di StockCard untuk history
            StockCard::create([
                'product_id' => $this->holdProductId,
                'batch_id' => $batch->id,
                'type' => 'hold',
                'qty' => $this->holdQty,
                'from_location' => null,
                'to_location' => $this->holdLocationType === 'store' ? "Toko #{$this->holdLocationId}" : "Gudang #{$this->holdLocationId}",
                'reference_type' => 'manual_hold',
                'reference_id' => $batch->id,
                'note' => "Tumpukan hold dibuat: {$this->holdReason}",
            ]);

            Log::info('Hold stock batch created successfully', [
                'batch_id' => $batch->id,
                'product_id' => $this->holdProductId,
                'qty' => $this->holdQty,
                'reason' => $this->holdReason,
            ]);

            session()->flash('message', 'Tumpukan hold berhasil dibuat!');

            // Reset form
            $this->resetHoldForm();
            $this->showCreateHoldForm = false;

            // Reset page to first page to show new data
            $this->resetPage();

            // Dispatch event ke frontend
            $this->dispatch('hold-batch-created');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        } finally {
            // Always reset flag
            $this->isCreatingHoldBatch = false;
        }
    }

    private function resetCreateForm()
    {
        // Hanya reset field yang perlu di-reset untuk input batch berikutnya
        // Pertahankan: tanggal, kategori, subkategori, produk, satuan, lokasi
        $this->reset([
            'createNamaTumpukan',
            'createQty',
            'createNote',
            // Inline create helpers
            'newCreateCategoryName',
            'newCreateSubcategoryName',
            'newCreateCategoryCode',
            'newCreateSubcategoryCode',
            'showCreateCategoryInline',
            'showCreateSubcategoryInline',
        ]);

        // Reset validasi
        $this->clearValidation();

        // Re-initialize qty ke 0
        $this->createQty = 0;
    }

    // Inline add category for create batch form
    public function createInlineCategory()
    {
        $this->validate([
            'newCreateCategoryName' => 'required|string|max:255',
            'newCreateCategoryCode' => 'nullable|string|max:50|unique:categories,kode_kategori',
        ], [
            'newCreateCategoryCode.unique' => 'Kode kategori sudah digunakan',
        ]);

        $kode = $this->newCreateCategoryCode ?: strtoupper(Str::slug($this->newCreateCategoryName, '_'));

        $cat = Category::create([
            'nama_kategori' => $this->newCreateCategoryName,
            'kode_kategori' => $kode,
        ]);

        $this->createCategoryId = $cat->id;
        $this->newCreateCategoryName = '';
        $this->newCreateCategoryCode = '';
        $this->showCreateCategoryInline = false;

        session()->flash('message', "Kategori '{$cat->nama_kategori}' berhasil dibuat.");
    }

    // Inline add subcategory for create batch form
    public function createInlineSubcategory()
    {
        $this->validate([
            'newCreateSubcategoryName' => 'required|string|max:255',
            'newCreateSubcategoryCode' => 'nullable|string|max:50|unique:subcategories,kode_subkategori',
            'createCategoryId' => 'required|exists:categories,id',
        ], [
            'createCategoryId.required' => 'Pilih kategori terlebih dahulu.',
            'newCreateSubcategoryCode.unique' => 'Kode subkategori sudah digunakan',
        ]);

        $kode = $this->newCreateSubcategoryCode ?: strtoupper(Str::slug($this->newCreateSubcategoryName, '_'));

        $sub = Subcategory::create([
            'nama_subkategori' => $this->newCreateSubcategoryName,
            'kode_subkategori' => $kode,
            'category_id' => $this->createCategoryId,
        ]);

        $this->createSubcategoryId = $sub->id;
        $this->newCreateSubcategoryName = '';
        $this->newCreateSubcategoryCode = '';
        $this->showCreateSubcategoryInline = false;

        session()->flash('message', "Subkategori '{$sub->nama_subkategori}' berhasil dibuat.");
    }

    // Hold batch methods
    public function openCreateHoldForm()
    {
        $this->showCreateHoldForm = true;
        $this->showCreateForm = false; // Close regular create form if open
    }

    public function closeCreateHoldForm()
    {
        $this->showCreateHoldForm = false;
        $this->resetHoldForm();
    }

    public function updatedHoldProductId()
    {
        // Reset nama tumpukan when product changes
        $this->holdNamaTumpukan = '';
    }

    public function selectHoldProduct($value)
    {
        $product = Product::find($value);
        if ($product) {
            $this->holdProductId = $product->id;
            $this->holdProductSearch = "[{$product->kode_produk}] {$product->nama_produk}";
            $this->holdCategoryId = $product->category_id;
            $this->holdSubcategoryId = $product->subcategory_id;
            $this->holdSatuan = $product->satuan ?? '';

            // Reset nama tumpukan - user will select from dropdown
            $this->holdNamaTumpukan = '';

            // Get the latest existing batch for this product to pre-fill other fields
            $existingBatch = StockBatch::where('product_id', $product->id)
                ->where('status', 'aktual')
                ->where('qty', '>', 0)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($existingBatch) {
                // Pre-fill location and qty from existing batch
                $this->holdQty = $existingBatch->qty;
                $this->holdLocationType = $existingBatch->location_type;
                $this->holdLocationId = $existingBatch->location_id;
            } else {
                // Reset if no existing batch
                $this->holdQty = 0;
            }
        }
    }

    private function resetHoldForm()
    {
        $this->reset([
            'holdProductId',
            'holdProductSearch',
            'holdCategoryId',
            'holdSubcategoryId',
            'holdLocationType',
            'holdLocationId',
            'holdNamaTumpukan',
            'holdQty',
            'holdNote',
            'holdSatuan',
            'holdReason',
        ]);

        // Reset validasi
        $this->clearValidation();

        // Re-initialize qty ke 0
        $this->holdQty = 0;
    }

    // Selection methods
    public function toggleSelectBatch($batchId)
    {
        if (in_array($batchId, $this->selectedBatches)) {
            $this->selectedBatches = array_filter($this->selectedBatches, fn($id) => $id != $batchId);
        } else {
            $this->selectedBatches[] = $batchId;
        }

        $this->updateSelectAllState();
    }

    public function updatedSelectAll()
    {
        // Livewire hook ketika selectAll berubah
        if ($this->selectAll) {
            // Select semua batch di halaman saat ini
            $this->selectedBatches = $this->getCurrentPageBatchIds();
        } else {
            // Clear semua
            $this->selectedBatches = [];
        }
    }

    private function getCurrentPageBatchIds()
    {
        // Get batches yang ada di halaman saat ini dari render method
        $query = StockBatch::active()->latestFirst();

        // Filter berdasarkan search (nama produk)
        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('nama_produk', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_produk', 'like', '%' . $this->search . '%');
            });
        }

        // Filter berdasarkan lokasi
        if ($this->location) {
            $query->byLocation($this->location);
        }

        // Filter berdasarkan satuan
        if ($this->satuan) {
            $query->whereHas('product', function ($q) {
                $q->where('satuan', $this->satuan);
            });
        }

        return $query->paginate($this->per_page)->pluck('id')->toArray();
    }

    public function updatedSelectedBatches()
    {
        // Update state ketika user mengubah checkbox individual
        $this->updateSelectAllState();
    }

    public function updateSelectAllState()
    {
        $totalBatches = $this->batches->count();
        $selectedCount = count($this->selectedBatches);

        $this->selectAll = $totalBatches > 0 && $selectedCount === $totalBatches;
    }

    public function deleteSelected()
    {
        if (empty($this->selectedBatches)) {
            session()->flash('message', 'Pilih minimal satu batch untuk dihapus');
            return;
        }

        try {
            $batchIds = $this->selectedBatches;

            // Delete batches
            $count = StockBatch::whereIn('id', $batchIds)->delete();

            if ($count > 0) {
                // Clear selection
                $this->selectedBatches = [];
                $this->selectAll = false;
                $this->resetPage();

                session()->flash('message', "$count batch berhasil dihapus!");
            } else {
                session()->flash('message', 'Tidak ada batch yang dihapus');
            }
        } catch (\Exception $e) {
            Log::error('Error deleting batches: ' . $e->getMessage());
            session()->flash('error', 'Gagal menghapus batch: ' . $e->getMessage());
        }
    }
    public function clearSelection()
    {
        $this->selectedBatches = [];
        $this->selectAll = false;
    }

    #[Computed]
    public function batches()
    {
        $query = StockBatch::with('product')
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('nama_produk', 'like', '%' . $this->search . '%')
                        ->orWhere('kode_produk', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->location, function ($query) {
                [$type, $id] = explode('_', $this->location);
                $query->where('location_type', $type)->where('location_id', $id);
            });

        return $query->paginate($this->per_page);
    }

    public function openQuickAddProductModal()
    {
        $this->showQuickAddProductModal = true;
        $this->resetQuickAddProductForm();
    }

    public function closeQuickAddProductModal()
    {
        $this->showQuickAddProductModal = false;
        $this->resetQuickAddProductForm();
    }

    private function resetQuickAddProductForm()
    {
        $this->quickProductName = '';
        $this->quickProductCode = '';
        $this->quickProductCategoryId = null;
        $this->quickProductSubcategoryId = null;
        $this->quickProductUnit = '';
        $this->resetValidation(['quickProductName', 'quickProductCode', 'quickProductCategoryId', 'quickProductSubcategoryId', 'quickProductUnit']);
    }

    public function quickAddProduct()
    {
        $this->validate([
            'quickProductName' => 'required|string|max:255',
            'quickProductCode' => 'required|string|max:100|unique:products,kode_produk',
            'quickProductCategoryId' => 'required|exists:categories,id',
            'quickProductSubcategoryId' => 'required|exists:subcategories,id',
            'quickProductUnit' => 'required|string|max:50',
        ], [
            'quickProductName.required' => 'Nama produk harus diisi',
            'quickProductCode.required' => 'Kode produk harus diisi',
            'quickProductCode.unique' => 'Kode produk sudah digunakan',
            'quickProductCategoryId.required' => 'Kategori harus dipilih',
            'quickProductSubcategoryId.required' => 'Subkategori harus dipilih',
            'quickProductUnit.required' => 'Satuan harus diisi',
        ]);

        try {
            $product = Product::create([
                'nama_produk' => $this->quickProductName,
                'kode_produk' => $this->quickProductCode,
                'category_id' => $this->quickProductCategoryId,
                'subcategory_id' => $this->quickProductSubcategoryId,
                'satuan' => $this->quickProductUnit,
            ]);

            // Auto-select the newly created product
            $this->createProductId = $product->id;

            // Set product search value for display
            $this->createProductSearch = "[{$product->kode_produk}] {$product->nama_produk}";

            $this->closeQuickAddProductModal();
            session()->flash('message', 'Produk baru berhasil ditambahkan!');

            // Dispatch event to update product list in Alpine with full product data
            $this->dispatch('productAdded', [
                'id' => $product->id,
                'kode_produk' => $product->kode_produk,
                'nama_produk' => $product->nama_produk,
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding quick product: ' . $e->getMessage());
            session()->flash('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    #[Computed]
    public function quickAddSubcategories()
    {
        if (!$this->quickProductCategoryId) {
            return collect([]);
        }
        return Subcategory::where('category_id', $this->quickProductCategoryId)->get();
    }

    #[Computed]
    public function productsForJson()
    {
        return Product::select('id', 'kode_produk', 'nama_produk', 'satuan', 'category_id', 'subcategory_id')
            ->orderBy('nama_produk')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => (int)$product->id,
                    'kode_produk' => e($product->kode_produk ?? ''),
                    'nama_produk' => e($product->nama_produk ?? ''),
                    'satuan' => e($product->satuan ?? ''),
                    'category_id' => $product->category_id ? (int)$product->category_id : null,
                    'subcategory_id' => $product->subcategory_id ? (int)$product->subcategory_id : null,
                ];
            });
    }
}
