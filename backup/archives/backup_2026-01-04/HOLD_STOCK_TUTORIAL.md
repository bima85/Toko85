# 🧪 Tutorial Testing - Fitur Hold/Keep Stock

Panduan step-by-step untuk testing fitur Hold/Keep Stock di aplikasi Anda.

---

## 📋 Prasyarat

✅ Server Laravel berjalan (`npm run dev:all`)
✅ Migrations sudah dijalankan
✅ Ada data:

- Minimal 1 Produk dengan stok
- Minimal 1 Customer
- Minimal 1 StockBatch aktif

---

## 🚀 Tutorial Testing Lengkap

### **STEP 1: Verifikasi Data Awal**

1. Buka aplikasi: `http://192.168.1.5:8000` atau `http://localhost:8000`
2. Login ke admin dashboard
3. Buka **Admin → Stock Batches**
4. Cari produk dengan stok banyak (misal: 100 qty)
5. **Catat:**
   - Nama Produk
   - Nama Tumpukan
   - Qty saat ini

**Contoh:**

```
Produk: Beras Premium
Tumpukan: Grade A
Qty: 100
```

---

### **STEP 2: Test Hold - Pindahkan Stok ke Hold**

#### **Scenario: Customer Order 30 qty Beras Premium**

Kita akan simulasikan pindah stok ke HOLD menggunakan database/code:

**Option A: Via Tinker (Recommended)**

1. Buka terminal dan jalankan:

```bash
php artisan tinker
```

2. Copy-paste code ini (satu per satu):

```php
// Ambil produk & batch
$product = \App\Models\Product::find(1); // Ganti dengan ID produk Anda
$batch = \App\Models\StockBatch::where('product_id', $product->id)->first();

// Buat sale untuk simulasi order
$customer = \App\Models\Customer::first();

$sale = \App\Models\Sale::create([
    'no_invoice' => 'INV-HOLD-001',
    'tanggal_penjualan' => now(),
    'customer_id' => $customer->id,
    'user_id' => auth()->id() ?? 1,
    'status' => 'pending',
]);

// Pindahkan ke HOLD
$holdService = app(\App\Services\HoldStockService::class);
$result = $holdService->moveToHold($sale, $batch, 30);

// Lihat hasilnya
echo json_encode($result, JSON_PRETTY_PRINT);
```

**Output yang diharapkan:**

```json
{
  "success": true,
  "message": "✅ Stok berhasil ditahan. Qty: 30",
  "hold_batch": { ... }
}
```

3. Ketik `exit` untuk keluar tinker

---

#### **Verifikasi di UI:**

1. Buka **Admin → Stock Batches**
2. Cari produk yang tadi
3. **Harusnya ada 2 batch:**
   - ✅ `Grade A` → Qty: **70** (berkurang dari 100)
   - ✅ `Grade A - HOLD #123` → Qty: **30** (batch baru)

---

### **STEP 3: Lihat Dashboard Hold Orders**

1. Buka **Admin → Hold Orders** (menu baru)
   - URL: `/admin/hold-orders`

2. Klik tab **"Hold Aktif"**

3. **Harusnya muncul:**
   - No Invoice: `INV-HOLD-001`
   - Customer: nama customer Anda
   - Status: `Hold` (badge kuning)
   - 1 item

4. Klik tombol **"Detail"** untuk lihat:
   - Info customer
   - Item yang dipesan (30 qty Beras)
   - Status Hold dengan timestamp
   - Stok yang ditahan

---

### **STEP 4: Test Complete Hold**

**Scenario: Customer Membayar & Mengambil Barang**

1. Di halaman **Hold Orders**, cari order `INV-HOLD-001`

2. Klik tombol **"Selesai"** (button hijau dengan icon check)

3. Dialog konfirmasi akan muncul:

   ```
   Apakah Anda yakin ingin menyelesaikan transaksi untuk order ini?
   Stok akan dikurangi dari tumpukan hold.
   ```

4. Klik **"Selesaikan"**

