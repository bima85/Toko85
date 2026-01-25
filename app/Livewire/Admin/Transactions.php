<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockAdjustment;
use App\Models\StockBatch;
use App\Models\StockCard;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Unit;
use App\Services\StockBatchService;
use App\Services\StockCardService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Transactions extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $tab = 'sale'; // or 'purchase'

    public $products;

    public array $productSuggestions = [];

    // Purchase form properties (matching Purchases component)
    public $no_invoice;

    public $tanggal_pembelian;

    public $supplier_id;

    public $store_id;

    public $warehouse_id;

    public $status = 'completed';

    public $keterangan;

    public $ownerFilter = null;

    public $suppliers;

    public $owners;

    public $stores;

    public $warehouses;

    public $categories;

    public $subcategories;

    public $units;

    // Purchase items with full structure
    public $purchaseItems = [];

    public $batch_enabled = false; // Global batch toggle

    // Batch input dinamis
    public $showCreateBatchSection = false;

    public $batchNameList = [];

    public $batchQtyList = [];

    public $batchLocationList = [];

    public $showOwnerModal = false;

    public $new_owner_name = '';

    public $showSupplierModal = false;

    public $kode_supplier;

    public $nama_supplier;

    public $telepon;

    public $email;

    public $alamat;

    public $supplier_keterangan;

    // Legacy properties (keeping for backward compatibility)
    public $purchase_location_type = 'store';

    public $purchase_location_id = null;

    public $purchase_tumpukan_name = '';

    public $saleItems = [];

    public $sale_location_type = 'store';

    public $sale_location_id = null;

    // Realtime activity filters
    public $recent_search = '';

    public $recent_location = ''; // format: store:{id} or warehouse:{id} or empty

    protected $rules = [
        'tanggal_pembelian' => 'required|date',
        'supplier_id' => 'required|exists:suppliers,id',
        'store_id' => 'required|exists:stores,id',
        'keterangan' => 'nullable|string|max:500',
        'purchaseItems' => 'required|array|min:1',
        'purchaseItems.*.category_id' => 'required|exists:categories,id',
        'purchaseItems.*.product_id' => 'required|exists:products,id',
        'purchaseItems.*.qty' => 'required|integer|min:1',
        'purchaseItems.*.harga_beli' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->products = Product::orderBy('nama_produk')->limit(200)->get();
        $this->suppliers = Supplier::orderBy('nama_supplier')->get();
        $this->owners = Supplier::pluck('nama_supplier')->unique()->sort()->values();
        $this->stores = Store::orderBy('nama_toko')->get();
        $this->warehouses = \App\Models\Warehouse::orderBy('nama_gudang')->get();
        $this->categories = Category::orderBy('nama_kategori')->get();
        $this->subcategories = collect();
        $this->units = Unit::orderBy('nama_unit')->get();

        // Generate invoice number
        $this->no_invoice = 'PUR-'.now()->format('YmdHis');
        $this->tanggal_pembelian = now()->format('Y-m-d');

        // Initialize purchase items with full structure
        $this->purchaseItems = [[
            'category_id' => null,
            'subcategory_id' => null,
            'product_id' => null,
            'qty' => 1,
            'unit_id' => null,
            'harga_beli' => 0,
            'use_batch' => false,
            'batch_name' => '',
            'batch_qty' => 0,
            'batch_location' => '',
            'batches' => [
                ['name' => '', 'qty' => 0],
            ],
        ]];

        $this->saleItems = [['product_id' => null, 'qty' => 1, 'harga_jual' => 0]];

        // Ensure all purchase items have required batch keys
        $this->normalizePurchaseItems();
    }

    private function normalizePurchaseItems()
    {
        foreach ($this->purchaseItems as $index => $item) {
            if (! isset($item['use_batch'])) {
                $this->purchaseItems[$index]['use_batch'] = false;
            }
            if (! isset($item['batches'])) {
                $this->purchaseItems[$index]['batches'] = [];
            }
            if (! isset($item['batch_name'])) {
                $this->purchaseItems[$index]['batch_name'] = '';
            }
            if (! isset($item['batch_qty'])) {
                $this->purchaseItems[$index]['batch_qty'] = 0;
            }
            if (! isset($item['batch_location'])) {
                $this->purchaseItems[$index]['batch_location'] = '';
            }
        }
    }

    public function updatedTab($value)
    {
        if ($value === 'purchase') {
            // Initialize purchase form when switching to purchase tab
            $this->resetPurchaseForm();
        }
    }

    public function addSaleItem()
    {
        $this->saleItems[] = ['product_id' => null, 'qty' => 1, 'harga_jual' => 0];
    }

    public function removeSaleItem($i)
    {
        array_splice($this->saleItems, $i, 1);
    }

    public function submitPurchase(StockBatchService $batchService, StockCardService $cardService)
    {
        $this->validate([
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
        ]);

        DB::transaction(function () use ($batchService, $cardService) {
            $purchase = Purchase::create([
                'no_invoice' => $this->no_invoice,
                'tanggal_pembelian' => $this->tanggal_pembelian,
                'supplier_id' => $this->supplier_id,
                'store_id' => $this->store_id,
                'warehouse_id' => $this->warehouse_id,
                'status' => $this->status,
                'keterangan' => $this->keterangan,
            ]);

            foreach ($this->purchaseItems as $it) {
                $purchaseItem = $purchase->purchaseItems()->create([
                    'product_id' => $it['product_id'],
                    'qty' => $it['qty'],
                    'unit_id' => $it['unit_id'],
                    'harga_beli' => $it['harga_beli'],
                    'total' => $it['harga_beli'] * $it['qty'],
                    'category_id' => $it['category_id'],
                    'subcategory_id' => $it['subcategory_id'],
                ]);

                // Handle batch creation if enabled
                if ($this->batch_enabled) {
                    $batchService->addStock(
                        $it['product_id'],
                        $this->store_id ? 'store' : 'warehouse',
                        $it['batch_name'] ?: ('PUR #'.$purchase->id.' - '.$purchaseItem->id),
                        $it['qty'],
                        $this->store_id ?: $this->warehouse_id,
                        'Pembelian via Transactions page'
                    );
                } else {
                    // Create stock adjustment for non-batch purchases
                    $adjustment = StockAdjustment::create([
                        'product_id' => $it['product_id'],
                        'type' => 'in',
                        'qty' => $it['qty'],
                        'cost' => $it['harga_beli'],
                        'store_id' => $this->store_id,
                        'warehouse_id' => $this->warehouse_id,
                        'note' => 'Pembelian via Transactions page - '.$purchase->no_invoice,
                        'reference_type' => 'purchase',
                        'reference_id' => $purchase->id,
                    ]);

                    // Create stock card for audit trail
                    $supplier = $this->suppliers->find($this->supplier_id);
                    $toLocation = $this->store_id
                        ? $this->stores->find($this->store_id)?->nama_toko ?? 'Toko'
                        : $this->warehouses->find($this->warehouse_id)?->nama_gudang ?? 'Gudang';

                    $cardService->createStockCard([
                        'product_id' => $it['product_id'],
                        'batch_id' => null,
                        'type' => 'in',
                        'qty' => $it['qty'],
                        'from_location' => $supplier?->nama_supplier ?? 'Supplier',
                        'to_location' => $toLocation,
                        'reference_type' => 'purchase',
                        'reference_id' => $purchase->id,
                        'note' => 'Pembelian via Transactions page - '.$purchase->no_invoice,
                    ]);
                }
            }
        });

        // Reset form
        $this->resetPurchaseForm();

        session()->flash('message', 'Pembelian berhasil disimpan');

        // Stay on the same page instead of redirecting
        $this->dispatch('purchase-saved');
    }

    private function resetPurchaseForm()
    {
        $this->no_invoice = 'PUR-'.now()->format('YmdHis');
        $this->tanggal_pembelian = now()->format('Y-m-d');
        $this->supplier_id = null;
        $this->store_id = null;
        $this->warehouse_id = null;
        $this->status = 'completed';
        $this->keterangan = '';
        $this->ownerFilter = null;
        $this->purchaseItems = [[
            'category_id' => null,
            'subcategory_id' => null,
            'product_id' => null,
            'qty' => 1,
            'unit_id' => null,
            'harga_beli' => 0,
            'use_batch' => false,
            'batch_name' => '',
            'batch_qty' => 0,
            'batch_location' => '',
            'batches' => [
                ['name' => '', 'qty' => 0],
            ],
        ]];
        $this->batch_enabled = false;

        // Ensure all purchase items have required batch keys
        $this->normalizePurchaseItems();
    }

    public function submitSale()
    {
        $this->validate([
            'saleItems.*.product_id' => 'required|exists:products,id',
            'saleItems.*.qty' => 'required|numeric|min:0.01',
            'saleItems.*.harga_jual' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () {
            $sale = Sale::create([
                'no_invoice' => 'SALE-'.now()->format('YmdHis'),
                'tanggal_penjualan' => now(),
                'customer_id' => null,
                'store_id' => $this->sale_location_type === 'store' ? $this->sale_location_id : null,
                'warehouse_id' => $this->sale_location_type === 'warehouse' ? $this->sale_location_id : null,
                'status' => 'completed',
            ]);

            foreach ($this->saleItems as $it) {
                $sale->saleItems()->create([
                    'product_id' => $it['product_id'],
                    'qty' => $it['qty'],
                    'unit_id' => Product::find($it['product_id'])?->unit_id,
                    'harga_jual' => $it['harga_jual'] ?? 0,
                ]);

                // Allocate FIFO from available batches
                $need = $it['qty'];
                $batches = StockBatch::where('product_id', $it['product_id'])->where('qty', '>', 0)->orderBy('id', 'asc')->lockForUpdate()->get();
                foreach ($batches as $batch) {
                    if ($need <= 0) {
                        break;
                    }
                    $take = min($batch->qty, $need);
                    $batch->decrement('qty', $take);

                    StockCard::create([
                        'product_id' => $it['product_id'],
                        'batch_id' => $batch->id,
                        'type' => 'out',
                        'qty' => $take,
                        'cost' => null,
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'note' => 'Penjualan via Transactions page',
                    ]);

                    $need -= $take;
                }

                if ($need > 0) {
                    throw new \Exception('Stok tidak mencukupi untuk produk ID '.$it['product_id']);
                }
            }
        });

        session()->flash('message', 'Penjualan berhasil disimpan');

        return redirect()->route('admin.transactions.manage');
    }

    // Methods for supplier/owner management
    public function ownerChanged($value)
    {
        $supplier = Supplier::where('nama_supplier', $value)->first();
        if ($supplier) {
            $this->supplier_id = $supplier->id;
        }
    }

    public function openOwnerModal()
    {
        $this->showOwnerModal = true;
        $this->new_owner_name = '';
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

        $supplier = Supplier::create([
            'kode_supplier' => 'SUP-'.now()->format('YmdHis'),
            'nama_supplier' => $this->new_owner_name,
        ]);

        $this->suppliers = Supplier::orderBy('nama_supplier')->get();
        $this->owners = Supplier::pluck('nama_supplier')->unique()->sort()->values();
        $this->supplier_id = $supplier->id;
        $this->ownerFilter = $supplier->nama_supplier;

        $this->closeOwnerModal();

        session()->flash('message', 'Supplier berhasil ditambahkan');
    }

    public function openSupplierModal()
    {
        $this->showSupplierModal = true;
        $this->kode_supplier = '';
        $this->nama_supplier = '';
        $this->telepon = '';
        $this->email = '';
        $this->alamat = '';
        $this->supplier_keterangan = '';
    }

    public function closeSupplierModal()
    {
        $this->showSupplierModal = false;
    }

    public function saveSupplier()
    {
        $this->validate([
            'kode_supplier' => 'required|string|max:50|unique:suppliers,kode_supplier',
            'nama_supplier' => 'required|string|max:255',
        ]);

        Supplier::create([
            'kode_supplier' => $this->kode_supplier,
            'nama_supplier' => $this->nama_supplier,
            'telepon' => $this->telepon,
            'email' => $this->email,
            'alamat' => $this->alamat,
            'keterangan' => $this->supplier_keterangan,
        ]);

        $this->suppliers = Supplier::orderBy('nama_supplier')->get();
        $this->owners = Supplier::pluck('nama_supplier')->unique()->sort()->values();

        $this->closeSupplierModal();

        session()->flash('message', 'Supplier berhasil ditambahkan');
    }

    public function selectLocation($value)
    {
        if (str_contains($value, 'store:')) {
            $this->store_id = str_replace('store:', '', $value);
            $this->warehouse_id = null;
        } elseif (str_contains($value, 'warehouse:')) {
            $this->warehouse_id = str_replace('warehouse:', '', $value);
            $this->store_id = null;
        } else {
            $this->store_id = null;
            $this->warehouse_id = null;
        }
    }

    public function addPurchaseItem()
    {
        $this->purchaseItems[] = [
            'category_id' => null,
            'subcategory_id' => null,
            'product_id' => null,
            'qty' => 1,
            'unit_id' => null,
            'harga_beli' => 0,
            'use_batch' => false,
            'batch_name' => '',
            'batch_qty' => 0,
            'batch_location' => '',
            'batches' => [
                ['name' => '', 'qty' => 0],
            ],
        ];

        // Ensure all purchase items have required batch keys
        $this->normalizePurchaseItems();
    }

    public function removePurchaseItem($i)
    {
        array_splice($this->purchaseItems, $i, 1);

        // Ensure all purchase items have required batch keys
        $this->normalizePurchaseItems();
    }

    public function toggleBatch($index)
    {
        $this->purchaseItems[$index]['use_batch'] = ! $this->purchaseItems[$index]['use_batch'];

        if (! $this->purchaseItems[$index]['use_batch']) {
            $this->purchaseItems[$index]['batches'] = [];
        }

        // Ensure all purchase items have required batch keys
        $this->normalizePurchaseItems();
    }

    public function addBatch($index)
    {
        $this->purchaseItems[$index]['batches'][] = [
            'batch_name' => '',
            'batch_qty' => 0,
            'batch_location' => '',
        ];

        // Ensure all purchase items have required batch keys
        $this->normalizePurchaseItems();
    }

    public function removeBatch($index, $batchIndex)
    {
        unset($this->purchaseItems[$index]['batches'][$batchIndex]);
        $this->purchaseItems[$index]['batches'] = array_values($this->purchaseItems[$index]['batches']);

        // Ensure all purchase items have required batch keys
        $this->normalizePurchaseItems();
    }

    public function toggleCreateBatchSection()
    {
        $this->showCreateBatchSection = ! $this->showCreateBatchSection;
    }

    public function createBatch()
    {
        if (empty($this->batchNameList) || empty($this->batchQtyList)) {
            return;
        }

        foreach ($this->purchaseItems as $index => $item) {
            if ($item['use_batch']) {
                $this->purchaseItems[$index]['batches'] = [];

                foreach ($this->batchNameList as $batchIndex => $batchName) {
                    if (! empty($batchName) && isset($this->batchQtyList[$batchIndex])) {
                        $this->purchaseItems[$index]['batches'][] = [
                            'batch_name' => $batchName,
                            'batch_qty' => $this->batchQtyList[$batchIndex],
                            'batch_location' => $this->batchLocationList[$batchIndex] ?? '',
                        ];
                    }
                }
            }
        }

        $this->showCreateBatchSection = false;
        $this->batchNameList = [];
        $this->batchQtyList = [];
        $this->batchLocationList = [];
    }

    public function addBatchCreatorRow()
    {
        $this->batchNameList[] = '';
        $this->batchQtyList[] = 0;
        $this->batchLocationList[] = '';
    }

    public function removeBatchCreatorRow($index)
    {
        unset($this->batchNameList[$index]);
        unset($this->batchQtyList[$index]);
        unset($this->batchLocationList[$index]);

        $this->batchNameList = array_values($this->batchNameList);
        $this->batchQtyList = array_values($this->batchQtyList);
        $this->batchLocationList = array_values($this->batchLocationList);
    }

    public function updatedPurchaseItems($value, $key)
    {
        $parts = explode('.', $key);
        $index = $parts[0];
        $field = $parts[1] ?? null;

        if ($field === 'category_id' && $value) {
            $this->purchaseItems[$index]['subcategory_id'] = null;
            $this->purchaseItems[$index]['product_id'] = null;
        }

        if ($field === 'subcategory_id' && $value) {
            $this->purchaseItems[$index]['product_id'] = null;
        }

        if ($field === 'product_id' && $value) {
            $product = Product::find($value);
            if ($product) {
                $this->purchaseItems[$index]['unit_id'] = $product->unit_id;
                $this->purchaseItems[$index]['category_id'] = $product->category_id;
                $this->purchaseItems[$index]['subcategory_id'] = $product->subcategory_id;
            }
        }

        // Ensure all purchase items have required batch keys
        $this->normalizePurchaseItems();
    }

    public function addBatchRow($itemIndex)
    {
        if (! isset($this->purchaseItems[$itemIndex]['batches'])) {
            $this->purchaseItems[$itemIndex]['batches'] = [];
        }

        $this->purchaseItems[$itemIndex]['batches'][] = ['name' => '', 'qty' => 0];
        $this->syncQtyFromBatches($itemIndex);
    }

    public function removeBatchRow($itemIndex, $batchIndex)
    {
        if (! isset($this->purchaseItems[$itemIndex]['batches'][$batchIndex])) {
            return;
        }

        unset($this->purchaseItems[$itemIndex]['batches'][$batchIndex]);
        $this->purchaseItems[$itemIndex]['batches'] = array_values($this->purchaseItems[$itemIndex]['batches']);
        $this->syncQtyFromBatches($itemIndex);
    }

    public function updateBatchField($itemIndex, $batchIndex, $field, $value)
    {
        if (! isset($this->purchaseItems[$itemIndex]['batches'][$batchIndex])) {
            return;
        }

        // Convert value to appropriate type
        if ($field === 'qty') {
            $value = is_numeric($value) ? (float) $value : 0;
        }

        $this->purchaseItems[$itemIndex]['batches'][$batchIndex][$field] = $value;
        $this->syncQtyFromBatches($itemIndex);
    }

    protected function syncQtyFromBatches($itemIndex)
    {
        if (! isset($this->purchaseItems[$itemIndex])) {
            return;
        }

        $batches = $this->purchaseItems[$itemIndex]['batches'] ?? [];
        $sum = 0;
        foreach ($batches as $batch) {
            // Ensure qty is numeric before adding
            $qty = isset($batch['qty']) ? (float) $batch['qty'] : 0;
            $sum += $qty;
        }

        $this->purchaseItems[$itemIndex]['qty'] = (float) $sum;
        $this->purchaseItems = array_merge([], $this->purchaseItems);
    }

    public function updatedBatchEnabled($value)
    {
        if ($value) {
            $this->syncAllBatchedQty();
        }
    }

    public function productSelected(int $index, string $name): void
    {
        $product = Product::where('nama_produk', $name)->orWhere('kode_produk', $name)->first();

        if (! $product) {
            // clear selection if not found
            $this->purchaseItems[$index]['product_id'] = null;
            $this->purchaseItems = array_merge([], $this->purchaseItems);

            return;
        }

        $this->purchaseItems[$index]['product_id'] = $product->id;
        $this->purchaseItems[$index]['unit_id'] = $product->unit_id ?? $this->purchaseItems[$index]['unit_id'] ?? null;
        $this->purchaseItems[$index]['category_id'] = $product->category_id ?? $this->purchaseItems[$index]['category_id'] ?? null;
        $this->purchaseItems[$index]['subcategory_id'] = $product->subcategory_id ?? $this->purchaseItems[$index]['subcategory_id'] ?? null;

        // If price available on product default, don't overwrite if already set
        if (property_exists($product, 'harga_jual') && empty($this->purchaseItems[$index]['harga_beli'])) {
            $this->purchaseItems[$index]['harga_beli'] = $product->harga_jual ?? $this->purchaseItems[$index]['harga_beli'] ?? 0;
        }

        $this->purchaseItems = array_merge([], $this->purchaseItems);
    }

    public function searchProducts(int $index, string $q): void
    {
        $q = trim($q);
        if ($q === '') {
            unset($this->productSuggestions[$index]);

            return;
        }

        $results = Product::where('nama_produk', 'like', "%{$q}%")
            ->orWhere('kode_produk', 'like', "%{$q}%")
            ->orderBy('nama_produk')
            ->limit(12)
            ->get(['id', 'nama_produk'])
            ->toArray();

        // Debug log to verify Livewire requests reach the server
        try {
            Log::debug('Transactions::searchProducts', ['index' => $index, 'q' => $q, 'count' => count($results)]);
        } catch (\Throwable $e) {
            // avoid breaking the request if logging fails for any reason
        }

        $this->productSuggestions[$index] = $results;
    }

    public function selectSuggestedProduct(int $index, int $productId): void
    {
        $product = Product::find($productId);
        if (! $product) {
            return;
        }

        try {
            Log::debug('Transactions::selectSuggestedProduct', ['index' => $index, 'product_id' => $productId, 'name' => $product->nama_produk]);
        } catch (\Throwable $e) {
        }

        $this->purchaseItems[$index]['product_id'] = $product->id;
        $this->purchaseItems[$index]['unit_id'] = $product->unit_id ?? $this->purchaseItems[$index]['unit_id'] ?? null;
        $this->purchaseItems[$index]['category_id'] = $product->category_id ?? $this->purchaseItems[$index]['category_id'] ?? null;
        $this->purchaseItems[$index]['subcategory_id'] = $product->subcategory_id ?? $this->purchaseItems[$index]['subcategory_id'] ?? null;
        $this->purchaseItems[$index]['product_search'] = $product->nama_produk;

        if (property_exists($product, 'harga_jual') && empty($this->purchaseItems[$index]['harga_beli'])) {
            $this->purchaseItems[$index]['harga_beli'] = $product->harga_jual ?? $this->purchaseItems[$index]['harga_beli'] ?? 0;
        }

        unset($this->productSuggestions[$index]);
        $this->purchaseItems = array_merge([], $this->purchaseItems);
    }

    protected function syncAllBatchedQty()
    {
        if (! $this->batch_enabled || empty($this->purchaseItems)) {
            return;
        }

        foreach ($this->purchaseItems as $idx => $item) {
            $this->syncQtyFromBatches($idx);
        }
    }

    // Livewire hooks for recent filters (help ensure component re-renders predictably)
    public function updatedRecentSearch($value)
    {
        // intentionally left blank; placeholder for future logic
    }

    public function updatedRecentLocation($value)
    {
        // intentionally left blank; placeholder for future logic
    }

    // Explicit setters called from the view when wire:model isn't updating reliably
    public function setRecentSearch(string $value): void
    {
        $this->recent_search = $value;
    }

    public function setRecentLocation(string $value): void
    {
        $this->recent_location = $value;
    }

    #[Computed]
    public function purchaseTotal()
    {
        $total = 0;
        foreach ($this->purchaseItems as $item) {
            $qty = $item['qty'] ?? 0;
            $harga = $item['harga_beli'] ?? 0;
            $total += $qty * $harga;
        }

        return $total;
    }

    public function render()
    {
        // Recent raw activity (limited) - eager load batch for location display
        $recent = StockCard::latestFirst()->with(['product', 'batch'])->limit(25)->get();

        // Grouped batches per product for display in realtime section
        // only eager-load product; do not eager-load polymorphic `location` (DB stores non-class values)
        $batchesQuery = StockBatch::query()->where('qty', '>', 0)->with('product');

        if (! empty($this->recent_search)) {
            $batchesQuery->whereHas('product', function ($q) {
                $q->where('nama_produk', 'like', "%{$this->recent_search}%");
            });
        }

        if (! empty($this->recent_location)) {
            if (str_starts_with($this->recent_location, 'store:')) {
                $batchesQuery->where('location_type', 'store')->where('location_id', (int) str_replace('store:', '', $this->recent_location));
            } elseif (str_starts_with($this->recent_location, 'warehouse:')) {
                $batchesQuery->where('location_type', 'warehouse')->where('location_id', (int) str_replace('warehouse:', '', $this->recent_location));
            }
        }

        $groupedBatches = $batchesQuery->get()->groupBy(function ($b) {
            return $b->product?->nama_produk ?? 'Produk tidak ditemukan';
        })->map(function ($items) {
            return $items->map(function ($b) {
                // Resolve location name using preloaded stores/warehouses collections to avoid morph resolution
                $locationName = null;
                if (! empty($b->location_type) && ! empty($b->location_id)) {
                    if ($b->location_type === 'store') {
                        $store = $this->stores->firstWhere('id', $b->location_id);
                        $locationName = $store?->nama_toko;
                    } elseif ($b->location_type === 'warehouse') {
                        $wh = $this->warehouses->firstWhere('id', $b->location_id);
                        $locationName = $wh?->nama_gudang;
                    }
                }

                if (! $locationName) {
                    $locationName = ($b->location_type ? ucfirst($b->location_type) : 'Lokasi').' '.($b->location_id ?? '');
                }

                return [
                    'name' => $b->nama_tumpukan ?: $b->id,
                    'qty' => $b->qty,
                    'location' => $locationName,
                    'location_type' => $b->location_type,
                    'location_id' => $b->location_id,
                ];
            })->values();
        });

        return view('livewire.admin.transactions.transactions', [
            'recent' => $recent,
            'groupedBatches' => $groupedBatches,
        ]);
    }
}
