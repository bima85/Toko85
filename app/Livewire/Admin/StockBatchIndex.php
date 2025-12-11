<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\Warehouse;
use App\Models\StockBatch;
use App\Models\Subcategory;
use App\Models\Unit;
use App\Services\StockBatchService;
use Illuminate\Support\Facades\Log;
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

    // Inline create form properties
    public bool $showCreateForm = false;
    public bool $isCreatingBatch = false; // Flag untuk prevent double submission
    public ?int $createProductId = null;
    public string $createProductSearch = '';
    public ?int $createCategoryId = null;
    public ?int $createSubcategoryId = null;
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

    // Selection properties
    public array $selectedBatches = [];
    public bool $selectAll = false;

    public function mount() {}

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


    public function updatedCreateCategoryId()
    {
        // Reset subcategory and product when category changes
        $this->createSubcategoryId = null;
        $this->createProductId = null;
        $this->createProductSearch = '';
        $this->createSatuan = '';
    }

    public function updatedCreateSubcategoryId()
    {
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
        $query = StockBatch::active();

        // Filter berdasarkan lokasi
        if ($this->location) {
            $query->byLocation($this->location);
        }

        // Group by product_id dan hitung total qty
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

        return $totals;
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

    public function updatingProductPerPage()
    {
        $this->productPage = 1;
    }

    public function render()
    {
        $query = StockBatch::active()->latestFirst()->with('product');

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

        $batches = $query->paginate($this->per_page);
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
            'products' => $products,
            'productsJson' => $productsJson,
            'locations' => $locations,
            'units' => $units,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'stores' => $stores,
            'warehouses' => $warehouses,
            'totalPerProduct' => $this->totalPerProduct,
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
                'createProductId' => 'required|numeric|min:1|exists:products,id',
                'createLocationType' => 'required|in:store,warehouse',
                'createLocationId' => 'required|numeric|min:1',
                'createNamaTumpukan' => 'required|string|max:255',
                'createQty' => 'required|numeric|min:0.01',
                'createSatuan' => 'nullable|string|max:50',
                'createDate' => 'nullable|date',
            ], [
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

    private function resetCreateForm()
    {
        // Reset semua properties
        $this->reset([
            'createProductId',
            'createProductSearch',
            'createCategoryId',
            'createSubcategoryId',
            'createLocationType',
            'createLocationId',
            'createNamaTumpukan',
            'createQty',
            'createNote',
            'createSatuan',
            'createDate',
        ]);

        // Reset validasi
        $this->clearValidation();

        // Re-initialize default values
        $this->createLocationType = 'store';
        $this->createQty = 0;
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
