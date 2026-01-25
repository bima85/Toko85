<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockAdjustment;
use App\Models\StockBatch;
use App\Models\Store;
use App\Models\Subcategory;
use App\Models\Unit;
use App\Services\StockCardService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Sales extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public $no_invoice;

    public $tanggal_penjualan;

    public $tanggal;  // Day (01-31)

    public $bulan;    // Month (01-12)

    public $tahun;    // Year (YYYY)

    public $customer_id;

    public $store_id;

    public $warehouse_id;

    public $location_source = 'toko'; // 'toko' or 'gudang'

    public $status = 'completed';

    public $keterangan;

    public $editingSaleId = null;

    public $showCreateForm = false;

    // When this component is embedded elsewhere we may want to hide the full page header
    public $hideHeader = false;

    // Modal to create customer inline
    public $showCreateCustomerModal = false;

    public $new_customer_nama = '';

    public $new_customer_telepon = '';

    public $new_customer_email = '';

    public $new_customer_alamat = '';

    // Sale items
    public $saleItems = [];

    // Kuli (biaya angkut)
    public $kuli = 0;

    // Grand total
    public $grandTotal = 0;

    // Delivery note (Surat Jalan) properties
    public $showDeliveryNoteModal = false;

    public $deliveryNoteNumber;

    public $deliveryDate;

    public $deliveryNotes;

    public $deliveryApproved = false;

    // Stock warning
    public $showStockWarning = false;

    public $stockWarningMessage = '';

    public $useWarehouseStock = false;

    protected $rules = [
        'no_invoice' => 'nullable|string|max:50|unique:sales,no_invoice',
        'tanggal_penjualan' => 'required|date',
        'customer_id' => 'required|exists:customers,id',
        'store_id' => 'nullable|exists:stores,id',
        'warehouse_id' => 'nullable|exists:warehouses,id',
        'status' => 'required|in:pending,completed,cancelled,hold',
        'keterangan' => 'nullable|string',
        'saleItems' => 'required|array|min:1',
        'saleItems.*.category_id' => 'required|exists:categories,id',
        'saleItems.*.subcategory_id' => 'nullable|exists:subcategories,id',
        'saleItems.*.product_id' => 'required|exists:products,id',
        'saleItems.*.batch_id' => 'required|exists:stock_batches,id',
        'saleItems.*.qty' => 'required|integer|min:1',
        'saleItems.*.unit_id' => 'required|exists:units,id',
        'saleItems.*.harga_jual' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        abort_unless($user && (
            (method_exists($user, 'hasPermissionTo') && (
                $user->hasPermissionTo('sales.view') || $user->hasPermissionTo('transactions.manage')
            ))
            || (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'superadmin']))
        ), 403);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        abort_unless($user && (
            (method_exists($user, 'hasPermissionTo') && (
                $user->hasPermissionTo('sales.create') || $user->hasPermissionTo('transactions.manage')
            ))
            || (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'superadmin']))
        ), 403);

        // DEFAULT: Penjualan dari TOKO (bukan gudang)
        $firstStore = Store::orderBy('nama_toko')->first();
        $this->store_id = $firstStore ? $firstStore->id : null;
        $this->warehouse_id = null; // Tidak pakai gudang by default

        // Set tanggal
        $today = now();
        $this->tanggal = str_pad($today->day, 2, '0', STR_PAD_LEFT);
        $this->bulan = str_pad($today->month, 2, '0', STR_PAD_LEFT);
        $this->tahun = $today->year;
        $this->tanggal_penjualan = $today->format('d-m-Y');
        $this->deliveryDate = $today->format('d-m-Y');

        // Invoice will be auto-generated when customer is selected
        // or before save if still empty

        // Generate nomor surat jalan
        $this->generateDeliveryNoteNumber();

        $this->editingSaleId = null;
        $this->showCreateForm = true;
        $this->deliveryApproved = false;

        // Reset inline customer form values
        $this->showCreateCustomerModal = false;
        $this->new_customer_nama = '';
        $this->new_customer_telepon = '';
        $this->new_customer_email = '';
        $this->new_customer_alamat = '';
    }

    public function openCreateCustomerModal()
    {
        $this->showCreateCustomerModal = true;
    }

    public function edit($id)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        abort_unless($user && (
            (method_exists($user, 'hasPermissionTo') && (
                $user->hasPermissionTo('sales.update') || $user->hasPermissionTo('transactions.manage')
            ))
            || (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'superadmin']))
        ), 403);

        $sale = Sale::with('saleItems')->findOrFail($id);
        $this->editingSaleId = $sale->id;
        $this->no_invoice = $sale->no_invoice;
        $this->tanggal_penjualan = $sale->tanggal_penjualan->format('d-m-Y');
        // Split date into tanggal, bulan, tahun
        $this->tanggal = str_pad($sale->tanggal_penjualan->day, 2, '0', STR_PAD_LEFT);
        $this->bulan = str_pad($sale->tanggal_penjualan->month, 2, '0', STR_PAD_LEFT);
        $this->tahun = $sale->tanggal_penjualan->year;
        $this->customer_id = $sale->customer_id;
        $this->store_id = $sale->store_id;
        $this->warehouse_id = $sale->warehouse_id;
        $this->status = $sale->status;
        $this->keterangan = $sale->keterangan;
        $this->saleItems = $sale->saleItems->map(function ($item) {
            $qty = $item->qty ?? 0;
            $harga = $item->harga_jual ?? 0;
            $total = $qty * $harga;

            // Get batch info if batch_id exists
            $batch = $item->batch_id ? StockBatch::find($item->batch_id) : null;
            $batch_name = $batch ? $batch->nama_tumpukan : null;

            return [
                'id' => $item->id,
                'category_id' => $item->product->subcategory->category_id ?? null,
                'subcategory_id' => $item->product->subcategory_id ?? null,
                'product_id' => $item->product_id,
                'qty' => $qty,
                'unit_id' => $item->unit_id,
                'harga_jual' => $harga,
                'total' => $total,
                'batch_id' => $item->batch_id,
                'batch_name' => $batch_name,
            ];
        })->toArray();

        $this->showCreateForm = true;
    }

    public function selectLocationSource($value)
    {
        /**
         * Listener ketika user mengubah location_source
         * Jika memilih 'toko', set warehouse_id null dan pastikan store_id terisi
         * Jika memilih 'gudang', set store_id null dan set warehouse_id ke gudang pertama
         */
        if ($value === 'toko') {
            // Switch ke TOKO
            $this->location_source = 'toko';
            $this->warehouse_id = null;
            // Auto-set store pertama jika belum ada
            if (! $this->store_id) {
                $firstStore = Store::orderBy('nama_toko')->first();
                $this->store_id = $firstStore ? $firstStore->id : null;
            }
        } elseif ($value === 'gudang') {
            // Switch ke GUDANG
            $this->location_source = 'gudang';
            $this->store_id = null;
            // Auto-set gudang pertama jika belum ada
            if (! $this->warehouse_id) {
                $firstWarehouse = \App\Models\Warehouse::orderBy('nama_gudang')->first();
                $this->warehouse_id = $firstWarehouse ? $firstWarehouse->id : null;
            }
        }
    }

    public function updatedLocationSource($value)
    {
        // Lifecycle hook - akan dipanggil otomatis oleh Livewire 3
        // Tinggal forward ke selectLocationSource method
        $this->selectLocationSource($value);
    }

    public function addItem()
    {
        $this->saleItems[] = [
            'category_id' => null,
            'subcategory_id' => null,
            'product_id' => null,
            'product_name' => '',
            'qty' => null,
            'unit_id' => null,
            'harga_jual' => null,
            'total' => 0,
            'batch_id' => null,
            'batch_name' => null,
            'batch_warning' => null,
            'location_source' => null,
            'product_suggestions' => [],
            'available_batches' => [],
        ];
    }

    public function removeItem($index)
    {
        unset($this->saleItems[$index]);
        $this->saleItems = array_values($this->saleItems);
    }

    public function selectProduct($index, $productId)
    {
        /**
         * Handle product selection dari product suggestions
         */
        if (isset($this->saleItems[$index])) {
            $product = Product::find($productId);
            if ($product) {
                $this->saleItems[$index]['product_id'] = $product->id;
                $this->saleItems[$index]['product_name'] = $product->nama_produk;
                $this->saleItems[$index]['product_suggestions'] = [];
                $this->populateBatchForItem($index);
            }
        }
    }

    /**
     * Get available batches untuk item tertentu berdasarkan product_id dan location
     * Dipanggil dari template untuk populate dropdown options
     */
    public function getAvailableBatches($index)
    {
        $productId = $this->saleItems[$index]['product_id'] ?? null;

        if (! $productId) {
            return collect([]);
        }

        $query = StockBatch::where('product_id', $productId)
            ->where('qty', '>', 0)
            ->where('status', 'aktual') // Only show aktual batches, not hold
            ->orderBy('created_at', 'asc');

        // Determine effective source: per-row override if set, otherwise global
        $rowSource = $this->saleItems[$index]['location_source'] ?? null;
        $effectiveSource = $rowSource ?: $this->location_source;

        if ($effectiveSource === 'toko') {
            // Restrict to store batches; if specific store selected, filter by it
            $query->where('location_type', 'store');
            if ($this->store_id) {
                $query->where('location_id', $this->store_id);
            }
        } elseif ($effectiveSource === 'gudang') {
            // Restrict to warehouse batches; if specific warehouse selected, filter by it
            $query->where('location_type', 'warehouse');
            if ($this->warehouse_id) {
                $query->where('location_id', $this->warehouse_id);
            }
        }

        $batches = $query->get();

        \Log::info("getAvailableBatches: product=$productId, source={$this->location_source}, store={$this->store_id}, warehouse={$this->warehouse_id}, count={$batches->count()}");

        return $batches;
    }

    /**
     * Return products filtered by selected category/subcategory for a given row index
     */
    public function getProductsForRow($index)
    {
        $categoryId = $this->saleItems[$index]['category_id'] ?? null;
        $subcategoryId = $this->saleItems[$index]['subcategory_id'] ?? null;

        $query = Product::query();

        // Filter by category/subcategory
        if ($subcategoryId) {
            $query->where('subcategory_id', $subcategoryId);
        } elseif ($categoryId) {
            $query->whereHas('subcategory', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // Determine effective source per-row
        $rowSource = $this->saleItems[$index]['location_source'] ?? null;
        $effectiveSource = $rowSource ?: $this->location_source;

        // Filter by stock location only if store/warehouse is selected
        if ($effectiveSource === 'toko' && $this->store_id) {
            $query->whereHas('stockBatches', function ($q) {
                $q->where('location_type', 'store')
                    ->where('location_id', $this->store_id)
                    ->where('qty', '>', 0)
                    ->where('status', 'aktual');
            });
        } elseif ($effectiveSource === 'gudang' && $this->warehouse_id) {
            $query->whereHas('stockBatches', function ($q) {
                $q->where('location_type', 'warehouse')
                    ->where('location_id', $this->warehouse_id)
                    ->where('qty', '>', 0)
                    ->where('status', 'aktual');
            });
        } else {
            // If no location selected, show all products (or only show products with stock in ANY location)
            // This allows user to search while deciding on location
            $query->whereHas('stockBatches', function ($q) {
                $q->where('qty', '>', 0)
                    ->where('status', 'aktual');
            });
        }

        return $query->orderBy('nama_produk')->get();
    }

    #[Computed]
    public function availableProducts()
    {
        /**
         * Filter products berdasarkan location_source
         * - Jika toko: tampilkan produk yang memiliki stok di toko terpilih
         * - Jika gudang: tampilkan produk yang memiliki stok di gudang terpilih
         */
        if ($this->location_source === 'toko' && $this->store_id) {
            return Product::whereHas('stockBatches', function ($query) {
                $query->where('location_type', 'store')
                    ->where('location_id', $this->store_id)
                    ->where('qty', '>', 0)
                    ->where('status', 'aktual');
            })->get();
        } elseif ($this->location_source === 'gudang' && $this->warehouse_id) {
            return Product::whereHas('stockBatches', function ($query) {
                $query->where('location_type', 'warehouse')
                    ->where('location_id', $this->warehouse_id)
                    ->where('qty', '>', 0)
                    ->where('status', 'aktual');
            })->get();
        }

        return collect([]);
    }

    #[Computed]
    public function availableBatchesByItem()
    {
        $result = [];
        foreach ($this->saleItems as $index => $item) {
            $result[$index] = $this->getAvailableBatches($index);
        }

        return $result;
    }

    #[Computed]
    public function grandTotal()
    {
        $itemsTotal = 0;
        foreach ($this->saleItems as $item) {
            $itemsTotal += (float) ($item['total'] ?? 0);
        }

        return $itemsTotal + $this->kuli;
    }

    public function updateTotal($index)
    {
        if (isset($this->saleItems[$index])) {
            $qty = (float) ($this->saleItems[$index]['qty'] ?? 0);
            $harga = (float) ($this->saleItems[$index]['harga_jual'] ?? 0);

            // Get unit conversion value
            $unitId = $this->saleItems[$index]['unit_id'] ?? null;
            $unitValue = 1; // default

            if ($unitId) {
                $unit = Unit::find($unitId);
                if ($unit && $unit->conversion_value) {
                    $unitValue = (float) $unit->conversion_value;
                }
            }

            // Formula: Qty × Unit (conversion_value) × Harga Jual = Total
            $this->saleItems[$index]['total'] = $qty * $unitValue * $harga;

            // Update grand total
            $this->updateGrandTotal();

            // Check batch availability
            $this->checkBatchAvailability($index);
        }
    }

    private function updateGrandTotal()
    {
        $itemsTotal = 0;
        foreach ($this->saleItems as $item) {
            $itemsTotal += (float) ($item['total'] ?? 0);
        }
        $this->grandTotal = $itemsTotal + $this->kuli;
    }

    public function updatedKuli()
    {
        $this->updateGrandTotal();
    }

    private function checkBatchAvailability($index)
    {
        /**
         * Check apakah batch qty cukup untuk requested qty
         * Store warning/info di saleItems
         */
        if (! isset($this->saleItems[$index])) {
            return;
        }

        $batchId = $this->saleItems[$index]['batch_id'] ?? null;
        $requestedQty = $this->saleItems[$index]['qty'] ?? 0;

        if (! $batchId || ! $requestedQty) {
            $this->saleItems[$index]['batch_warning'] = null;

            return;
        }

        $batch = StockBatch::find($batchId);
        if (! $batch) {
            $this->saleItems[$index]['batch_warning'] = 'Batch tidak ditemukan';

            return;
        }

        // Set warning if qty exceeds batch availability
        if ($requestedQty > $batch->qty) {
            $this->saleItems[$index]['batch_warning'] =
                "Batch {$batch->nama_tumpukan} hanya tersedia {$batch->qty} sak";
        } else {
            $this->saleItems[$index]['batch_warning'] = null;
        }
    }

    public function updatedSaleItems($value, $key)
    {
        /**
         * Listen untuk perubahan apapun di saleItems array
         * Jika category/subcategory berubah, refresh product suggestions
         * Jika product_name berubah, filter dan tampilkan product suggestions sesuai category/subcategory
         * Jika product_id berubah, auto-populate batch info
         * Jika batch_id berubah, update batch_name
         */
        \Log::info("updatedSaleItems TRIGGERED: key='$key', value='" . (is_array($value) ? json_encode($value) : $value) . "'");

        // Handle category change
        if (strpos($key, 'category_id') !== false) {
            preg_match('/(\d+)/', $key, $matches);
            if (isset($matches[1])) {
                $index = (int) $matches[1];
                \Log::info("Category changed at index $index");
                // Reset subcategory and product when category changes
                $this->saleItems[$index]['subcategory_id'] = null;
                $this->saleItems[$index]['product_id'] = null;
                $this->saleItems[$index]['product_name'] = '';
                $this->saleItems[$index]['product_suggestions'] = [];
            }
        }
        // Handle subcategory change
        elseif (strpos($key, 'subcategory_id') !== false) {
            preg_match('/(\d+)/', $key, $matches);
            if (isset($matches[1])) {
                $index = (int) $matches[1];
                \Log::info("Subcategory changed at index $index");
                // Reset product when subcategory changes
                $this->saleItems[$index]['product_id'] = null;
                $this->saleItems[$index]['product_name'] = '';
                $this->saleItems[$index]['product_suggestions'] = [];
            }
        }
        // Handle product name search - show suggestions filtered by category/subcategory
        elseif (strpos($key, 'product_name') !== false) {
            preg_match('/(\d+)/', $key, $matches);
            if (isset($matches[1])) {
                $index = (int) $matches[1];
                $searchValue = trim($value);
                \Log::info("Product name changed at index $index: '$searchValue' (len=" . strlen($searchValue) . ")");

                if (strlen($searchValue) >= 2) {
                    // Get products for this row (filtered by category/subcategory and location)
                    $candidates = $this->getProductsForRow($index);
                    \Log::info("Candidates count: " . $candidates->count());

                    // Filter by search term
                    $suggestions = $candidates->filter(function ($product) use ($searchValue) {
                        return stripos($product->nama_produk, $searchValue) !== false ||
                            stripos($product->kode_produk, $searchValue) !== false;
                    })->take(10)->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'nama_produk' => $product->nama_produk,
                            'kode_produk' => $product->kode_produk,
                        ];
                    })->values()->toArray();

                    \Log::info("Filtered suggestions count: " . count($suggestions));
                    $this->saleItems[$index]['product_suggestions'] = $suggestions;
                } else {
                    $this->saleItems[$index]['product_suggestions'] = [];
                }
            }
        }
        // Handle product selection from suggestions
        elseif (strpos($key, 'product_id') !== false) {
            preg_match('/(\d+)/', $key, $matches);
            if (isset($matches[1])) {
                $index = (int) $matches[1];
                \Log::info("Product changed at index $index to $value");
                $this->saleItems[$index]['product_suggestions'] = [];
                $this->populateBatchForItem($index);
            }
        }
        // Handle batch selection
        elseif (strpos($key, 'batch_id') !== false) {
            preg_match('/(\d+)/', $key, $matches);
            if (isset($matches[1])) {
                $index = (int) $matches[1];
                $batchId = $this->saleItems[$index]['batch_id'] ?? null;
                if ($batchId) {
                    $batch = StockBatch::find($batchId);
                    $this->saleItems[$index]['batch_name'] = $batch ? $batch->nama_tumpukan : null;
                    $this->checkBatchAvailability($index);
                } else {
                    $this->saleItems[$index]['batch_name'] = null;
                }
            }
        }
        // Handle quantity change
        elseif (strpos($key, 'quantity') !== false) {
            preg_match('/(\d+)/', $key, $matches);
            if (isset($matches[1])) {
                $index = (int) $matches[1];
                $this->checkBatchAvailability($index);
            }
        }
        // Handle location source change
        elseif (strpos($key, 'location_source') !== false) {
            preg_match('/(\d+)/', $key, $matches);
            if (isset($matches[1])) {
                $index = (int) $matches[1];
                $this->populateBatchForItem($index);
                $this->updateTotal($index);
            }
        }
    }

    private function populateBatchForItem($index)
    {
        /**
         * Auto-select batch untuk item pada index tertentu
         * Menggunakan FIFO: ambil batch tertua dengan stok tersedia
         */
        if (! isset($this->saleItems[$index])) {
            return;
        }

        $productId = $this->saleItems[$index]['product_id'] ?? null;
        if (! $productId) {
            $this->saleItems[$index]['batch_id'] = null;
            $this->saleItems[$index]['batch_name'] = null;

            return;
        }

        // Get location dari per-row override atau global
        $rowSource = $this->saleItems[$index]['location_source'] ?? null;
        $effectiveSource = $rowSource ?: $this->location_source;

        $storeId = null;
        $warehouseId = null;
        if ($effectiveSource === 'toko') {
            $storeId = $this->store_id ?? null;
        } elseif ($effectiveSource === 'gudang') {
            $warehouseId = $this->warehouse_id ?? null;
        }

        // Query batch yang tersedia (FIFO - oldest first)
        $query = StockBatch::where('product_id', $productId)
            ->where('qty', '>', 0)
            ->orderBy('created_at', 'asc');

        // If a specific location id is set, filter by it. Otherwise restrict by location_type
        if ($storeId) {
            $query->where('location_type', 'store')
                ->where('location_id', $storeId);
        } elseif ($warehouseId) {
            $query->where('location_type', 'warehouse')
                ->where('location_id', $warehouseId);
        } else {
            if ($effectiveSource === 'toko') {
                $query->where('location_type', 'store');
            } elseif ($effectiveSource === 'gudang') {
                $query->where('location_type', 'warehouse');
            }
        }

        $batch = $query->first();

        if ($batch) {
            $this->saleItems[$index]['batch_id'] = $batch->id;
            $this->saleItems[$index]['batch_name'] = $batch->nama_tumpukan ?? "Batch #{$batch->id}";
        } else {
            $this->saleItems[$index]['batch_id'] = null;
            $this->saleItems[$index]['batch_name'] = 'Stok tidak tersedia';
        }
    }

    public function generateInvoiceNumber()
    {
        \Log::info('=== generateInvoiceNumber called ===', [
            'customer_id' => $this->customer_id,
            'current_no_invoice' => $this->no_invoice,
        ]);
        // Use selected sale date if provided, otherwise today
        $dateSource = $this->tanggal_penjualan ?: date('Y-m-d');

        // Parse date to Carbon
        try {
            $carbonDate = Carbon::parse($dateSource);
        } catch (\Exception $e) {
            // Fallback to today if parsing fails
            $carbonDate = Carbon::today();
        }

        // Format tanggal untuk invoice: d-m-Y (contoh: 24-01-2026)
        $dateFormatted = $carbonDate->format('d-m-Y');
        $dateCompact = $carbonDate->format('dmy'); // For sorting: 240126

        if (! $this->customer_id) {
            // Generate default invoice without customer code
            $prefix = 'INV/PJ/' . $dateFormatted;

            $lastSale = Sale::where('no_invoice', 'LIKE', $prefix . '/%')
                ->orderByRaw("CAST(SUBSTRING_INDEX(no_invoice, '/', -1) AS UNSIGNED) DESC")
                ->first();

            $sequence = 1;
            if ($lastSale) {
                $parts = explode('/', $lastSale->no_invoice);
                if (count($parts) >= 4) {
                    $sequence = (int) $parts[3] + 1;
                }
            }

            $this->no_invoice = sprintf('INV/PJ/%s/%03d', $dateFormatted, $sequence);

            \Log::info('Generated invoice (no customer):', ['no_invoice' => $this->no_invoice]);

            return;
        }

        $customer = Customer::find($this->customer_id);
        if (! $customer) {
            $this->no_invoice = '';
            \Log::warning('Customer not found, no_invoice set to empty');

            return;
        }

        // Determine customer code: prefer kode_pelanggan, fallback to uppercase name without spaces
        $customerCode = $customer->kode_pelanggan ?: preg_replace('/\s+/', '', strtoupper($customer->nama_pelanggan ?? ''));

        // Format: [CUSTOMER_CODE]/PJ/[d-m-Y]/[SEQUENCE]
        $prefix = $customerCode . '/PJ/' . $dateFormatted;

        // Cari invoice terakhir dengan pattern yang sama (by invoice prefix)
        $lastInvoice = Sale::where('no_invoice', 'LIKE', $prefix . '/%')
            ->orderByRaw("CAST(SUBSTRING_INDEX(no_invoice, '/', -1) AS UNSIGNED) DESC")
            ->first();

        $sequence = 1;
        if ($lastInvoice) {
            $parts = explode('/', $lastInvoice->no_invoice);
            if (count($parts) >= 4) {
                $sequence = (int) $parts[3] + 1;
            }
        }

        $this->no_invoice = sprintf('%s/PJ/%s/%03d', $customerCode, $dateFormatted, $sequence);

        \Log::info('Generated invoice (with customer):', [
            'customer_code' => $customerCode,
            'no_invoice' => $this->no_invoice,
        ]);
    }

    public function updatedTanggalPenjualan()
    {
        \Log::info('updatedTanggalPenjualan fired', ['tanggal_penjualan' => $this->tanggal_penjualan]);
        // Regenerate invoice number when user changes the sale date
        $this->generateInvoiceNumber();
    }

    public function updatedTanggal()
    {
        $this->combineTanggal();
    }

    public function updatedBulan()
    {
        $this->combineTanggal();
    }

    public function updatedTahun()
    {
        $this->combineTanggal();
    }

    private function combineTanggal()
    {
        /**
         * Combine tanggal, bulan, tahun into tanggal_penjualan (d-m-Y format)
         */
        if ($this->tanggal && $this->bulan && $this->tahun) {
            $this->tanggal_penjualan = "{$this->tahun}-{$this->bulan}-{$this->tanggal}";
            $this->generateInvoiceNumber();
        }
    }

    public function generateDeliveryNoteNumber()
    {
        // Format: SJ/[YYYYMMDD]/[SEQUENCE]
        // Contoh: SJ/20251107/001
        $today = date('Ymd');

        // Cari jumlah surat jalan hari ini
        $lastSale = Sale::whereDate('tanggal_penjualan', date('d-m-Y'))
            ->latest('id')
            ->first();

        $sequence = 1;
        if ($lastSale && ! empty($lastSale->delivery_note_number)) {
            // Extract sequence dari last delivery note
            $parts = explode('/', $lastSale->delivery_note_number);
            if (count($parts) >= 3) {
                $sequence = (int) $parts[2] + 1;
            }
        }

        $this->deliveryNoteNumber = sprintf('SJ/%s/%03d', $today, $sequence);
    }

    public function checkStockAvailability()
    {
        /**
         * Cek stok di toko, jika tidak cukup tampilkan warning
         * dan tanya apakah ingin ambil dari gudang
         */
        $insufficientItems = [];

        foreach ($this->saleItems as $index => $item) {
            if (empty($item['product_id']) || empty($item['qty'])) {
                continue;
            }

            // Cek stok di toko
            $storeStock = StockBatch::where('product_id', $item['product_id'])
                ->where('location_type', 'store')
                ->where('location_id', $this->store_id)
                ->sum('qty');

            if ($storeStock < $item['qty']) {
                // Cek stok di gudang
                $warehouseStock = StockBatch::where('product_id', $item['product_id'])
                    ->where('location_type', 'warehouse')
                    ->sum('qty');

                $product = Product::find($item['product_id']);
                $insufficientItems[] = [
                    'product_name' => $product->nama_produk,
                    'requested' => $item['qty'],
                    'store_stock' => $storeStock,
                    'warehouse_stock' => $warehouseStock,
                ];
            }
        }

        if (! empty($insufficientItems)) {
            $this->showStockWarning = true;
            $message = "Stok di toko tidak mencukupi:\n";
            foreach ($insufficientItems as $item) {
                $message .= "- {$item['product_name']}: Diminta {$item['requested']}, Stok Toko {$item['store_stock']}, Stok Gudang {$item['warehouse_stock']}\n";
            }
            $this->stockWarningMessage = $message;

            return false;
        }

        return true;
    }

    public function proceedWithWarehouse()
    {
        /**
         * User memilih untuk ambil stok dari gudang
         * PENTING: Kosongkan store_id agar hanya warehouse yang digunakan
         */
        $this->useWarehouseStock = true;
        $this->showStockWarning = false;

        // SWITCH dari TOKO ke GUDANG
        $this->store_id = null;  // Kosongkan toko

        // Pilih warehouse pertama
        $firstWarehouse = \App\Models\Warehouse::first();
        $this->warehouse_id = $firstWarehouse ? $firstWarehouse->id : null;

        // Refresh batch items untuk ambil dari warehouse
        foreach (array_keys($this->saleItems) as $index) {
            $this->populateBatchForItem($index);
        }

        // Lanjut ke surat jalan
        $this->showDeliveryNote();
    }

    public function cancelStockWarning()
    {
        $this->showStockWarning = false;
        $this->useWarehouseStock = false;
    }

    public function showDeliveryNote()
    {
        // Validasi form dulu sebelum tampilkan surat jalan
        try {
            $this->validate();
            $this->showDeliveryNoteModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Lengkapi form terlebih dahulu: ' . $e->getMessage());
        }
    }

    public function approveDeliveryNote()
    {
        $this->deliveryApproved = true;
        $this->showDeliveryNoteModal = false;

        // Setelah approve, langsung save
        $this->save();
    }

    public function cancelDeliveryNote()
    {
        $this->showDeliveryNoteModal = false;
    }

    public function updatedCustomerId()
    {
        $this->generateInvoiceNumber();
    }

    public function refreshBatchesForStoreChange(): void
    {
        /**
         * Ketika store berubah, refresh batch info semua items
         * karena batch yang tersedia bisa berbeda per location
         */
        foreach (array_keys($this->saleItems) as $index) {
            $this->populateBatchForItem($index);
        }
    }

    public function refreshBatchesForWarehouseChange(): void
    {
        /**
         * Ketika warehouse berubah, refresh batch info semua items
         */
        foreach (array_keys($this->saleItems) as $index) {
            $this->populateBatchForItem($index);
        }
    }

    public function updatedStoreId(): void
    {
        $this->refreshBatchesForStoreChange();
    }

    public function updatedWarehouseId(): void
    {
        $this->refreshBatchesForWarehouseChange();
    }

    private function reduceStockFromBatch($productId, $qty, $storeId = null, $warehouseId = null)
    {
        /**
         * Reduce stock dari batch dengan logic:
         * StockBatch menggunakan location_type + location_id
         * - Jika store_id: location_type = 'store', location_id = store_id
         * - Jika warehouse_id: location_type = 'warehouse', location_id = warehouse_id
         * - Gunakan FIFO (First In First Out) - ambil dari batch tertua dulu
         * - UPDATE StockCard untuk laporan stok
         */
        $query = StockBatch::where('product_id', $productId)
            ->where('qty', '>', 0)
            ->orderBy('created_at', 'asc');

        // Filter berdasarkan location (store atau warehouse)
        if ($storeId) {
            $query->where('location_type', 'store')
                ->where('location_id', $storeId);
        } elseif ($warehouseId) {
            $query->where('location_type', 'warehouse')
                ->where('location_id', $warehouseId);
        }

        $batches = $query->get();
        $remainingQty = $qty;

        foreach ($batches as $batch) {
            if ($remainingQty <= 0) {
                break;
            }

            $qtyToReduce = min($remainingQty, $batch->qty);
            $batch->update([
                'qty' => $batch->qty - $qtyToReduce,
            ]);

            $remainingQty -= $qtyToReduce;
        }

        // Jika masih ada qty yang tidak bisa dikurangi, throw error
        if ($remainingQty > 0) {
            throw new \Exception(
                "Stok produk tidak cukup. Kurang: {$remainingQty} unit. " .
                    'Tersedia di batch: ' . ($qty - $remainingQty) . ' unit'
            );
        }
    }

    private function createStockAdjustmentForSale($productId, $qty, $unitId, $storeId = null, $warehouseId = null, $invoiceNo = null)
    {
        /**
         * Buat StockAdjustment untuk tracking stok keluar (penjualan)
         * StockReports menggunakan StockAdjustment sebagai data source
         */

        // Get stok awal dari adjustment terakhir
        $lastAdjustment = StockAdjustment::where('product_id', $productId)
            ->where(function ($q) use ($storeId, $warehouseId) {
                if ($storeId) {
                    $q->where('store_id', $storeId);
                } else {
                    $q->where('warehouse_id', $warehouseId);
                }
            })
            ->orderBy('adjustment_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        // Hitung stok awal dari total_stok terakhir atau dari batch
        $stokAwal = 0;
        if ($lastAdjustment) {
            $stokAwal = $lastAdjustment->total_stok ?? 0;
        } else {
            // Jika belum ada adjustment, ambil dari total batch
            $stokAwal = StockBatch::where('product_id', $productId)
                ->where(function ($q) use ($storeId, $warehouseId) {
                    if ($storeId) {
                        $q->where('location_type', 'store')->where('location_id', $storeId);
                    } else {
                        $q->where('location_type', 'warehouse')->where('location_id', $warehouseId);
                    }
                })
                ->sum('qty');
        }

        // Stok keluar = qty penjualan
        // Total stok = stok awal - qty
        $totalStok = max(0, $stokAwal - $qty);

        // Buat adjustment record
        StockAdjustment::create([
            'product_id' => $productId,
            'store_id' => $storeId,
            'warehouse_id' => $warehouseId,
            'adjustment_type' => 'remove', // Penjualan = pengurangan stok (remove, bukan subtract)
            'stok_awal' => $stokAwal,
            'quantity' => $qty, // Qty yang keluar
            'total_stok' => $totalStok,
            'unit_id' => $unitId,
            'reason' => 'Penjualan - Invoice: ' . ($invoiceNo ?? '-'),
            'adjustment_date' => ($this->tanggal_penjualan) ? \Carbon\Carbon::parse($this->tanggal_penjualan)->toDateString() : now(),
            'user_id' => Auth::id(),
        ]);
    }

    private function createStockCardForSale($productId, $qty, $batchId = null, $fromLocation = '', $toLocationId = null, $saleId = null, $invoiceNo = null)
    {
        /**
         * Buat StockCard untuk audit trail penjualan
         * StockCard mencatat setiap pergerakan stok dengan detail lengkap
         */
        try {
            $stockCardService = app(StockCardService::class);

            $eventDate = $this->tanggal_penjualan ? Carbon::parse($this->tanggal_penjualan) : Carbon::now();

            $stockCardService->createStockCard([
                'product_id' => $productId,
                'batch_id' => $batchId,
                'type' => 'out', // Tipe = keluar (penjualan)
                'qty' => $qty,
                'from_location' => $fromLocation, // Dari: Toko/Gudang
                'to_location' => 'Customer', // Ke: Customer
                'reference_type' => 'sale', // Referensi dari Penjualan
                'reference_id' => $saleId, // ID Penjualan
                'note' => "Penjualan - Invoice: {$invoiceNo}",
                'created_at' => $eventDate,
                'updated_at' => $eventDate,
            ]);
        } catch (\Exception $e) {
            // Log error tapi jangan hentikan proses penjualan
            Log::warning("Failed to create StockCard for Sale {$invoiceNo}: " . $e->getMessage());
        }
    }

    public function save()
    {
        try {
            \Log::info('=== SAVE METHOD CALLED ===', [
                'editingSaleId' => $this->editingSaleId,
                'no_invoice' => $this->no_invoice,
                'customer_id' => $this->customer_id,
                'status' => $this->status,
            ]);
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            if ($this->editingSaleId) {
                abort_unless($user && (
                    (method_exists($user, 'hasPermissionTo') && (
                        $user->hasPermissionTo('sales.update') || $user->hasPermissionTo('transactions.manage')
                    ))
                    || (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'superadmin']))
                ), 403);
            } else {
                abort_unless($user && (
                    (method_exists($user, 'hasPermissionTo') && (
                        $user->hasPermissionTo('sales.create') || $user->hasPermissionTo('transactions.manage')
                    ))
                    || (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'superadmin']))
                ), 403);
            }

            // Step 1: Cek stok availability (hanya untuk penjualan baru, bukan edit)
            if (! $this->editingSaleId && ! $this->deliveryApproved) {
                if (! $this->checkStockAvailability()) {
                    // Stok tidak cukup, tampilkan warning
                    // User harus pilih ambil dari gudang atau tidak
                    return;
                }

                // Stok cukup di TOKO, langsung simpan TANPA surat jalan
                // Set flag agar tidak loop kembali ke sini
                $this->deliveryApproved = true;

                // Rekursif call save() untuk proses simpan
                return $this->save();
            }

            // Step 2: Jika sudah approved atau sedang edit, lanjut proses save
            if ($this->editingSaleId) {
                // Update validation: no_invoice unique except current record
                $this->rules['no_invoice'] = 'required|string|max:50|unique:sales,no_invoice,' . $this->editingSaleId;
            } else {
                // For new sale, auto-generate invoice if empty
                if (empty($this->no_invoice)) {
                    $this->generateInvoiceNumber();
                }
                // Make no_invoice required for new records
                $this->rules['no_invoice'] = 'required|string|max:50|unique:sales,no_invoice';
            }

            // Validate batch qty availability
            foreach ($this->saleItems as $index => $item) {
                if (! $item['batch_id'] || ! $item['qty']) {
                    continue;
                }

                $batch = StockBatch::find($item['batch_id']);
                if (! $batch) {
                    throw new \Exception('Batch tidak ditemukan pada item ' . ($index + 1));
                }

                // Check if requested qty exceeds available batch qty
                if ($item['qty'] > $batch->qty) {
                    throw new \Exception(
                        "Batch {$batch->nama_tumpukan} pada item " . ($index + 1) . ' tidak cukup. ' .
                            "Diminta: {$item['qty']} sak, Tersedia: {$batch->qty} sak"
                    );
                }
            }

            $this->validate();

            DB::transaction(function () {
                // Calculate total amount dari sale items (menggunakan total yang sudah dihitung per item)
                // Total per item sudah dihitung dengan formula: Qty × Unit Conversion × Harga Jual
                $totalAmount = collect($this->saleItems)->sum(function ($item) {
                    return (float) ($item['total'] ?? 0);
                });

                // Add kuli (delivery cost) to total
                $totalAmount += (float) ($this->kuli ?? 0);

                if ($this->editingSaleId) {
                    // Update existing sale
                    $sale = Sale::findOrFail($this->editingSaleId);
                    $sale->update([
                        'no_invoice' => $this->no_invoice,
                        'tanggal_penjualan' => $this->tanggal_penjualan,
                        'customer_id' => $this->customer_id,
                        'store_id' => $this->store_id,
                        'warehouse_id' => $this->warehouse_id,
                        'total_amount' => $totalAmount,
                        'status' => $this->status,
                        'keterangan' => $this->keterangan,
                    ]);

                    // Delete old items and create new ones
                    $sale->saleItems()->delete();
                } else {
                    // Create new sale
                    $sale = Sale::create([
                        'no_invoice' => $this->no_invoice,
                        'delivery_note_number' => $this->deliveryNoteNumber,
                        'delivery_date' => $this->deliveryDate,
                        'delivery_notes' => $this->deliveryNotes,
                        'tanggal_penjualan' => $this->tanggal_penjualan,
                        'customer_id' => $this->customer_id,
                        'store_id' => $this->store_id,
                        'warehouse_id' => $this->warehouse_id,
                        'total_amount' => $totalAmount,
                        'status' => $this->status,
                        'keterangan' => $this->keterangan,
                        'user_id' => Auth::id(),
                    ]);
                }

                // Create sale items and stock adjustments
                foreach ($this->saleItems as $item) {
                    $saleItem = $sale->saleItems()->create([
                        'product_id' => $item['product_id'],
                        'qty' => $item['qty'],
                        'unit_id' => $item['unit_id'],
                        'harga_jual' => $item['harga_jual'],
                        'batch_id' => $item['batch_id'] ?? null,
                    ]);

                    // HOLD LOGIC: If status is 'hold', use HoldStockService
                    if ($this->status === 'hold') {
                        $batch = StockBatch::find($item['batch_id']);
                        if ($batch) {
                            $holdService = app(\App\Services\HoldStockService::class);
                            $holdService->moveToHold($sale, $batch, $item['qty']);
                        }

                        continue; // Skip normal stock reduction
                    }

                    // PENTING: Prioritas TOKO dulu, jika tidak ada baru GUDANG
                    // Hanya satu yang aktif untuk menghindari pengurangan stok ganda
                    if ($sale->store_id) {
                        // Reduce dari TOKO
                        $this->reduceStockFromBatch(
                            $item['product_id'],
                            $item['qty'],
                            $sale->store_id,  // storeId
                            null              // warehouseId = null
                        );

                        // Create stock adjustment untuk TOKO
                        $this->createStockAdjustmentForSale(
                            $item['product_id'],
                            $item['qty'],
                            $item['unit_id'],
                            $sale->store_id,  // storeId
                            null,             // warehouseId = null
                            $sale->no_invoice
                        );

                        // Create stock card (kartu stok) untuk audit trail
                        $store = Store::find($sale->store_id);
                        $this->createStockCardForSale(
                            $item['product_id'],
                            $item['qty'],
                            $item['batch_id'] ?? null,
                            $store->nama_toko ?? 'Toko',
                            $sale->customer_id,
                            $sale->id,
                            $sale->no_invoice
                        );
                    } elseif ($sale->warehouse_id) {
                        // Reduce dari GUDANG (hanya jika toko tidak ada)
                        $this->reduceStockFromBatch(
                            $item['product_id'],
                            $item['qty'],
                            null,                 // storeId = null
                            $sale->warehouse_id   // warehouseId
                        );

                        // Create stock adjustment untuk GUDANG
                        $this->createStockAdjustmentForSale(
                            $item['product_id'],
                            $item['qty'],
                            $item['unit_id'],
                            null,                 // storeId = null
                            $sale->warehouse_id,  // warehouseId
                            $sale->no_invoice
                        );

                        // Create stock card (kartu stok) untuk audit trail
                        $warehouse = \App\Models\Warehouse::find($sale->warehouse_id);
                        $this->createStockCardForSale(
                            $item['product_id'],
                            $item['qty'],
                            $item['batch_id'] ?? null,
                            $warehouse->nama_gudang ?? 'Gudang',
                            $sale->customer_id,
                            $sale->id,
                            $sale->no_invoice
                        );
                    }
                }

                // Create transaction history entry
                try {
                    \App\Models\TransactionHistory::create([
                        'transaction_code' => $sale->no_invoice,
                        'transaction_type' => 'penjualan',
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'transaction_date' => $this->tanggal_penjualan, // Use actual sale date
                        'amount' => $totalAmount, // Use calculated total from above
                        'currency' => 'IDR',
                        'description' => 'Penjualan - ' . $sale->no_invoice . ($sale->customer_id ? ' ke ' . optional($sale->customer)->nama_customer : ''),
                        'status' => $this->status === 'hold' ? 'pending' : 'completed',
                        'user_id' => Auth::id(),
                        'notes' => $sale->keterangan,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to create TransactionHistory for Sale: ' . $e->getMessage());
                }

                $this->resetForm();
                $this->showCreateForm = false;
                session()->flash('success', $this->editingSaleId ? 'Penjualan berhasil diperbarui!' : 'Penjualan berhasil dibuat!');
            });
        } catch (\Exception $e) {
            Log::error('Sale save error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showCreateForm = false;
    }

    public function delete($id)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        abort_unless($user && (
            (method_exists($user, 'hasPermissionTo') && (
                $user->hasPermissionTo('sales.delete') || $user->hasPermissionTo('transactions.manage')
            ))
            || (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'superadmin']))
        ), 403);

        try {
            $sale = Sale::findOrFail($id);

            // Delete sale akan cascade delete sale items
            $sale->delete();

            session()->flash('success', 'Penjualan berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Sale delete error: ' . $e->getMessage());
            session()->flash('error', 'Gagal menghapus penjualan!');
        }
    }

    public function resetForm()
    {
        $this->no_invoice = '';
        $this->tanggal_penjualan = '';
        $this->tanggal = '';
        $this->bulan = '';
        $this->tahun = '';
        $this->customer_id = null;
        $this->store_id = null;
        $this->warehouse_id = null;
        $this->location_source = 'toko'; // Reset to default
        $this->status = 'completed';
        $this->keterangan = '';
        $this->saleItems = [];
        $this->editingSaleId = null;

        // Reset delivery note
        $this->deliveryNoteNumber = '';
        $this->deliveryDate = '';
        $this->deliveryNotes = '';
        $this->deliveryApproved = false;
        $this->showDeliveryNoteModal = false;

        // Reset stock warning
        $this->showStockWarning = false;
        $this->stockWarningMessage = '';
        $this->useWarehouseStock = false;

        $this->clearValidation();
    }

    public function render()
    {
        \Log::info('========== RENDER CALLED ==========');

        $sales = Sale::where(function ($query) {
            $query->where('no_invoice', 'like', '%' . $this->search . '%')
                ->orWhereHas('customer', fn($q) => $q->where('nama_pelanggan', 'like', '%' . $this->search . '%'));
        })
            ->latest()
            ->paginate(15);

        // Load data untuk form
        $customers = Customer::all();
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $products = $this->availableProducts();  // Use filtered products based on location
        $units = Unit::all();
        $stores = Store::all();
        $warehouses = \App\Models\Warehouse::all();

        return view('livewire.admin.sales.sales-index', [
            'sales' => $sales,
            'customers' => $customers,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'products' => $products,
            'units' => $units,
            'stores' => $stores,
            'warehouses' => $warehouses,
        ]);
    }

    /**
     * Save a new customer created inline from sales form
     */
    public function saveNewCustomer()
    {
        $this->validate([
            'new_customer_nama' => 'required|string|max:255',
            'new_customer_telepon' => 'nullable|string|max:50',
            'new_customer_email' => 'nullable|email|max:255',
        ]);

        $customer = Customer::create([
            'kode_pelanggan' => strtoupper(str_replace(' ', '', substr($this->new_customer_nama, 0, 6))) . rand(10, 99),
            'nama_pelanggan' => $this->new_customer_nama,
            'telepon' => $this->new_customer_telepon,
            'email' => $this->new_customer_email,
            'alamat' => $this->new_customer_alamat,
        ]);

        // Select created customer in form
        $this->customer_id = $customer->id;

        // Hide modal and clear inputs
        $this->showCreateCustomerModal = false;
        $this->new_customer_nama = '';
        $this->new_customer_telepon = '';
        $this->new_customer_email = '';
        $this->new_customer_alamat = '';

        session()->flash('success', 'Pelanggan baru berhasil dibuat dan dipilih.');
    }
}
