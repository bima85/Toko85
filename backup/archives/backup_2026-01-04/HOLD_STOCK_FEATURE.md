# 📋 Fitur Hold/Keep Stock - Dokumentasi Lengkap

Fitur ini memungkinkan Anda untuk menahan stok sementara ketika customer melakukan pre-order/reservasi sebelum membayar atau mengambil barang.

---

## 🎯 Fitur Utama

### 1. **Memindahkan Stok ke Hold**

Ketika customer melakukan order, stok dapat ditahan ke tumpukan HOLD:

- ✅ Stok normal berkurang
- ✅ Tumpukan HOLD dibuat/diperbarui
- ✅ Setiap transaksi dicatat di StockCard

### 2. **Status Order**

Order memiliki status yang jelas:

- `hold` - Stok ditahan untuk order
- `completed` - Order selesai, stok terjual
- `cancelled` - Order dibatalkan, stok dikembalikan

### 3. **Manajemen Hold**

Dari dashboard "Hold Orders", Anda bisa:

- 📋 Lihat semua order yang hold
- 👁️ Lihat detail order dan stok yang ditahan
- ✅ Selesaikan order (stok berkurang)
- ❌ Batalkan order (stok dikembalikan)

---

## 📁 File yang Ditambahkan

```
app/
  Services/
    └─ HoldStockService.php          ← Logika hold/cancel/complete
  Livewire/Admin/
    └─ HoldOrderManager.php          ← Component UI untuk manage hold

database/migrations/
  ├─ 2025_12_14_create_status_column_stock_batches.php  ← Tambah status column
  └─ 2025_12_14_add_hold_timestamps_to_sales.php        ← Tambah hold timestamps

resources/views/livewire/admin/
  └─ hold-order-manager.blade.php   ← UI untuk hold manager
```

---

## 🔄 Workflow Lengkap

### **Scenario 1: Order Normal → Hold → Complete**

```
1. AWAL
   Tumpukan Grade A = 100 qty (aktual)

2. CUSTOMER ORDER → HOLD
   $ holdStockService->moveToHold($sale, $batch, 30)

   Hasil:
   - Tumpukan Grade A = 70 (aktual)
   - Tumpukan Grade A - HOLD #12345 = 30 (hold)
   - StockCard: type=hold, qty=-30

3. CUSTOMER BAYAR → COMPLETE
   $ holdStockService->completeHold($sale)

   Hasil:
   - Tumpukan Grade A - HOLD #12345 dihapus
   - StockCard: type=sale, qty=-30
   - Final: Grade A = 70 (stok terjual)

4. LAPORAN STOK
   - Available (aktual): 70
   - Hold: 0
   - Total: 70
```

### **Scenario 2: Order Hold → Batalkan**

```
1. AWAL
   Tumpukan Grade A = 100 (aktual)

2. CUSTOMER ORDER → HOLD
   Tumpukan Grade A = 70 (aktual)
   Tumpukan Grade A - HOLD #12345 = 30 (hold)

3. CUSTOMER BATAL → CANCEL
   $ holdStockService->cancelHold($sale)

   Hasil:
   - Tumpukan Grade A - HOLD #12345 dihapus
   - Tumpukan Grade A = 100 (dikembalikan)
   - StockCard: type=cancel_hold, qty=+30

4. LAPORAN STOK
   - Available (aktual): 100 (kembali ke semula)
   - Hold: 0
   - Total: 100
```

---

## 💻 Cara Menggunakan di Code

### **1. Move to Hold**

```php
use App\Services\HoldStockService;
use App\Models\Sale;
use App\Models\StockBatch;

$holdService = app(HoldStockService::class);

// Ambil data
$sale = Sale::find(12345);
$batch = StockBatch::find(1);

// Pindahkan ke hold
$result = $holdService->moveToHold($sale, $batch, 30); // qty = 30

// Response:
// [
//   'success' => true,
//   'message' => '✅ Stok berhasil ditahan. Qty: 30',
//   'hold_batch' => StockBatch instance
// ]
```

### **2. Complete Hold**

```php
$sale = Sale::find(12345);

// Selesaikan transaksi
$result = $holdService->completeHold($sale);

// Response:
// [
//   'success' => true,
//   'message' => '✅ Transaksi selesai. Stok terjual: 30',
// ]
```

### **3. Cancel Hold**

```php
$sale = Sale::find(12345);

// Batalkan dan kembalikan stok
$result = $holdService->cancelHold($sale);

// Response:
// [
//   'success' => true,
//   'message' => '✅ Hold dibatalkan. Stok dikembalikan: 30',
// ]
```

### **4. Get Stock Summary**

```php
// Ambil ringkasan stok
$summary = $holdService->getStockSummary(
    $productId,
    ['type' => 'Store', 'id' => 1]
);

// Response:
// [
//   'available' => 70,      // Stok aktual
//   'hold' => 30,           // Stok yang ditahan
//   'total' => 100,
//   'percentage_hold' => 30.0
// ]
```

### **5. Get Active Holds**

```php
// Ambil list order yang masih hold
$activeHolds = $holdService->getActiveHolds($productId = null);

// Jika dengan filter product ID:
$activeHolds = $holdService->getActiveHolds(5); // product_id = 5

// Response: Collection of Sale dengan status 'hold'
```

