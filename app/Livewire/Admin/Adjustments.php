<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Store;
use App\Models\Warehouse;
use App\Models\Unit;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Adjustments extends Component
{
    public $adjustment_items = [];
    public $adjustment_location; // 'store' or 'warehouse'
    public $adjustment_store_id;
    public $adjustment_warehouse_id;
    public $adjustment_type = 'add'; // 'add' or 'remove'
    public $adjustment_reason;
    public $adjustment_date;

    public $products = [];
    public $units = [];
    public $stores = [];
    public $warehouses = [];
    public $categories = [];
    public $subcategories = [];

    // Modal properties
    public $showModalCreateCategory = false;
    public $showModalCreateSubcategory = false;
    public $showModalCreateProduct = false;
    public $showModalCreateUnit = false;

    // Form fields
    public $newCategoryName = '';
    public $newCategoryCode = '';
    public $newSubcategoryName = '';
    public $newSubcategoryCategory = '';
    public $newProductCode = '';
    public $newProductName = '';
    public $newProductSubcategory = '';
    public $newUnitName = '';

    protected $rules = [
        'adjustment_location' => 'required|in:store,warehouse',
        'adjustment_store_id' => 'nullable|exists:stores,id',
        'adjustment_warehouse_id' => 'nullable|exists:warehouses,id',
        'adjustment_type' => 'required|in:add,remove',
        'adjustment_reason' => 'required|string|min:3',
        'adjustment_date' => 'required|date',
        'adjustment_items' => 'required|array|min:1',
        'adjustment_items.*.category_id' => 'required|exists:categories,id',
        'adjustment_items.*.subcategory_id' => 'nullable|exists:subcategories,id',
        'adjustment_items.*.product_id' => 'required|exists:products,id',
        'adjustment_items.*.stok_awal' => 'required|integer|min:0',
        'adjustment_items.*.quantity' => 'required|integer|min:1',
        'adjustment_items.*.unit_id' => 'required|exists:units,id',
    ];

    protected $rulesCreateCategory = [
        'newCategoryCode' => 'required|string|min:1',
        'newCategoryName' => 'required|string|min:3',
    ];

    protected $rulesCreateSubcategory = [
        'newSubcategoryName' => 'required|string|min:3',
        'newSubcategoryCategory' => 'required|exists:categories,id',
    ];

    protected $rulesCreateProduct = [
        'newProductCode' => 'nullable|string|min:1',
        'newProductName' => 'required|string|min:3',
        'newProductSubcategory' => 'required|exists:subcategories,id',
    ];

    protected $rulesCreateUnit = [
        'newUnitName' => 'required|string|min:1',
    ];
    public function mount()
    {
        $this->loadAllData();
        $this->adjustment_date = date('Y-m-d');
    }

    private function loadAllData()
    {
        $this->products = Product::orderByDesc('created_at')->get();
        $this->units = Unit::orderByDesc('created_at')->get();
        $this->stores = Store::orderBy('nama_toko')->get();
        $this->warehouses = Warehouse::orderBy('nama_gudang')->get();
        $this->categories = Category::with('subcategories')->orderByDesc('created_at')->get();
    }

    // Modal functions
    public function openModalCreateCategory()
    {
        $this->showModalCreateCategory = true;
    }

    public function closeModalCreateCategory()
    {
        $this->showModalCreateCategory = false;
        $this->newCategoryName = '';
        $this->newCategoryCode = '';
    }

    public function saveNewCategory()
    {
        $this->validate($this->rulesCreateCategory);

        try {
            Category::create([
                'nama_kategori' => $this->newCategoryName,
                'kode_kategori' => $this->newCategoryCode,
            ]);

            session()->flash('message', 'Kategori berhasil ditambahkan!');
            $this->closeModalCreateCategory();
            $this->loadAllData();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }

    public function openModalCreateSubcategory()
    {
        $this->showModalCreateSubcategory = true;
    }

    public function closeModalCreateSubcategory()
    {
        $this->showModalCreateSubcategory = false;
        $this->newSubcategoryName = '';
        $this->newSubcategoryCategory = '';
    }

    public function saveNewSubcategory()
    {
        $this->validate($this->rulesCreateSubcategory);

        try {
            Subcategory::create([
                'nama_subkategori' => $this->newSubcategoryName,
                'category_id' => $this->newSubcategoryCategory,
            ]);

            session()->flash('message', 'Subkategori berhasil ditambahkan!');
            $this->closeModalCreateSubcategory();
            $this->loadAllData();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menambahkan subkategori: ' . $e->getMessage());
        }
    }

    public function openModalCreateProduct()
    {
        $this->showModalCreateProduct = true;
        $this->newProductCode = '';
        $this->newProductName = '';
        $this->newProductSubcategory = '';
    }

    public function updatedNewProductName($value)
    {
        if ($value && $this->newProductSubcategory) {
            $subcategory = Subcategory::find($this->newProductSubcategory);
            if ($subcategory && $subcategory->category) {
                $prefix = strtoupper(substr($subcategory->category->nama_kategori, 0, 3));
                $count = Product::where('category_id', $subcategory->category_id)->count();
                $namaShort = strtoupper(str_replace([' ', '-'], '', $value));
                $this->newProductCode = $prefix . '_' . str_pad($count + 1, 3, '0', STR_PAD_LEFT) . '_' . substr($namaShort, 0, 10);
            }
        }
    }

    public function updatedNewProductSubcategory($value)
    {
        // Re-generate kode produk ketika subkategori berubah
        if ($value && $this->newProductName) {
            $this->updatedNewProductName($this->newProductName);
        }
    }

    public function closeModalCreateProduct()
    {
        $this->showModalCreateProduct = false;
        $this->newProductCode = '';
        $this->newProductName = '';
        $this->newProductSubcategory = '';
    }

    public function saveNewProduct()
    {
        $this->validate($this->rulesCreateProduct);

        try {
            Product::create([
                'kode_produk' => $this->newProductCode,
                'nama_produk' => $this->newProductName,
                'subcategory_id' => $this->newProductSubcategory,
                'category_id' => Subcategory::find($this->newProductSubcategory)?->category_id,
                'harga' => 0,
                'stok' => 0,
            ]);

            session()->flash('message', 'Produk berhasil ditambahkan!');
            $this->closeModalCreateProduct();
            $this->loadAllData();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    public function openModalCreateUnit()
    {
        $this->showModalCreateUnit = true;
    }

    public function closeModalCreateUnit()
    {
        $this->showModalCreateUnit = false;
        $this->newUnitName = '';
    }

    public function saveNewUnit()
    {
        $this->validate($this->rulesCreateUnit);

        try {
            Unit::create([
                'nama_unit' => $this->newUnitName,
            ]);

            session()->flash('message', 'Unit berhasil ditambahkan!');
            $this->closeModalCreateUnit();
            $this->loadAllData();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menambahkan unit: ' . $e->getMessage());
        }
    }

    public function addItem()
    {
        $this->adjustment_items[] = [
            'category_id' => null,
            'subcategory_id' => null,
            'product_id' => null,
            'stok_awal' => null,
            'quantity' => null,  // Untuk penambahan: stok_masuk, Untuk pengurangan: stok_keluar
            'unit_id' => null,
            'category_name' => '-',
            'subcategory_name' => '-',
            'product_name' => '-',
            'unit_name' => '-',
            'stok_akhir' => 0,
        ];
    }
    public function removeItem($index)
    {
        unset($this->adjustment_items[$index]);
        $this->adjustment_items = array_values($this->adjustment_items);
    }

    // Handle dropdown selections with __add_new__ check
    public function handleCategorySelect($index, $value)
    {
        if ($value === '__add_new__') {
            // Reset selection and open modal
            $this->adjustment_items[$index]['category_id'] = null;
            $this->openModalCreateCategory();
        } else {
            $this->updateCategoryFilter($index);
        }
    }

    public function handleSubcategorySelect($index, $value)
    {
        if ($value === '__add_new__') {
            // Reset selection and open modal
            $this->adjustment_items[$index]['subcategory_id'] = null;
            $this->openModalCreateSubcategory();
        } else {
            $this->updateSubcategoryFilter($index);
        }
    }

    public function handleProductSelect($index, $value)
    {
        if ($value === '__add_new__') {
            // Reset selection and open modal
            $this->adjustment_items[$index]['product_id'] = null;
            $this->openModalCreateProduct();
        } else {
            $this->updateStokAwalForItem($index, $value);
        }
    }

    public function handleUnitSelect($index, $value)
    {
        if ($value === '__add_new__') {
            // Reset selection and open modal
            $this->adjustment_items[$index]['unit_id'] = null;
            $this->openModalCreateUnit();
        }
    }

    public function handleCategoryChange($index)
    {
        $categoryId = $this->adjustment_items[$index]['category_id'] ?? null;

        if ($categoryId === '__add_new__') {
            // Reset category selection
            $this->adjustment_items[$index]['category_id'] = null;
            // Open modal
            $this->openModalCreateCategory();
        } else if ($categoryId) {
            // Normal category selection
            $this->updateCategoryFilter($index);
        }
    }

    public function handleSubcategoryChange($index, $value)
    {
        if ($value === '__add_new__') {
            // Reset subcategory selection
            $this->adjustment_items[$index]['subcategory_id'] = null;
            // Open modal
            $this->openModalCreateSubcategory();
        } else if ($value) {
            $this->adjustment_items[$index]['subcategory_id'] = $value;
            // Normal subcategory selection
            $this->updateSubcategoryFilter($index);
        }
    }

    public function handleProductChange($index, $value)
    {
        if ($value === '__add_new__') {
            // Reset product selection
            $this->adjustment_items[$index]['product_id'] = null;
            // Open modal
            $this->openModalCreateProduct();
        } else if ($value) {
            $this->adjustment_items[$index]['product_id'] = $value;
            // Normal product selection
            $this->updateStokAwalForItem($index, $value);
        }
    }

    public function handleUnitChange($index, $value)
    {
        if ($value === '__add_new__') {
            // Reset unit selection
            $this->adjustment_items[$index]['unit_id'] = null;
            // Open modal
            $this->openModalCreateUnit();
        } else if ($value) {
            $this->adjustment_items[$index]['unit_id'] = $value;
        }
    }

    public function updateCategoryFilter($index)
    {
        if (isset($this->adjustment_items[$index])) {
            $this->adjustment_items[$index]['subcategory_id'] = null;
            $this->adjustment_items[$index]['product_id'] = null;
            $this->adjustment_items[$index]['subcategory_name'] = '-';
            $this->adjustment_items[$index]['product_name'] = '-';
        }
    }

    public function updateSubcategoryFilter($index)
    {
        if (isset($this->adjustment_items[$index])) {
            $this->adjustment_items[$index]['product_id'] = null;
            $this->adjustment_items[$index]['product_name'] = '-';
        }
    }

    public function updatedAdjustmentItems($value, $key)
    {
        if (str_contains($key, 'category_id')) {
            $index = explode('.', $key)[1];
            $categoryId = $this->adjustment_items[$index]['category_id'] ?? null;
            if ($categoryId) {
                $category = Category::find($categoryId);
                $this->adjustment_items[$index]['category_name'] = $category->nama_kategori ?? '-';
            }
        }

        if (str_contains($key, 'subcategory_id')) {
            $index = explode('.', $key)[1];
            $subcategoryId = $this->adjustment_items[$index]['subcategory_id'] ?? null;
            if ($subcategoryId) {
                $subcategory = Subcategory::find($subcategoryId);
                $this->adjustment_items[$index]['subcategory_name'] = $subcategory->nama_subkategori ?? '-';
            }
        }

        if (str_contains($key, 'product_id')) {
            $index = explode('.', $key)[1];
            $productId = $this->adjustment_items[$index]['product_id'] ?? null;
            if ($productId) {
                $product = Product::find($productId);
                $this->adjustment_items[$index]['product_name'] = $product->nama_produk ?? '-';

                // Update stok awal untuk item ini
                $this->updateStokAwalForItem($index, $productId);
            }
        }

        if (str_contains($key, 'unit_id')) {
            $index = explode('.', $key)[1];
            $unitId = $this->adjustment_items[$index]['unit_id'] ?? null;
            if ($unitId) {
                $unit = Unit::find($unitId);
                $this->adjustment_items[$index]['unit_name'] = $unit->nama_unit ?? '-';
            }
        }

        // Calculate total whenever quantity or stok_awal changes
        if (str_contains($key, 'quantity') || str_contains($key, 'stok_awal')) {
            $index = explode('.', $key)[1];
            $this->calculateTotal($index);
        }
    }

    public function updatedAdjustmentStoreId()
    {
        // Update stok_awal untuk semua items ketika store berubah
        $this->refreshStokAwalForAllItems();
    }

    public function updatedAdjustmentWarehouseId()
    {
        // Update stok_awal untuk semua items ketika warehouse berubah
        $this->refreshStokAwalForAllItems();
    }

    private function refreshStokAwalForAllItems()
    {
        \Log::info('refreshStokAwalForAllItems called');
        foreach ($this->adjustment_items as $index => $item) {
            if (!empty($item['product_id'])) {
                $this->updateStokAwalForItem($index, $item['product_id']);
            }
        }
    }

    public function updateStokAwalForItem($index, $productId)
    {
        $stokAwal = 0;

        if ($this->adjustment_location === 'store' && $this->adjustment_store_id) {
            $adjustment = StockAdjustment::where('product_id', $productId)
                ->where('store_id', $this->adjustment_store_id)
                ->latest('created_at')
                ->first();
            if ($adjustment) {
                if ($adjustment->adjustment_type === 'add') {
                    $stokAwal = (int)$adjustment->stok_awal + (int)$adjustment->quantity;
                } else {
                    $stokAwal = (int)$adjustment->stok_awal - (int)$adjustment->quantity;
                }
            }
        } elseif ($this->adjustment_location === 'warehouse' && $this->adjustment_warehouse_id) {
            $adjustment = StockAdjustment::where('product_id', $productId)
                ->where('warehouse_id', $this->adjustment_warehouse_id)
                ->latest('created_at')
                ->first();
            if ($adjustment) {
                if ($adjustment->adjustment_type === 'add') {
                    $stokAwal = (int)$adjustment->stok_awal + (int)$adjustment->quantity;
                } else {
                    $stokAwal = (int)$adjustment->stok_awal - (int)$adjustment->quantity;
                }
            }
        }

        $this->adjustment_items[$index]['stok_awal'] = $stokAwal;
    }
    public function calculateTotal($index)
    {
        if (isset($this->adjustment_items[$index])) {
            $stok_awal = (int)($this->adjustment_items[$index]['stok_awal'] ?? 0);
            $quantity = (int)($this->adjustment_items[$index]['quantity'] ?? 0);

            // Berbeda perhitungan berdasarkan tipe penyesuaian
            if ($this->adjustment_type === 'add') {
                // Penambahan: Stok Akhir = Stok Awal + Stok Masuk
                $this->adjustment_items[$index]['stok_akhir'] = $stok_awal + $quantity;
            } else {
                // Pengurangan: Stok Akhir = Stok Awal - Stok Keluar
                $this->adjustment_items[$index]['stok_akhir'] = $stok_awal - $quantity;
            }
        }
    }

    public function getTotalStokToko()
    {
        $total = 0;
        foreach ($this->adjustment_items as $item) {
            // Total stok menggunakan stok_akhir yang sudah dihitung
            $total += ($item['stok_akhir'] ?? 0);
        }
        return $total;
    }

    public function save()
    {
        $this->validate();

        // Validate location selection
        if ($this->adjustment_location === 'store' && !$this->adjustment_store_id) {
            $this->addError('adjustment_store_id', 'Pilih lokasi toko terlebih dahulu');
            return;
        }

        if ($this->adjustment_location === 'warehouse' && !$this->adjustment_warehouse_id) {
            $this->addError('adjustment_warehouse_id', 'Pilih gudang terlebih dahulu');
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($this->adjustment_items as $item) {
                StockAdjustment::create([
                    'product_id' => $item['product_id'],
                    'store_id' => $this->adjustment_location === 'store' ? $this->adjustment_store_id : null,
                    'warehouse_id' => $this->adjustment_location === 'warehouse' ? $this->adjustment_warehouse_id : null,
                    'adjustment_type' => $this->adjustment_type,
                    'quantity' => $item['quantity'],
                    'stok_awal' => $item['stok_awal'] ?? 0,
                    'stok_masuk' => $this->adjustment_type === 'add' ? $item['quantity'] : 0,
                    'unit_id' => $item['unit_id'] ?? null,
                    'reason' => $this->adjustment_reason,
                    'adjustment_date' => $this->adjustment_date,
                    'user_id' => Auth::id(),
                ]);
            }

            DB::commit();

            session()->flash('message', 'Penyesuaian stok berhasil disimpan!');
            $this->reset();
            $this->adjustment_date = date('Y-m-d');
            return redirect()->route('admin.stock-reports');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan penyesuaian stok: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('admin.stock-reports');
    }

    public function render()
    {
        $filteredSubcategories = [];
        if (!empty($this->adjustment_items)) {
            $categoryIds = array_filter(array_unique(array_column($this->adjustment_items, 'category_id')));
            if (!empty($categoryIds)) {
                $filteredSubcategories = Subcategory::whereIn('category_id', $categoryIds)->get();
            }
        }

        $filteredProducts = [];
        if (!empty($this->adjustment_items)) {
            $subcategoryIds = array_filter(array_unique(array_column($this->adjustment_items, 'subcategory_id')));
            if (!empty($subcategoryIds)) {
                $filteredProducts = Product::whereIn('subcategory_id', $subcategoryIds)->get();
            }
        }

        return view('livewire.admin.adjustments', [
            'filteredSubcategories' => $filteredSubcategories,
            'filteredProducts' => $filteredProducts,
            'totalStokToko' => $this->getTotalStokToko(),
        ]);
    }
}