5. **Harusnya:**
   - ✅ Alert hijau: `✅ Transaksi selesai. Stok terjual: 30`
   - Order hilang dari tab "Hold Aktif"
   - Muncul di tab "Selesai"

---

#### **Verifikasi di Stock Batches:**

1. Buka **Admin → Stock Batches**

2. **Harusnya:**
   - `Grade A` → Qty: **70** (tetap, tidak berubah)
   - `Grade A - HOLD #123` → **HILANG** (batch hold dihapus)

**Penjelasan:**

```
Awal:        Aktual=100
Hold:        Aktual=70, Hold=30
Complete:    Aktual=70 (stok terjual, tidak berubah lagi)
```

---

### **STEP 5: Test Cancel Hold**

**Scenario: Customer Membatalkan Order**

1. Buat order hold baru (ulangi STEP 2 dengan invoice berbeda)

```php
php artisan tinker

$product = \App\Models\Product::find(1);
$batch = \App\Models\StockBatch::where('product_id', $product->id)
    ->where('status', 'aktual')
    ->first();
$customer = \App\Models\Customer::first();

$sale = \App\Models\Sale::create([
    'no_invoice' => 'INV-HOLD-002',
    'tanggal_penjualan' => now(),
    'customer_id' => $customer->id,
    'user_id' => 1,
    'status' => 'pending',
]);

$holdService = app(\App\Services\HoldStockService::class);
$result = $holdService->moveToHold($sale, $batch, 20);
echo $result['message'];

exit
```

2. Buka **Admin → Hold Orders**

3. Cari order `INV-HOLD-002`

4. Klik tombol **"Batalkan"** (button merah dengan icon times)

5. Dialog konfirmasi akan muncul:

   ```
   Apakah Anda yakin ingin membatalkan hold untuk order ini?
   Stok akan dikembalikan ke tumpukan asli.
   ```

6. Klik **"Batalkan"**

7. **Harusnya:**
   - ✅ Alert hijau: `✅ Hold dibatalkan. Stok dikembalikan: 20`
   - Order pindah ke tab "Dibatalkan"

---

#### **Verifikasi di Stock Batches:**

1. Buka **Admin → Stock Batches**

2. **Harusnya stok kembali normal:**
   - Batch `Grade A` qty meningkat kembali
   - Batch HOLD hilang

---

### **STEP 6: Verifikasi di StockCard**

1. Buka **Admin → Transaction History** (atau Stock Card Report)

2. Cari entries dengan reference = Sale #123

3. **Harusnya ada entries:**

| Type          | Qty | From                | To                  | Note                             |
| ------------- | --- | ------------------- | ------------------- | -------------------------------- |
| `hold`        | -30 | Grade A             | Grade A - HOLD #123 | Stok ditahan untuk Order #123    |
| `cancel_hold` | +30 | Grade A - HOLD #123 | Grade A             | Hold dibatalkan untuk Order #123 |

**Atau jika completed:**

| Type   | Qty | From      | To                  | Note                                   |
| ------ | --- | --------- | ------------------- | -------------------------------------- |
| `hold` | -30 | Grade A   | Grade A - HOLD #123 | Stok ditahan untuk Order #123          |
| `sale` | -30 | HOLD #123 | Customer: John Doe  | Penjualan selesai dari hold Order #123 |

---

### **STEP 7: Test Stock Summary Report**

Cek ringkasan stok dengan hold info:

```php
php artisan tinker

$holdService = app(\App\Services\HoldStockService::class);

// Ambil summary stok
$productId = 1; // Ganti dengan ID produk Anda
$summary = $holdService->getStockSummary($productId);

// Tampilkan
echo "STOCK SUMMARY:\n";
echo "Available: " . $summary['available'] . "\n";
echo "Hold: " . $summary['hold'] . "\n";
echo "Total: " . $summary['total'] . "\n";
echo "% Hold: " . $summary['percentage_hold'] . "%\n";

exit
```

**Output contoh:**

```
STOCK SUMMARY:
Available: 70
Hold: 0
Total: 70
% Hold: 0%
```

---

## 🎯 Test Checklist

Tandai setiap test yang sudah berhasil:

