<?php

namespace App\Livewire\Admin;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Store;
use App\Models\StockAdjustment;
use App\Models\StockBatch;
use App\Services\StockCardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('layouts.admin')]
class Sales extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $no_invoice;
    public $tanggal_penjualan;
    public $customer_id;
    public $store_id;
    public $warehouse_id;
    public $status = 'completed';
    public $keterangan;
    public $editingSaleId = null;
    public $showCreateForm = false;

    // Sale items
    public $saleItems = [];

    // Kuli (biaya angkut)
    public $kuli = 0;

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
        'no_invoice' => 'required|string|max:50|unique:sales,no_invoice',
        'tanggal_penjualan' => 'required|date',
        'customer_id' => 'required|exists:customers,id',
        'store_id' => 'nullable|exists:stores,id',
        'warehouse_id' => 'nullable|exists:warehouses,id',
        'status' => 'required|in:pending,completed,cancelled',
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
        abort_unless($user && method_exists($user, 'hasRole') && $user->hasRole('admin'), 403);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        // DEFAULT: Penjualan dari TOKO (bukan gudang)
        $firstStore = Store::orderBy('nama_toko')->first();
        $this->store_id = $firstStore ? $firstStore->id : null;
        $this->warehouse_id = null; // Tidak pakai gudang by default

        // Set tanggal
        $this->tanggal_penjualan = date('Y-m-d');
        $this->deliveryDate = date('Y-m-d');

        // Generate nomor surat jalan
        $this->generateDeliveryNoteNumber();

        $this->editingSaleId = null;
        $this->showCreateForm = true;
        $this->deliveryApproved = false;
    }

    public function edit($id)
    {
        $sale = Sale::with('saleItems')->findOrFail($id);
        $this->editingSaleId = $sale->id;
        $this->no_invoice = $sale->no_invoice;
        $this->tanggal_penjualan = $sale->tanggal_penjualan->format('Y-m-d');
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

    public function addItem()
    {
        $this->saleItems[] = [
            'category_id' => null,
            'subcategory_id' => null,
            'product_id' => null,
            'product_search' => '',  // Tambah untuk searchable product input
            'qty' => null,
            'unit_id' => null,
            'harga_jual' => null,
            'total' => 0,
            'batch_id' => null,
            'batch_name' => null,
            'batch_warning' => null,
        ];
    }

    public function removeItem($index)
    {
        unset($this->saleItems[$index]);
        $this->saleItems = array_values($this->saleItems);
    }

    /**
     * Get available batches untuk item tertentu berdasarkan product_id dan location
     * Dipanggil dari template untuk populate dropdown options
     */
    public function getAvailableBatches($index)
    {
        $productId = $this->saleItems[$index]['product_id'] ?? null;

        if (!$productId) {
            return collect([]);
        }

        $query = StockBatch::where('product_id', $productId)
            ->where('qty', '>', 0)
            ->orderBy('created_at', 'asc');

        // Filter by location (Store or Warehouse) ONLY jika sudah dipilih
        if ($this->store_id) {
            $query->where('location_type', 'store')
                ->where('location_id', $this->store_id);
        } elseif ($this->warehouse_id) {
            $query->where('location_type', 'warehouse')
                ->where('location_id', $this->warehouse_id);
        }
        // Jika keduanya NULL, tampilkan semua batch untuk product (no filter)

        $batches = $query->get();

        \Log::info("getAvailableBatches: product=$productId, store={$this->store_id}, warehouse={$this->warehouse_id}, count={$batches->count()}");

        return $batches;
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

            // Check batch availability
            $this->checkBatchAvailability($index);
        }
    }

    private function checkBatchAvailability($index)
    {
        /**
         * Check apakah batch qty cukup untuk requested qty
         * Store warning/info di saleItems
         */
        if (!isset($this->saleItems[$index])) {
            return;
        }

        $batchId = $this->saleItems[$index]['batch_id'] ?? null;
        $requestedQty = $this->saleItems[$index]['qty'] ?? 0;

        if (!$batchId || !$requestedQty) {
            $this->saleItems[$index]['batch_warning'] = null;
            return;
        }

        $batch = StockBatch::find($batchId);
        if (!$batch) {
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
         * Jika product_id berubah, auto-populate batch info
         * Jika batch_id berubah, update batch_name
         * Jika qty berubah, check batch availability
         * Jika product_search berubah, extract product ID dari search string
         */
        \Log::info("updatedSaleItems: key=$key, value=$value");
        \Log::info("Checking if key contains .product_search: " . (strpos($key, '.product_search') !== false ? 'YES' : 'NO'));

        if (strpos($key, '.product_search') !== false) {
            // Handle product search input dari datalist
            \Log::info("INSIDE product_search block! Full key: '$key'");
            // Key format is "0.product_search" not "saleItems.0.product_search"
            preg_match('/(\d+)\.product_search/', $key, $matches);
            \Log::info("Regex matches: " . json_encode($matches));
            if (isset($matches[1])) {
                $index = (int)$matches[1];
                $searchValue = trim($value);

                \Log::info("Product search at index $index: '$searchValue'");

                // Extract product code dari format "[CODE] Name"
                if (preg_match('/\[([^\]]+)\]/', $searchValue, $codeMatches)) {
                    $productCode = trim($codeMatches[1]);
                    \Log::info("Extracted product code: '$productCode'");

                    $product = Product::where('kode_produk', $productCode)->first();
                    if ($product) {
                        \Log::info("Product found: {$product->nama_produk} (ID: {$product->id})");
                        $this->saleItems[$index]['product_id'] = $product->id;
                        $this->populateBatchForItem($index);
                    } else {
                        \Log::warning("Product not found for code: '$productCode'");
                    }
                } else {
                    \Log::warning("Could not extract product code from: '$searchValue'");
                }
            }
        } elseif (strpos($key, '.product_id') !== false) {
            // Extract index dari key: "0.product_id" => 0
            preg_match('/(\d+)\.product_id/', $key, $matches);
            if (isset($matches[1])) {
                $index = (int)$matches[1];
                \Log::info("Product changed at index $index to $value");
                $this->populateBatchForItem($index);
            }
        } elseif (strpos($key, '.batch_id') !== false) {
            // User memilih batch manual, update batch_name dari database
            preg_match('/(\d+)\.batch_id/', $key, $matches);
            if (isset($matches[1])) {
                $index = (int)$matches[1];
                $batchId = $this->saleItems[$index]['batch_id'] ?? null;
                if ($batchId) {
                    $batch = StockBatch::find($batchId);
                    $this->saleItems[$index]['batch_name'] = $batch ? $batch->nama_tumpukan : null;
                    // Check availability saat batch berubah
                    $this->checkBatchAvailability($index);
                } else {
                    $this->saleItems[$index]['batch_name'] = null;
                }
            }
        } elseif (strpos($key, '.qty') !== false) {
            // User mengubah qty, check batch availability
            preg_match('/(\d+)\.qty/', $key, $matches);
            if (isset($matches[1])) {
                $index = (int)$matches[1];
                $this->checkBatchAvailability($index);
            }
        }
    }

    private function populateBatchForItem($index)
    {
        /**
         * Auto-select batch untuk item pada index tertentu
         * Menggunakan FIFO: ambil batch tertua dengan stok tersedia
         */
        if (!isset($this->saleItems[$index])) {
            return;
        }

        $productId = $this->saleItems[$index]['product_id'] ?? null;
        if (!$productId) {
            $this->saleItems[$index]['batch_id'] = null;
            $this->saleItems[$index]['batch_name'] = null;
            return;
        }

        // Get location dari form
        $storeId = $this->store_id ?? null;
        $warehouseId = $this->warehouse_id ?? null;

        // Query batch yang tersedia (FIFO - oldest first)
        $query = StockBatch::where('product_id', $productId)
            ->where('qty', '>', 0)
            ->orderBy('created_at', 'asc');

        if ($storeId) {
            $query->where('location_type', 'store')
                ->where('location_id', $storeId);
        } elseif ($warehouseId) {
            $query->where('location_type', 'warehouse')
                ->where('location_id', $warehouseId);
        }

        $batch = $query->first();

        if ($batch) {
            $this->saleItems[$index]['batch_id'] = $batch->id;
            $this->saleItems[$index]['batch_name'] = $batch->nama_tumpukan ?? "Batch #{$batch->id}";
        } else {
            $this->saleItems[$index]['batch_id'] = null;
            $this->saleItems[$index]['batch_name'] = "Stok tidak tersedia";
        }
    }

    public function generateInvoiceNumber()
    {
        if (!$this->customer_id) {
            $this->no_invoice = '';
            return;
        }

        $customer = Customer::find($this->customer_id);
        if (!$customer) {
            $this->no_invoice = '';
            return;
        }

        // Format: [CUSTOMER_CODE]/PJ/[YYYYMMDD]/[SEQUENCE]
        // Contoh: CUST001/PJ/20251107/001
        $today = date('Ymd');

        // Cari jumlah invoice untuk pelanggan ini hari ini
        $lastInvoice = Sale::where('customer_id', $this->customer_id)
            ->whereDate('tanggal_penjualan', date('Y-m-d'))
            ->latest('id')
            ->first();

        $sequence = 1;
        if ($lastInvoice) {
            // Extract sequence dari last invoice
            $parts = explode('/', $lastInvoice->no_invoice);
            if (count($parts) >= 4) {
                $sequence = (int)$parts[3] + 1;
            }
        }

        $this->no_invoice = sprintf(
            '%s/PJ/%s/%03d',
            $customer->kode_pelanggan,
            $today,
            $sequence
        );
    }

    public function generateDeliveryNoteNumber()
    {
        // Format: SJ/[YYYYMMDD]/[SEQUENCE]
        // Contoh: SJ/20251107/001
        $today = date('Ymd');

        // Cari jumlah surat jalan hari ini
        $lastSale = Sale::whereDate('tanggal_penjualan', date('Y-m-d'))
            ->latest('id')
            ->first();

        $sequence = 1;
        if ($lastSale && !empty($lastSale->delivery_note_number)) {
            // Extract sequence dari last delivery note
            $parts = explode('/', $lastSale->delivery_note_number);
            if (count($parts) >= 3) {
                $sequence = (int)$parts[2] + 1;
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

        if (!empty($insufficientItems)) {
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

    public function updatedStoreId()
    {
        /**
         * Ketika store berubah, refresh batch info semua items
         * karena batch yang tersedia bisa berbeda per location
         */
        foreach (array_keys($this->saleItems) as $index) {
            $this->populateBatchForItem($index);
        }
    }

    public function updatedWarehouseId()
    {
        /**
         * Ketika warehouse berubah, refresh batch info semua items
         */
        foreach (array_keys($this->saleItems) as $index) {
            $this->populateBatchForItem($index);
        }
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
                    "Tersedia di batch: " . ($qty - $remainingQty) . " unit"
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
            'adjustment_date' => now(),
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
            ]);
        } catch (\Exception $e) {
            // Log error tapi jangan hentikan proses penjualan
            Log::warning("Failed to create StockCard for Sale {$invoiceNo}: " . $e->getMessage());
        }
    }

    public function save()
    {
        try {
            // Step 1: Cek stok availability (hanya untuk penjualan baru, bukan edit)
            if (!$this->editingSaleId && !$this->deliveryApproved) {
                if (!$this->checkStockAvailability()) {
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
            }

            // Validate batch qty availability
            foreach ($this->saleItems as $index => $item) {
                if (!$item['batch_id'] || !$item['qty']) {
                    continue;
                }

                $batch = StockBatch::find($item['batch_id']);
                if (!$batch) {
                    throw new \Exception("Batch tidak ditemukan pada item " . ($index + 1));
                }

                // Check if requested qty exceeds available batch qty
                if ($item['qty'] > $batch->qty) {
                    throw new \Exception(
                        "Batch {$batch->nama_tumpukan} pada item " . ($index + 1) . " tidak cukup. " .
                            "Diminta: {$item['qty']} sak, Tersedia: {$batch->qty} sak"
                    );
                }
            }

            $this->validate();

            DB::transaction(function () {
                if ($this->editingSaleId) {
                    // Update existing sale
                    $sale = Sale::findOrFail($this->editingSaleId);
                    $sale->update([
                        'no_invoice' => $this->no_invoice,
                        'tanggal_penjualan' => $this->tanggal_penjualan,
                        'customer_id' => $this->customer_id,
                        'store_id' => $this->store_id,
                        'warehouse_id' => $this->warehouse_id,
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
                    $totalAmount = collect($this->saleItems)->sum(function ($item) {
                        return ($item['qty'] ?? 0) * ($item['harga_jual'] ?? 0);
                    });

                    \App\Models\TransactionHistory::create([
                        'transaction_code' => $sale->no_invoice,
                        'transaction_type' => 'penjualan',
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'transaction_date' => now(),
                        'amount' => $totalAmount,
                        'currency' => 'IDR',
                        'description' => 'Penjualan - ' . $sale->no_invoice . ($sale->customer_id ? ' ke ' . optional($sale->customer)->nama_customer : ''),
                        'status' => 'completed',
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
        $this->customer_id = null;
        $this->store_id = null;
        $this->warehouse_id = null;
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
        $products = Product::all();  // Always load products even if form not shown
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
}