---

## 🎨 UI - Hold Order Manager

### **Akses dari Menu:**

- URL: `/admin/hold-orders`
- Menu Admin → Hold Orders

### **Tabs Tersedia:**

1. **Hold Aktif** - Order yang sedang ditahan
2. **Selesai** - Order yang sudah di-complete
3. **Dibatalkan** - Order yang di-cancel

### **Fitur UI:**

✅ **Summary Cards:**

- Hold Aktif (jumlah order)
- Selesai Hari Ini
- Dibatalkan Hari Ini
- Total Qty Hold

✅ **Search & Filter:**

- Cari berdasarkan No Invoice
- Cari berdasarkan Nama Customer

✅ **Actions:**

- 👁️ Lihat Detail Order
- ✅ Selesaikan Hold → Complete
- ❌ Batalkan Hold → Cancel

✅ **Detail Modal:**

- Info Customer
- Item yang Dipesan
- Status Hold & Timestamp
- Stok yang Ditahan per Tumpukan

---

## 📊 Database Changes

### **1. Stock Batches**

```sql
ALTER TABLE stock_batches ADD COLUMN status ENUM('aktual', 'hold') DEFAULT 'aktual';
ALTER TABLE stock_batches ADD INDEX idx_status (status);
```

Column baru:

- `status` - Menandai apakah batch aktual atau hold

### **2. Sales**

```sql
ALTER TABLE sales ADD COLUMN held_at TIMESTAMP NULL;
ALTER TABLE sales ADD COLUMN cancelled_at TIMESTAMP NULL;
ALTER TABLE sales ADD COLUMN completed_at TIMESTAMP NULL;
```

Columns baru:

- `held_at` - Waktu order di-hold
- `cancelled_at` - Waktu order di-cancel
- `completed_at` - Waktu order di-complete

---

## 🔍 StockCard Entries

Setiap transaksi hold dicatat di StockCard dengan type berbeda:

| Type          | Arti                                | Dampak Qty |
| ------------- | ----------------------------------- | ---------- |
| `hold`        | Pindah ke tumpukan hold             | `-qty`     |
| `cancel_hold` | Batalkan hold, kembalikan ke aktual | `+qty`     |
| `sale`        | Penjualan selesai dari hold         | `-qty`     |

---

## ⚠️ Validasi & Error Handling

### **Validasi Otomatis:**

1. **Stok Cukup**

   ```php
   if ($batch->qty < $qty) {
       throw new \Exception("Stok tidak cukup. Tersedia: {$batch->qty}, Diminta: {$qty}");
   }
   ```

2. **Hold Exists**

   ```php
   if (!$holdBatch) {
       throw new \Exception("Tidak ada stok yang ditahan untuk order ini");
   }
   ```

3. **Transactional**
   - Semua operasi wrapped dalam `DB::transaction()`
   - Jika error, semua perubahan rollback

---

## 🚀 Best Practices

### ✅ DO:

- ✅ Gunakan HoldStockService untuk semua operasi hold
- ✅ Always wrap dalam try-catch di UI
- ✅ Check stok sebelum hold via `getStockSummary()`
- ✅ Manajemen hold dari dashboard dedicated

### ❌ DON'T:

- ❌ Jangan langsung update StockBatch qty
- ❌ Jangan hapus batch HOLD manual (biarkan service)
- ❌ Jangan skip validasi stok

---

## 📈 Reporting & Analytics

### **Metrics yang Bisa Dimonitor:**

```php
// Total hold per product
$holdPerProduct = StockBatch::where('status', 'hold')
    ->groupBy('product_id')
    ->selectRaw('product_id, SUM(qty) as total_hold')
    ->get();

// Orders pending (hold lama)
$pendingOrders = Sale::where('status', 'hold')
    ->where('held_at', '<', now()->subDays(7))
    ->count();

// Conversion rate (hold → complete)
$holdCount = Sale::where('status', 'hold')->count();
$completedCount = Sale::where('status', 'completed')->count();
$completionRate = $completedCount / $holdCount * 100; // %
```

---

## 🛠️ Troubleshooting

### **Q: Stok tidak berkurang saat hold?**

A: Pastikan migration sudah dijalankan dan gunakan HoldStockService, jangan update manual.

### **Q: Batch HOLD ganda?**

A: Normal! Jika ada beberapa order untuk produk yang sama, akan ada multiple HOLD batches dengan nama berbeda (HOLD #12345, HOLD #12346, dll).

### **Q: Gimana tracking history?**

A: Lihat StockCard dengan `reference_type = Sale` dan `type = 'hold'` / `'cancel_hold'` / `'sale'`.

---

## 📝 Migrasi dari Sistem Lama (Optional)

Jika punya order lama yang perlu dikonversi ke hold:

```php
// Script helper untuk migrate existing orders to hold status
$orders = Sale::where('status', 'pending')->get();
foreach ($orders as $order) {
    $order->update(['status' => 'hold', 'held_at' => $order->created_at]);
}
```

---

**✨ Sistem Hold/Keep stock siap digunakan! 🚀**