```
[ ] Data produk & stok sudah disiapkan
[ ] Bisa membuat order hold via tinker
[ ] Stock Batches menampilkan 2 batch (aktual + hold)
[ ] Halaman Hold Orders bisa diakses
[ ] Bisa melihat detail order hold
[ ] Bisa complete order hold
  [ ] Stok berkurang dengan benar
  [ ] Batch hold hilang
[ ] Bisa cancel order hold
  [ ] Stok dikembalikan
  [ ] Order pindah ke tab dibatalkan
[ ] StockCard mencatat semua transaksi
[ ] Stock summary menampilkan available & hold
```

---

## 🐛 Troubleshooting

### **Q: "Stok tidak cukup" saat move to hold?**

A: Pastikan qty yang diminta < qty batch. Contoh: batch = 100, diminta = 30 ✓

### **Q: Batch HOLD tidak muncul di Stock Batches?**

A: Buka dengan pagination, atau filter by status='hold'

### **Q: Error "Tidak ada stok yang ditahan"?**

A: Pastikan order sudah di-hold sebelum di-complete/cancel

### **Q: StockCard tidak mencatat transaksi?**

A: Refresh halaman atau clear cache: `php artisan cache:clear`

### **Q: Duplikasi batch HOLD?**

A: Normal! Jika ada beberapa order, setiap order punya HOLD batch sendiri

---

## 📝 Test Script Lengkap (Copy-Paste)

Untuk test cepat tanpa manual:

```php
php artisan tinker

// SETUP
$product = \App\Models\Product::find(1);
$batch = \App\Models\StockBatch::where('product_id', $product->id)
    ->where('status', 'aktual')->first();
$customer = \App\Models\Customer::first();
$holdService = app(\App\Services\HoldStockService::class);

echo "=== INITIAL STATE ===\n";
echo "Batch: " . $batch->nama_tumpukan . "\n";
echo "Qty: " . $batch->qty . "\n\n";

// TEST 1: MOVE TO HOLD
echo "=== TEST 1: MOVE TO HOLD ===\n";
$sale1 = \App\Models\Sale::create([
    'no_invoice' => 'TEST-HOLD-001',
    'tanggal_penjualan' => now(),
    'customer_id' => $customer->id,
    'user_id' => 1,
]);

$result = $holdService->moveToHold($sale1, $batch, 30);
echo $result['message'] . "\n\n";

$batch->refresh();
$holdBatch = \App\Models\StockBatch::where('status', 'hold')
    ->where('product_id', $product->id)->first();
echo "After Hold:\n";
echo "Aktual Qty: " . $batch->qty . "\n";
echo "Hold Qty: " . $holdBatch->qty . "\n\n";

// TEST 2: COMPLETE
echo "=== TEST 2: COMPLETE HOLD ===\n";
$result = $holdService->completeHold($sale1);
echo $result['message'] . "\n";
$batch->refresh();
echo "After Complete:\n";
echo "Aktual Qty: " . $batch->qty . "\n\n";

// TEST 3: CANCEL
echo "=== TEST 3: CANCEL HOLD ===\n";
$sale2 = \App\Models\Sale::create([
    'no_invoice' => 'TEST-HOLD-002',
    'tanggal_penjualan' => now(),
    'customer_id' => $customer->id,
    'user_id' => 1,
]);

$batch->refresh();
$result = $holdService->moveToHold($sale2, $batch, 20);
echo $result['message'] . "\n";

$result = $holdService->cancelHold($sale2);
echo $result['message'] . "\n";
$batch->refresh();
echo "After Cancel:\n";
echo "Final Aktual Qty: " . $batch->qty . "\n";

exit
```

---

## ✅ Testing Selesai!

Jika semua checklist sudah ✓, fitur Hold/Keep Stock berfungsi dengan sempurna! 🎉

**Langkah berikutnya:**

1. Push ke GitHub
2. Test di production (jika ada)
3. Dokumentasikan perubahan di Changelog

---

**Need help? Check HOLD_STOCK_FEATURE.md untuk dokumentasi lengkap!**
