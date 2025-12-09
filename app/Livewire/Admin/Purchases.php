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

#[Layout('layouts.admin')]
class Purchases extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
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

    // Supplier form modal
    public $showSupplierModal = false;
    public $kode_supplier = '';
    public $nama_supplier = '';
    public $telepon = '';
    public $email = '';
    public $alamat = '';
    public $editingSupplier = null;

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
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        // set default store to the first store if available (warehouse kosong)
        $firstStore = Store::orderBy('nama_toko')->first();
        $this->store_id = $firstStore ? $firstStore->id : null;
        // warehouse_id dibiarkan kosong (null) - user pilih manual jika mau
        $this->warehouse_id = null;
        // set tanggal
        $this->tanggal_pembelian = date('Y-m-d');
        $this->editingPurchaseId = null;
        $this->showCreateForm = true;
    }

    public function edit($id)
    {
        $purchase = Purchase::with('purchaseItems')->findOrFail($id);
        $this->editingPurchaseId = $purchase->id;
        $this->no_invoice = $purchase->no_invoice;
        $this->tanggal_pembelian = $purchase->tanggal_pembelian->format('Y-m-d');
        $this->supplier_id = $purchase->supplier_id;
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
    }

    public function show($id)
    {
        $this->selectedPurchase = Purchase::with('supplier', 'purchaseItems.product', 'purchaseItems.category', 'purchaseItems.subcategory', 'purchaseItems.unit')->findOrFail($id);
        $this->showModal = true;
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
            $date = date('Y/m/d');
            $lastPurchase = Purchase::where('supplier_id', $value)
                ->whereDate('tanggal_pembelian', date('Y-m/d'))
                ->orderByRaw("CAST(SUBSTRING_INDEX(no_invoice, '-', -1) AS UNSIGNED) DESC")
                ->first();
            if ($lastPurchase) {
                $parts = explode('-', $lastPurchase->no_invoice);
                $num = intval($parts[1]) + 1;
            } else {
                $num = 1;
            }
            $this->no_invoice = 'PB/' . $date . '-' . str_pad($num, 3, '0', STR_PAD_LEFT);
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

            // Log after calculation so we can confirm what's stored server-side
            Log::debug('Livewire updatedPurchaseItems computed', ['index' => $index, 'item' => $this->purchaseItems[$index]]);

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
        if (isset($this->purchaseItems[$index])) {
            $this->purchaseItems[$index]['subcategory_id'] = null;
            $this->purchaseItems[$index]['product_id'] = null;
        }
    }

    public function updateSubcategoryFilter($index)
    {
        // Clear product when subcategory changes
        if (isset($this->purchaseItems[$index])) {
            $this->purchaseItems[$index]['product_id'] = null;
        }
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
        $this->resetSupplierForm();
    }

    public function closeSupplierModal()
    {
        $this->showSupplierModal = false;
        $this->resetSupplierForm();
    }

    public function resetSupplierForm()
    {
        $this->kode_supplier = '';
        $this->nama_supplier = '';
        $this->telepon = '';
        $this->email = '';
        $this->alamat = '';
        $this->editingSupplier = null;
    }

    public function saveSupplier()
    {
        $rules = [
            'kode_supplier' => 'required|string|max:50|unique:suppliers,kode_supplier' . ($this->editingSupplier ? (',' . $this->editingSupplier) : ''),
            'nama_supplier' => 'required|string|max:100|unique:suppliers,nama_supplier' . ($this->editingSupplier ? (',' . $this->editingSupplier) : ''),
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'alamat' => 'nullable|string',
        ];

        $this->validate($rules);

        try {
            if ($this->editingSupplier) {
                $supplier = Supplier::findOrFail($this->editingSupplier);
                $supplier->update([
                    'kode_supplier' => $this->kode_supplier,
                    'nama_supplier' => $this->nama_supplier,
                    'telepon' => $this->telepon,
                    'email' => $this->email,
                    'alamat' => $this->alamat,
                    'keterangan' => $this->keterangan,
                ]);
                session()->flash('message', 'Pemasok berhasil diperbarui.');
            } else {
                Supplier::create([
                    'kode_supplier' => $this->kode_supplier,
                    'nama_supplier' => $this->nama_supplier,
                    'telepon' => $this->telepon,
                    'email' => $this->email,
                    'alamat' => $this->alamat,
                    'keterangan' => $this->keterangan,
                ]);
                session()->flash('message', 'Pemasok berhasil ditambahkan.');
            }
            $this->closeSupplierModal();
        } catch (\Exception $e) {
            Log::error('Save supplier error: ' . $e->getMessage());
            session()->flash('error', 'Gagal menyimpan pemasok: ' . $e->getMessage());
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
            $suppliers = Supplier::orderBy('nama_supplier')->get();
            $categories = Category::orderBy('nama_kategori')->get();
            $subcategories = Subcategory::orderBy('nama_subkategori')->get();
            $products = Product::orderBy('nama_produk')->get();
            $units = Unit::orderBy('nama_unit')->get();
            $stores = Store::orderBy('nama_toko')->get();
            $warehouses = \App\Models\Warehouse::orderBy('nama_gudang')->get();
        } else {
            $suppliers = [];
            $categories = [];
            $subcategories = [];
            $products = [];
            $units = [];
            $stores = [];
            $warehouses = [];
        }

        return view('livewire.admin.purchases', [
            'purchases' => $purchases,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'products' => $products,
            'units' => $units,
            'stores' => $stores,
            'warehouses' => $warehouses,
        ]);
    }
}
