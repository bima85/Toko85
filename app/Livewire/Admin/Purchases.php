<?php

namespace App\Livewire\Admin;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Store;
use App\Models\StockAdjustment;
use App\Services\StockCardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Str;

#[Layout('layouts.admin')]
class Purchases extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $ownerFilter = null;
    public $suppliers;
    public $showOwnerModal = false;
    public $new_owner_name = '';
    // Supplier modal state and fields
    public $showSupplierModal = false;
    public $kode_supplier;
    public $nama_supplier;
    public $telepon;
    public $email;
    public $alamat;
    public $supplier_keterangan;
    public $no_invoice;
    public $tanggal_pembelian;
    public $supplier_id;
    public $store_id;
    public $warehouse_id;
    public $status = 'completed';
    public $keterangan;
    public $editingPurchaseId = null;
    public $showCreateForm = false;
    public $showModal = false;
    public $selectedPurchase = null;

    // Purchase items
    public $purchaseItems = [];

    // Modal states and fields for inline add
    public $showCategoryModal = false;
    public $new_category_name = '';
    public $category_modal_row = null;

    public $showSubcategoryModal = false;
    public $new_subcategory_name = '';
    public $subcategory_modal_row = null;
    public $subcategory_modal_category_id = null;

    public $showProductModal = false;
    public $new_product_name = '';
    public $product_modal_row = null;
    public $product_modal_category_id = null;
    public $product_modal_subcategory_id = null;

    protected $listeners = ['addCategory', 'addSubcategory', 'addProduct', 'openCategoryModal', 'openSubcategoryModal', 'openProductModal'];



    protected $rules = [
        'no_invoice' => 'required|string|max:50|unique:purchases,no_invoice',
        'tanggal_pembelian' => 'required|date',
        'supplier_id' => 'required|exists:suppliers,id',
        'store_id' => 'nullable|exists:stores,id',
        'warehouse_id' => 'nullable|exists:warehouses,id',
        'status' => 'required|in:pending,completed,cancelled',
        'keterangan' => 'nullable|string',
        'purchaseItems' => 'required|array|min:1',
        'purchaseItems.*.category_id' => 'required|exists:categories,id',
        'purchaseItems.*.subcategory_id' => 'nullable|exists:subcategories,id',
        'purchaseItems.*.product_id' => 'required|exists:products,id',
        'purchaseItems.*.qty' => 'required|integer|min:1',
        'purchaseItems.*.unit_id' => 'required|exists:units,id',
        'purchaseItems.*.harga_beli' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        abort_unless($user && method_exists($user, 'hasRole') && $user->hasRole('admin'), 403);

        $this->suppliers = collect();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedOwnerFilter($value)
    {
        // set the ownerFilter from the incoming $value and normalize it
        $this->ownerFilter = is_string($value) ? trim($value) : $value;

        // saat owner dipilih dalam form pembuatan, reset supplier_id agar user memilih ulang dari daftar yang terfilter
        $this->supplier_id = null;
        // update suppliers list (support multiple owner names separated by comma)
        // we update suppliers regardless of whether the form is shown so data is ready
        $query = Supplier::orderBy('nama_supplier');
        if (!empty($this->ownerFilter)) {
            // if ownerFilter contains commas, split into array and filter by partial match (orWhere like)
            if (str_contains($this->ownerFilter, ',')) {
                $owners = array_map('trim', explode(',', $this->ownerFilter));
                $query->where(function ($q2) use ($owners) {
                    foreach ($owners as $o) {
                        if ($o !== '') {
                            $q2->orWhere('owner', 'like', '%' . $o . '%');
                        }
                    }
                });
            } else {
                // use partial match as well so exact formatting doesn't block matches
                $query->where('owner', 'like', '%' . $this->ownerFilter . '%');
            }
        }
        $this->suppliers = $query->get();



        // if form is shown and only one supplier matches, auto-select it
        if ($this->showCreateForm) {
            if ($this->suppliers instanceof \Illuminate\Support\Collection && $this->suppliers->count() === 1) {
                $this->supplier_id = $this->suppliers->first()->id;
                // generate invoice when supplier auto-selected here
                $this->generateInvoiceNumber($this->supplier_id);
            } else {
                $this->supplier_id = null;
            }
        }
        // force re-render
        $this->dispatch('$refresh');
    }

    public function openOwnerModal()
    {
        $this->showOwnerModal = true;
        $this->new_owner_name = '';
    }

    public function ownerChanged($value)
    {
        // helper invoked from frontend change event to ensure update occurs
        $this->updatedOwnerFilter($value);
        // if updatedOwnerFilter resulted in a single supplier, generate invoice
        if (!empty($this->supplier_id)) {
            $this->generateInvoiceNumber($this->supplier_id);
        }
    }

    public function closeOwnerModal()
    {
        $this->showOwnerModal = false;
        $this->new_owner_name = '';
    }

    public function saveOwner()
    {
        $this->validate([
            'new_owner_name' => 'required|string|max:255',
        ]);

        try {
            // create a minimal supplier entry to persist owner value so it appears in owners list
            $supplier = Supplier::create([
                'kode_supplier' => 'SUP-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6)),
                'nama_supplier' => $this->new_owner_name,
                'owner' => $this->new_owner_name,
                'keterangan' => 'Owner: ' . $this->new_owner_name,
            ]);

            // set filter to new owner and reset supplier selection
            $this->ownerFilter = $this->new_owner_name;
            $this->supplier_id = null;
            $this->closeOwnerModal();
            session()->flash('message', 'Owner berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Log::error('Failed to create owner supplier: ' . $e->getMessage());
            session()->flash('error', 'Gagal menambahkan owner: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $this->resetForm();
        // reset owner filter to start fresh
        $this->ownerFilter = null;
        // set default store to the first store if available (warehouse kosong)
        $firstStore = Store::orderBy('nama_toko')->first();
        $this->store_id = $firstStore ? $firstStore->id : null;
        // warehouse_id dibiarkan kosong (null) - user pilih manual jika mau
        $this->warehouse_id = null;
        // set tanggal
        $this->tanggal_pembelian = date('Y-m-d');
        $this->editingPurchaseId = null;
        $this->showCreateForm = true;
        // set suppliers
        $this->suppliers = Supplier::when($this->ownerFilter, fn($q) => $q->where('owner', $this->ownerFilter))
            ->orderBy('nama_supplier')->get();
        // jika hanya ada satu perusahaan, pilih otomatis
        if ($this->suppliers instanceof \Illuminate\Support\Collection && $this->suppliers->count() === 1) {
            $this->supplier_id = $this->suppliers->first()->id;
            $this->generateInvoiceNumber($this->supplier_id);
        }
        // tambahkan satu baris item kosong agar form tidak tampak kosong
        $this->addItem();
    }

    public function edit($id)
    {
        $purchase = Purchase::with('purchaseItems')->findOrFail($id);
        $this->editingPurchaseId = $purchase->id;
        $this->no_invoice = $purchase->no_invoice;
        $this->tanggal_pembelian = $purchase->tanggal_pembelian->format('Y-m-d');
        $this->supplier_id = $purchase->supplier_id;
        // set owner filter based on the supplier's owner
        $supplier = Supplier::find($purchase->supplier_id);
        $this->ownerFilter = $supplier ? $supplier->owner : null;
        $this->store_id = $purchase->store_id;
        $this->warehouse_id = $purchase->warehouse_id;
        $this->status = $purchase->status;
        $this->keterangan = $purchase->keterangan;
        $this->purchaseItems = $purchase->purchaseItems->map(function ($item) {
            $qty = $item->qty ?? 0;
            $harga = $item->harga_beli ?? 0;
            $conv = 1;
            if (!empty($item['unit_id'])) {
                $unit = Unit::find($item['unit_id']);
                $conv = $unit ? (float) ($unit->conversion_value ?? 1) : 1;
            }
            $product = Product::find($item->product_id);
            $productSearchFormat = $product ? $product->nama_produk : '';
            return [
                'category_id' => $item->category_id,
                'subcategory_id' => $item->subcategory_id,
                'product_id' => $item->product_id,
                'product_search' => $productSearchFormat,
                'qty' => $item->qty,
                'qty_gudang' => $item->qty_gudang ?? 0,
                'unit_id' => $item->unit_id,
                'harga_beli' => $item->harga_beli,
                'total' => ($qty * $conv) * $harga,
            ];
        })->toArray();
        $this->showCreateForm = true;
        // set suppliers
        $this->suppliers = Supplier::when($this->ownerFilter, fn($q) => $q->where('owner', $this->ownerFilter))
            ->orderBy('nama_supplier')->get();
        // jika hanya ada satu perusahaan dan belum ada supplier terpilih, pilih otomatis
        if ($this->suppliers instanceof \Illuminate\Support\Collection && $this->suppliers->count() === 1 && empty($this->supplier_id)) {
            $this->supplier_id = $this->suppliers->first()->id;
        }
    }

    public function show($id)
    {
        $this->selectedPurchase = Purchase::with('supplier', 'purchaseItems.product', 'purchaseItems.category', 'purchaseItems.subcategory', 'purchaseItems.unit')->findOrFail($id);
        $this->showModal = true;
    }

    private function generateInvoiceNumber($supplierId)
    {
        // Create invoice number in format PB/YYYY/MM/DD-XXX where XXX increments per supplier per day
        $date = date('Y/m/d');
        $lastPurchase = Purchase::where('supplier_id', $supplierId)
            ->whereDate('tanggal_pembelian', date('Y/m/d'))
            ->orderByRaw("CAST(SUBSTRING_INDEX(no_invoice, '-', -1) AS UNSIGNED) DESC")
            ->first();
        if ($lastPurchase) {
            $parts = explode('-', $lastPurchase->no_invoice);
            $num = isset($parts[1]) ? intval($parts[1]) + 1 : 1;
        } else {
            $num = 1;
        }
        $this->no_invoice = 'PB/' . $date . '-' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedPurchase = null;
    }

    public function save()
    {
        $rules = [
            'tanggal_pembelian' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'store_id' => 'nullable|exists:stores,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'status' => 'required|in:pending,completed,cancelled',
            'keterangan' => 'nullable|string',
            'no_invoice' => 'required|string|max:50|unique:purchases,no_invoice' . ($this->editingPurchaseId ? (',' . $this->editingPurchaseId) : ''),
            'purchaseItems' => 'required|array|min:1',
            'purchaseItems.*.category_id' => 'required|exists:categories,id',
            'purchaseItems.*.subcategory_id' => 'nullable|exists:subcategories,id',
            'purchaseItems.*.product_id' => 'required|exists:products,id',
            'purchaseItems.*.qty' => 'required|integer|min:0',
            'purchaseItems.*.qty_gudang' => 'required|integer|min:0',
            'purchaseItems.*.unit_id' => 'required|exists:units,id',
            'purchaseItems.*.harga_beli' => 'required|numeric|min:0',
        ];

        $this->validate($rules);

        DB::beginTransaction();
        try {
            // Calculate totals for each item using unit conversion_value when available
            $unitIds = collect($this->purchaseItems)->pluck('unit_id')->filter()->unique()->values()->all();
            $conversionMap = [];
            if (!empty($unitIds)) {
                $conversionMap = Unit::whereIn('id', $unitIds)->pluck('conversion_value', 'id')->toArray();
            }

            foreach ($this->purchaseItems as &$item) {
                $qty = $item['qty'] ?? 0;
                $qty_gudang = $item['qty_gudang'] ?? 0;
                $harga = $item['harga_beli'] ?? 0;
                $conv = 1;
                if (!empty($item['unit_id']) && isset($conversionMap[$item['unit_id']])) {
                    $conv = (float) $conversionMap[$item['unit_id']];
                }
                // Use qty if filled, otherwise use qty_gudang
                $use_qty = ($qty > 0) ? $qty : $qty_gudang;
                $item['total'] = ($use_qty * $conv) * $harga;
            }

            if ($this->editingPurchaseId) {
                $purchase = Purchase::findOrFail($this->editingPurchaseId);
                $purchase->no_invoice = $this->no_invoice;
                $purchase->tanggal_pembelian = $this->tanggal_pembelian;
                $purchase->supplier_id = $this->supplier_id;
                $purchase->store_id = $this->store_id;
                $purchase->warehouse_id = $this->warehouse_id;
                $purchase->status = $this->status;
                $purchase->keterangan = $this->keterangan;
                $purchase->save();

                // Delete old stock adjustments for this purchase
                StockAdjustment::where('reason', 'like', '%Pembelian dari%')
                    ->whereDate('adjustment_date', $purchase->tanggal_pembelian)
                    ->where(function ($q) use ($purchase) {
                        $q->where('store_id', $purchase->store_id)
                            ->orWhere('warehouse_id', $purchase->warehouse_id);
                    })
                    ->delete();

                // Update purchase items
                $purchase->purchaseItems()->delete();
                foreach ($this->purchaseItems as $item) {
                    $purchase->purchaseItems()->create($item);

                    // Re-create StockAdjustment untuk Toko
                    if ($this->store_id && ($item['qty'] ?? 0) > 0) {
                        StockAdjustment::create([
                            'product_id' => $item['product_id'],
                            'store_id' => $this->store_id,
                            'warehouse_id' => null,
                            'adjustment_type' => 'add',
                            'quantity' => $item['qty'],
                            'stok_awal' => 0,
                            'stok_masuk' => $item['qty'],
                            'unit_id' => $item['unit_id'] ?? null,
                            'reason' => 'Pembelian dari ' . $this->getSupplerName(),
                            'adjustment_date' => $this->tanggal_pembelian,
                            'user_id' => Auth::id(),
                        ]);

                        // Create StockCard untuk Toko
                        $store = Store::find($this->store_id);
                        $this->createStockCardForPurchase(
                            $item['product_id'],
                            $item['qty'],
                            $store->nama_toko ?? 'Toko',
                            $purchase->id,
                            $this->no_invoice
                        );
                    }

                    // Re-create StockAdjustment untuk Gudang
                    if ($this->warehouse_id && ($item['qty_gudang'] ?? 0) > 0) {
                        StockAdjustment::create([
                            'product_id' => $item['product_id'],
                            'store_id' => null,
                            'warehouse_id' => $this->warehouse_id,
                            'adjustment_type' => 'add',
                            'quantity' => $item['qty_gudang'],
                            'stok_awal' => 0,
                            'stok_masuk' => $item['qty_gudang'],
                            'unit_id' => $item['unit_id'] ?? null,
                            'reason' => 'Pembelian dari ' . $this->getSupplerName(),
                            'adjustment_date' => $this->tanggal_pembelian,
                            'user_id' => Auth::id(),
                        ]);

                        // Create StockCard untuk Gudang
                        $warehouse = \App\Models\Warehouse::find($this->warehouse_id);
                        $this->createStockCardForPurchase(
                            $item['product_id'],
                            $item['qty_gudang'],
                            $warehouse->nama_gudang ?? 'Gudang',
                            $purchase->id,
                            $this->no_invoice
                        );
                    }
                }
                session()->flash('message', 'Pembelian diperbarui.');
            } else {
                $purchase = Purchase::create([
                    'no_invoice' => $this->no_invoice,
                    'tanggal_pembelian' => $this->tanggal_pembelian,
                    'supplier_id' => $this->supplier_id,
                    'store_id' => $this->store_id,
                    'warehouse_id' => $this->warehouse_id,
                    'status' => $this->status,
                    'keterangan' => $this->keterangan,
                ]);

                // Create purchase items
                foreach ($this->purchaseItems as $item) {
                    $purchaseItem = $purchase->purchaseItems()->create($item);

                    // Create StockAdjustment untuk Toko
                    if ($this->store_id && ($item['qty'] ?? 0) > 0) {
                        StockAdjustment::create([
                            'product_id' => $item['product_id'],
                            'store_id' => $this->store_id,
                            'warehouse_id' => null,
                            'adjustment_type' => 'add',
                            'quantity' => $item['qty'],
                            'stok_awal' => 0,
                            'stok_masuk' => $item['qty'],
                            'unit_id' => $item['unit_id'] ?? null,
                            'reason' => 'Pembelian dari ' . $this->getSupplerName(),
                            'adjustment_date' => $this->tanggal_pembelian,
                            'user_id' => Auth::id(),
                        ]);

                        // Create StockCard untuk Toko
                        $store = Store::find($this->store_id);
                        $this->createStockCardForPurchase(
                            $item['product_id'],
                            $item['qty'],
                            $store->nama_toko ?? 'Toko',
                            $purchase->id,
                            $this->no_invoice
                        );
                    }

                    // Create StockAdjustment untuk Gudang
                    if ($this->warehouse_id && ($item['qty_gudang'] ?? 0) > 0) {
                        StockAdjustment::create([
                            'product_id' => $item['product_id'],
                            'store_id' => null,
                            'warehouse_id' => $this->warehouse_id,
                            'adjustment_type' => 'add',
                            'quantity' => $item['qty_gudang'],
                            'stok_awal' => 0,
                            'stok_masuk' => $item['qty_gudang'],
                            'unit_id' => $item['unit_id'] ?? null,
                            'reason' => 'Pembelian dari ' . $this->getSupplerName(),
                            'adjustment_date' => $this->tanggal_pembelian,
                            'user_id' => Auth::id(),
                        ]);

                        // Create StockCard untuk Gudang
                        $warehouse = \App\Models\Warehouse::find($this->warehouse_id);
                        $this->createStockCardForPurchase(
                            $item['product_id'],
                            $item['qty_gudang'],
                            $warehouse->nama_gudang ?? 'Gudang',
                            $purchase->id,
                            $this->no_invoice
                        );
                    }
                }
                session()->flash('message', 'Pembelian dibuat.');

                // Create transaction history entry
                try {
                    $totalAmount = collect($this->purchaseItems)->sum(function ($item) {
                        return ($item['qty'] ?? 0) * ($item['harga_beli'] ?? 0);
                    });

                    \App\Models\TransactionHistory::create([
                        'transaction_code' => $purchase->no_invoice,
                        'transaction_type' => 'pembelian',
                        'reference_type' => 'purchase',
                        'reference_id' => $purchase->id,
                        'transaction_date' => now(),
                        'amount' => $totalAmount,
                        'currency' => 'IDR',
                        'description' => 'Pembelian - ' . $purchase->no_invoice . ' dari ' . $this->getSupplerName(),
                        'status' => 'completed',
                        'user_id' => Auth::id(),
                        'notes' => $purchase->keterangan,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to create TransactionHistory for Purchase: ' . $e->getMessage());
                }
            }

            DB::commit();
            $this->resetForm();
            $this->showCreateForm = false;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase save error: ' . $e->getMessage());
            session()->flash('error', 'Gagal menyimpan pembelian: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->showCreateForm = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        try {
            $purchase = Purchase::findOrFail($id);

            // Delete related stock adjustments first
            StockAdjustment::where('adjustment_type', 'add')
                ->whereDate('adjustment_date', $purchase->tanggal_pembelian)
                ->where('reason', 'like', '%Pembelian dari%')
                ->delete();

            // Delete purchase items
            $purchase->purchaseItems()->delete();

            // Delete purchase
            $purchase->delete();

            session()->flash('message', 'Pembelian dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus pembelian: ' . $e->getMessage());
            Log::error('Delete purchase error: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->delete($id);
    }

    public function updatedSupplierId($value)
    {
        if ($value) {
            $this->generateInvoiceNumber($value);
        }
    }

    public function addItem()
    {
        $this->purchaseItems[] = [
            'category_id' => null,
            'subcategory_id' => null,
            'product_id' => null,
            'product_search' => '', // For searchable product input
            'qty' => 0,
            'unit_id' => null,
            'harga_beli' => 0,
            'total' => 0,
        ];
        // ensure totals are recalculated after adding an item
        $this->computeAllTotals();
        $this->purchaseItems = array_merge([], $this->purchaseItems); // Force reactivity
    }

    public function updateField($field, $value, $index)
    {
        $this->purchaseItems[$index][$field] = $value;
        $this->computeAllTotals();
        $this->purchaseItems = array_merge([], $this->purchaseItems);
        $this->dispatch('console-log', ['purchaseItems' => $this->purchaseItems]);
    }

    public function updatedPurchaseItems($value, $name)
    {
        $this->dispatch('console-log', ['message' => 'updatedPurchaseItems called for ' . $name . ' with value ' . json_encode($value)]);
        $parts = explode('.', $name);
        if (count($parts) === 3 && $parts[2] === 'product_id') {
            $index = $parts[1];
            $productId = $value;
            if ($productId) {
                $product = Product::find($productId);
                if ($product) {
                    $this->purchaseItems[$index]['category_id'] = $product->category_id;
                    $this->purchaseItems[$index]['subcategory_id'] = $product->subcategory_id;
                    $this->purchaseItems[$index]['unit_id'] = $product->unit_id;
                }
            }
            // Recalculate totals after setting unit_id
            $this->computeAllTotals();
            $this->purchaseItems = array_merge([], $this->purchaseItems); // Force reactivity
        }

        // Handle product_search input (searchable product selection)
        if (count($parts) === 3 && $parts[2] === 'product_search') {
            $index = $parts[1];
            $searchValue = $value;
            if ($searchValue) {
                // Search product by name
                $product = Product::where('nama_produk', $searchValue)->first();
                if ($product) {
                    $this->purchaseItems[$index]['product_id'] = $product->id;
                    $this->purchaseItems[$index]['category_id'] = $product->category_id;
                    $this->purchaseItems[$index]['subcategory_id'] = $product->subcategory_id;
                    $this->purchaseItems[$index]['unit_id'] = $product->unit_id;
                    // Recalculate totals
                    $this->computeAllTotals();
                    $this->purchaseItems = array_merge([], $this->purchaseItems);
                }
            }
        }

        // Recalculate item total when qty, unit_id or harga_beli changes
        if (count($parts) === 3 && in_array($parts[2], ['qty', 'unit_id', 'harga_beli'])) {
            $index = $parts[1];
            if (!isset($this->purchaseItems[$index])) {
                return;
            }
            $item = $this->purchaseItems[$index];
            // normalize values to numeric to avoid string arithmetic issues
            $qty = isset($item['qty']) && $item['qty'] !== null ? (float) $item['qty'] : 0;
            $harga = isset($item['harga_beli']) && $item['harga_beli'] !== null ? (float) $item['harga_beli'] : 0;
            $conv = 1;
            if (!empty($item['unit_id'])) {
                $unit = Unit::find($item['unit_id']);
                $conv = $unit ? (float) ($unit->conversion_value ?: 1) : 1;
            }
            // ensure total is stored as numeric (float)
            $this->purchaseItems[$index]['total'] = (float) (($qty * $conv) * $harga);

            // recompute all totals to ensure consistency and force Livewire to sync the updated values
            $this->computeAllTotals();
            $this->purchaseItems = array_merge([], $this->purchaseItems); // Force reactivity
        }
    }

    public function getTotalProperty()
    {
        // get unit conversion values for units used in items
        $unitIds = collect($this->purchaseItems)->pluck('unit_id')->filter()->unique()->values()->all();
        $conversionMap = [];
        if (!empty($unitIds)) {
            $conversionMap = Unit::whereIn('id', $unitIds)->pluck('conversion_value', 'id')->toArray();
        }

        return collect($this->purchaseItems)->sum(function ($item) use ($conversionMap) {
            $qty = isset($item['qty']) && $item['qty'] !== null ? (float) $item['qty'] : 0;
            $qty_gudang = isset($item['qty_gudang']) && $item['qty_gudang'] !== null ? (float) $item['qty_gudang'] : 0;
            // Use qty if filled, otherwise use qty_gudang
            $use_qty = ($qty > 0) ? $qty : $qty_gudang;
            $harga = isset($item['harga_beli']) && $item['harga_beli'] !== null ? (float) $item['harga_beli'] : 0;
            $conv = 1;
            if (!empty($item['unit_id']) && isset($conversionMap[$item['unit_id']])) {
                $conv = (float) ($conversionMap[$item['unit_id']] ?: 1);
            }
            return ($use_qty * $conv) * $harga;
        });
    }

    /**
     * Recompute totals for all purchase items using unit conversion values.
     * This ensures the server-side array contains up-to-date numeric totals
     * which Livewire will sync back to the client.
     */
    protected function computeAllTotals()
    {
        if (empty($this->purchaseItems)) {
            return;
        }

        $unitIds = collect($this->purchaseItems)->pluck('unit_id')->filter()->unique()->values()->all();
        $conversionMap = [];
        if (!empty($unitIds)) {
            $conversionMap = Unit::whereIn('id', $unitIds)->pluck('conversion_value', 'id')->toArray();
        }

        foreach ($this->purchaseItems as $i => $item) {
            $qty = isset($item['qty']) && $item['qty'] !== null ? (float) $item['qty'] : 0;
            $harga = isset($item['harga_beli']) && $item['harga_beli'] !== null ? (float) $item['harga_beli'] : 0;
            $conv = 1;
            if (!empty($item['unit_id']) && isset($conversionMap[$item['unit_id']])) {
                $conv = (float) ($conversionMap[$item['unit_id']] ?: 1);
            }
            $this->purchaseItems[$i]['total'] = (float) (($qty * $conv) * $harga);
        }
    }

    public function resetForm()
    {
        $this->no_invoice = null;
        $this->tanggal_pembelian = null;
        $this->supplier_id = null;
        $this->store_id = null;
        $this->warehouse_id = null;
        $this->status = 'pending';
        $this->keterangan = null;
        $this->editingPurchaseId = null;
        $this->showCreateForm = false;
        $this->showModal = false;
        $this->selectedPurchase = null;
        $this->purchaseItems = [];
    }

    private function getSupplerName()
    {
        if ($this->supplier_id) {
            // Cache supplier lookup to avoid repeated queries
            static $supplierCache = [];

            if (!isset($supplierCache[$this->supplier_id])) {
                $supplier = Supplier::find($this->supplier_id);
                $supplierCache[$this->supplier_id] = $supplier ? $supplier->nama_supplier : 'Unknown';
            }

            return $supplierCache[$this->supplier_id];
        }
        return 'Unknown';
    }

    private function createStockCardForPurchase($productId, $qty, $toLocation = '', $purchaseId = null, $invoiceNo = null)
    {
        /**
         * Buat StockCard untuk audit trail pembelian
         * StockCard mencatat setiap pergerakan stok dengan detail lengkap
         */
        try {
            $stockCardService = app(StockCardService::class);

            $stockCardService->createStockCard([
                'product_id' => $productId,
                'batch_id' => null,
                'type' => 'in', // Tipe = masuk (pembelian)
                'qty' => $qty,
                'from_location' => $this->getSupplerName(), // Dari: Supplier
                'to_location' => $toLocation, // Ke: Toko/Gudang
                'reference_type' => 'purchase', // Referensi dari Pembelian
                'reference_id' => $purchaseId, // ID Pembelian
                'note' => "Pembelian - Invoice: {$invoiceNo}",
            ]);
        } catch (\Exception $e) {
            // Log error tapi jangan hentikan proses pembelian
            Log::warning("Failed to create StockCard for Purchase {$invoiceNo}: " . $e->getMessage());
        }
    }

    public function updateCategoryFilter($index)
    {
        // Clear subcategory and product when category changes
        if (!isset($this->purchaseItems[$index])) {
            return;
        }

        $val = $this->purchaseItems[$index]['category_id'] ?? null;
        // If the special magic value was set (from the CTA option), do nothing here
        if ($val === '__add_category__') {
            return;
        }

        $this->purchaseItems[$index]['subcategory_id'] = null;
        $this->purchaseItems[$index]['product_id'] = null;
    }

    /**
     * Handle combined location selection from UI.
     * Expected format: "store:{id}" or "warehouse:{id}" or empty string.
     */
    public function selectLocation($value)
    {
        if (empty($value)) {
            $this->store_id = null;
            $this->warehouse_id = null;
            return;
        }

        if (str_starts_with($value, 'store:')) {
            $id = (int) substr($value, strlen('store:'));
            $this->store_id = $id ?: null;
            $this->warehouse_id = null;
            return;
        }

        if (str_starts_with($value, 'warehouse:')) {
            $id = (int) substr($value, strlen('warehouse:'));
            $this->warehouse_id = $id ?: null;
            $this->store_id = null;
            return;
        }

        // Fallback: clear both
        $this->store_id = null;
        $this->warehouse_id = null;
    }

    // Open/close modal helpers
    public function openCategoryModal($row = null)
    {
        $this->category_modal_row = is_numeric($row) ? (int) $row : null;
        $this->new_category_name = '';
        $this->showCategoryModal = true;
    }

    public function closeCategoryModal()
    {
        $this->showCategoryModal = false;
        $this->new_category_name = '';
        $this->category_modal_row = null;
    }

    public function saveCategoryModal()
    {
        $name = trim($this->new_category_name ?? '');
        if ($name === '') {
            session()->flash('error', 'Nama kategori tidak boleh kosong.');
            return;
        }
        $this->addCategory($name, $this->category_modal_row);
        $this->closeCategoryModal();
    }

    public function updateSubcategoryFilter($index)
    {
        // Clear product when subcategory changes
        if (!isset($this->purchaseItems[$index])) {
            return;
        }

        $val = $this->purchaseItems[$index]['subcategory_id'] ?? null;
        if ($val === '__add_subcategory__') {
            return;
        }

        $this->purchaseItems[$index]['product_id'] = null;
    }

    /**
     * Create a new category from the purchases UI and assign it to the row.
     */
    public function addCategory($name, $index = null)
    {
        $name = trim($name ?? '');
        if ($name === '') {
            session()->flash('error', 'Nama kategori tidak boleh kosong.');
            return;
        }

        $category = Category::create([
            'kode_kategori' => 'CAT-' . Str::upper(Str::random(6)),
            'nama_kategori' => $name,
            'description' => null,
        ]);

        // assign to the item row if index provided
        if (is_numeric($index) && isset($this->purchaseItems[$index])) {
            $this->purchaseItems[$index]['category_id'] = $category->id;
            $this->purchaseItems[$index]['subcategory_id'] = null;
            $this->purchaseItems[$index]['product_id'] = null;
            $this->purchaseItems = array_merge([], $this->purchaseItems);
        }

        session()->flash('message', 'Kategori "' . $category->nama_kategori . '" berhasil dibuat.');
    }

    /**
     * Create a new subcategory under the selected category for the given row.
     */
    public function addSubcategory($name, $index = null)
    {
        $name = trim($name ?? '');
        if ($name === '') {
            session()->flash('error', 'Nama subkategori tidak boleh kosong.');
            return;
        }

        if (!is_numeric($index) || !isset($this->purchaseItems[$index])) {
            session()->flash('error', 'Baris item tidak valid.');
            return;
        }

        $categoryId = $this->purchaseItems[$index]['category_id'] ?? null;
        if (empty($categoryId)) {
            session()->flash('error', 'Pilih kategori terlebih dahulu.');
            return;
        }

        $sub = Subcategory::create([
            'kode_subkategori' => 'SUB-' . Str::upper(Str::random(6)),
            'nama_subkategori' => $name,
            'description' => null,
            'category_id' => $categoryId,
        ]);

        $this->purchaseItems[$index]['subcategory_id'] = $sub->id;
        $this->purchaseItems[$index]['product_id'] = null;
        $this->purchaseItems = array_merge([], $this->purchaseItems);

        session()->flash('message', 'Subkategori "' . $sub->nama_subkategori . '" berhasil dibuat.');
    }

    public function openSubcategoryModal($row = null)
    {
        $this->subcategory_modal_row = is_numeric($row) ? (int) $row : null;
        // try to prefill category id from row if available
        if (is_numeric($row) && isset($this->purchaseItems[$row]) && !empty($this->purchaseItems[$row]['category_id'])) {
            $this->subcategory_modal_category_id = $this->purchaseItems[$row]['category_id'];
        } else {
            $this->subcategory_modal_category_id = null;
        }
        $this->new_subcategory_name = '';
        $this->showSubcategoryModal = true;
    }

    public function closeSubcategoryModal()
    {
        $this->showSubcategoryModal = false;
        $this->new_subcategory_name = '';
        $this->subcategory_modal_row = null;
        $this->subcategory_modal_category_id = null;
    }

    public function saveSubcategoryModal()
    {
        $name = trim($this->new_subcategory_name ?? '');
        $catId = $this->subcategory_modal_category_id ?? null;
        if ($name === '') {
            session()->flash('error', 'Nama subkategori tidak boleh kosong.');
            return;
        }
        if (empty($catId)) {
            session()->flash('error', 'Pilih kategori terlebih dahulu.');
            return;
        }

        // create and assign
        $sub = Subcategory::create([
            'kode_subkategori' => 'SUB-' . Str::upper(Str::random(6)),
            'nama_subkategori' => $name,
            'description' => null,
            'category_id' => $catId,
        ]);

        if (is_numeric($this->subcategory_modal_row) && isset($this->purchaseItems[$this->subcategory_modal_row])) {
            $this->purchaseItems[$this->subcategory_modal_row]['subcategory_id'] = $sub->id;
            $this->purchaseItems = array_merge([], $this->purchaseItems);
        }

        session()->flash('message', 'Subkategori "' . $sub->nama_subkategori . '" berhasil dibuat.');
        $this->closeSubcategoryModal();
    }

    /**
     * Create a new product under the selected category/subcategory for the given row.
     */
    public function addProduct($name, $index = null)
    {
        $name = trim($name ?? '');
        if ($name === '') {
            session()->flash('error', 'Nama produk tidak boleh kosong.');
            return;
        }

        if (!is_numeric($index) || !isset($this->purchaseItems[$index])) {
            session()->flash('error', 'Baris item tidak valid.');
            return;
        }

        $categoryId = $this->purchaseItems[$index]['category_id'] ?? null;
        if (empty($categoryId)) {
            session()->flash('error', 'Pilih kategori terlebih dahulu.');
            return;
        }

        $subcategoryId = $this->purchaseItems[$index]['subcategory_id'] ?? null;

        $product = Product::create([
            'kode_produk' => 'PRD-' . Str::upper(Str::random(6)),
            'nama_produk' => $name,
            'description' => null,
            'satuan' => null,
            'supplier_id' => null,
            'category_id' => $categoryId,
            'subcategory_id' => $subcategoryId,
        ]);

        $this->purchaseItems[$index]['product_id'] = $product->id;
        $this->purchaseItems[$index]['product_search'] = $product->nama_produk;
        $this->purchaseItems = array_merge([], $this->purchaseItems);

        session()->flash('message', 'Produk "' . $product->nama_produk . '" berhasil dibuat.');
    }

    public function openProductModal($row = null)
    {
        $this->product_modal_row = is_numeric($row) ? (int) $row : null;
        if (is_numeric($row) && isset($this->purchaseItems[$row])) {
            $this->product_modal_category_id = $this->purchaseItems[$row]['category_id'] ?? null;
            $this->product_modal_subcategory_id = $this->purchaseItems[$row]['subcategory_id'] ?? null;
        } else {
            $this->product_modal_category_id = null;
            $this->product_modal_subcategory_id = null;
        }
        $this->new_product_name = '';
        $this->showProductModal = true;
    }

    public function closeProductModal()
    {
        $this->showProductModal = false;
        $this->new_product_name = '';
        $this->product_modal_row = null;
        $this->product_modal_category_id = null;
        $this->product_modal_subcategory_id = null;
    }

    public function saveProductModal()
    {
        $name = trim($this->new_product_name ?? '');
        if ($name === '') {
            session()->flash('error', 'Nama produk tidak boleh kosong.');
            return;
        }
        $categoryId = $this->product_modal_category_id ?? null;
        if (empty($categoryId)) {
            session()->flash('error', 'Pilih kategori terlebih dahulu.');
            return;
        }

        $product = Product::create([
            'kode_produk' => 'PRD-' . Str::upper(Str::random(6)),
            'nama_produk' => $name,
            'description' => null,
            'satuan' => null,
            'supplier_id' => null,
            'category_id' => $categoryId,
            'subcategory_id' => $this->product_modal_subcategory_id,
        ]);

        if (is_numeric($this->product_modal_row) && isset($this->purchaseItems[$this->product_modal_row])) {
            $this->purchaseItems[$this->product_modal_row]['product_id'] = $product->id;
            $this->purchaseItems[$this->product_modal_row]['product_search'] = $product->nama_produk;
            $this->purchaseItems = array_merge([], $this->purchaseItems);
        }

        session()->flash('message', 'Produk "' . $product->nama_produk . '" berhasil dibuat.');
        $this->closeProductModal();
    }

    public function updateTotal($index)
    {
        // Calculate total for this item based on qty toko or qty gudang (not both)
        if (isset($this->purchaseItems[$index])) {
            $qty = $this->purchaseItems[$index]['qty'] ?? 0;
            $qty_gudang = $this->purchaseItems[$index]['qty_gudang'] ?? 0;
            $harga = $this->purchaseItems[$index]['harga_beli'] ?? 0;
            $unit_id = $this->purchaseItems[$index]['unit_id'] ?? null;

            // Use qty if filled, otherwise use qty_gudang
            $use_qty = ($qty > 0) ? $qty : $qty_gudang;

            // Get unit conversion value
            $conv = 1;
            if ($unit_id) {
                $unit = Unit::find($unit_id);
                $conv = $unit ? (float)($unit->conversion_value ?: 1) : 1;
            }

            $this->purchaseItems[$index]['total'] = ($use_qty * $conv) * $harga;
        }
    }

    public function removeItem($index)
    {
        unset($this->purchaseItems[$index]);
        $this->purchaseItems = array_values($this->purchaseItems);
    }

    // Supplier modal methods
    public function openSupplierModal()
    {
        $this->showSupplierModal = true;
        $this->kode_supplier = null;
        $this->nama_supplier = null;
        $this->telepon = null;
        $this->email = null;
        $this->alamat = null;
        $this->supplier_keterangan = null;
    }

    public function closeSupplierModal()
    {
        $this->showSupplierModal = false;
        $this->kode_supplier = null;
        $this->nama_supplier = null;
        $this->telepon = null;
        $this->email = null;
        $this->alamat = null;
        $this->supplier_keterangan = null;
    }

    public function saveSupplier()
    {
        $this->validate([
            'kode_supplier' => 'required|string|max:50|unique:suppliers,kode_supplier',
            'nama_supplier' => 'required|string|max:255',
        ]);

        try {
            $supplier = Supplier::create([
                'kode_supplier' => $this->kode_supplier,
                'nama_supplier' => $this->nama_supplier,
                'telepon' => $this->telepon ?? null,
                'email' => $this->email ?? null,
                'alamat' => $this->alamat ?? null,
                'keterangan' => $this->supplier_keterangan ?? null,
            ]);

            // Refresh suppliers list and select the newly created supplier
            $this->suppliers = Supplier::orderBy('nama_supplier')->get();
            $this->supplier_id = $supplier->id;
            $this->closeSupplierModal();
            session()->flash('message', 'Supplier berhasil disimpan.');
        } catch (\Exception $e) {
            \Log::error('Failed to save supplier: ' . $e->getMessage());
            session()->flash('error', 'Gagal menyimpan supplier: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Purchase::with([
            'supplier',
            'purchaseItems.product.category',
            'purchaseItems.product.subcategory',
            'purchaseItems.unit'
        ]);

        // DataTables will handle searching and pagination, so we get all data
        $purchases = $query->orderBy('tanggal_pembelian', 'desc')->get();

        // Only load form data if form is shown
        if ($this->showCreateForm) {
            $categories = Category::orderBy('nama_kategori')->get();
            $subcategories = Subcategory::orderBy('nama_subkategori')->get();
            $products = Product::orderBy('nama_produk')->get();
            $units = Unit::orderBy('nama_unit')->get();
            $stores = Store::orderBy('nama_toko')->get();
            $warehouses = \App\Models\Warehouse::orderBy('nama_gudang')->get();

            // Ensure suppliers are computed on render so UI reflects current ownerFilter reliably
            $query = Supplier::orderBy('nama_supplier');
            if (!empty($this->ownerFilter)) {
                if (str_contains($this->ownerFilter, ',')) {
                    $owners = array_map('trim', explode(',', $this->ownerFilter));
                    $query->where(function ($q2) use ($owners) {
                        foreach ($owners as $o) {
                            if ($o !== '') {
                                $q2->orWhere('owner', 'like', '%' . $o . '%');
                            }
                        }
                    });
                } else {
                    $query->where('owner', 'like', '%' . $this->ownerFilter . '%');
                }
            }
            $this->suppliers = $query->get();
            $this->ownerTokens = !empty($this->ownerFilter) ? (str_contains($this->ownerFilter, ',') ? array_map('trim', explode(',', $this->ownerFilter)) : [$this->ownerFilter]) : [];
            // Auto-select if single result and none selected
            if (empty($this->supplier_id) && $this->suppliers->count() === 1) {
                $this->supplier_id = $this->suppliers->first()->id;
                // generate invoice number immediately when supplier auto-selected
                $this->generateInvoiceNumber($this->supplier_id);
                // generate invoice number immediately when supplier auto-selected
                $this->generateInvoiceNumber($this->supplier_id);
            }
        } else {
            $categories = [];
            $subcategories = [];
            $products = [];
            $units = [];
            $stores = [];
            $warehouses = [];
        }

        return view('livewire.admin.purchases', [
            'purchases' => $purchases,
            'owners' => Supplier::select('owner')->whereNotNull('owner')->where('owner', '<>', '')->distinct()->orderBy('owner')->pluck('owner'),
            'categories' => $categories,
            'subcategories' => $subcategories,
            'products' => $products,
            'units' => $units,
            'stores' => $stores,
            'warehouses' => $warehouses,
        ]);
    }
}
