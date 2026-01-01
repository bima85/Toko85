# 🚀 Quick Start - Hold Stock Feature

**TL;DR Version untuk langsung test!**

---

## 📍 Akses Halaman

```
http://localhost:8000/admin/hold-orders
```

---

## ⚡ Test Cepat (5 Menit)

### 1️⃣ Buka Terminal, Jalankan Tinker

```bash
php artisan tinker
```

### 2️⃣ Copy-Paste Satu Blok Ini

```php
$product = \App\Models\Product::find(1);
$batch = \App\Models\StockBatch::where('product_id', $product->id)->where('status', 'aktual')->first();
$customer = \App\Models\Customer::first();

$sale = \App\Models\Sale::create([
    'no_invoice' => 'TEST-001',
    'tanggal_penjualan' => now(),
    'customer_id' => $customer->id,
    'user_id' => 1,
]);

$holdService = app(\App\Services\HoldStockService::class);
$holdService->moveToHold($sale, $batch, 30);

exit
```

### 3️⃣ Lihat Hasilnya

- Buka `/admin/hold-orders`
- Klik tab "Hold Aktif"
- Order `TEST-001` harus muncul ✓

### 4️⃣ Test Complete

- Klik tombol "Selesai"
- Confirm
- Order hilang dari Hold Aktif ✓

### 5️⃣ Test Cancel

```php
php artisan tinker

$sale = \App\Models\Sale::create([
    'no_invoice' => 'TEST-002',
    'tanggal_penjualan' => now(),
    'customer_id' => \App\Models\Customer::first()->id,
    'user_id' => 1,
]);

$batch = \App\Models\StockBatch::where('product_id', 1)->where('status', 'aktual')->first();
$holdService = app(\App\Services\HoldStockService::class);
$holdService->moveToHold($sale, $batch, 20);

exit
```

- Buka `/admin/hold-orders`
- Klik "Batalkan" di order `TEST-002`
- Order pindah ke tab "Dibatalkan" ✓

---

## 📊 Hasil Akhir

| Test       | Result                            | ✓   |
| ---------- | --------------------------------- | --- |
| Hold Order | Stok berkurang, batch hold muncul | ✓   |
| Complete   | Batch hold hilang, stok tetap     | ✓   |
| Cancel     | Stok dikembalikan, order canceled | ✓   |

---

## 📋 File yang Ditambahkan

```
✅ app/Services/HoldStockService.php           ← Logika
✅ app/Livewire/Admin/HoldOrderManager.php     ← Component
✅ resources/views/livewire/admin/hold-order-manager.blade.php  ← UI
✅ database/migrations/2025_12_14_*.php        ← Database
```

---

## 🎯 API Quick Reference

```php
// 1. Move to Hold
$holdService->moveToHold($sale, $batch, $qty);

// 2. Complete
$holdService->completeHold($sale);

// 3. Cancel
$holdService->cancelHold($sale);

// 4. Get Summary
$summary = $holdService->getStockSummary($productId);
// Returns: ['available' => 70, 'hold' => 30, 'total' => 100]

// 5. Get Active Holds
$orders = $holdService->getActiveHolds($productId);
```

---

## 🔗 Status Flow

```
Order Created
    ↓
Hold (stock move to HOLD batch)
    ↓
    ├→ Complete (stock removed)
    └→ Cancel (stock returned)
```

---

**✨ Done! Enjoy your new Hold/Keep Stock feature! 🎉**

Untuk detail lebih, baca: [HOLD_STOCK_FEATURE.md](HOLD_STOCK_FEATURE.md)
Untuk tutorial lengkap, baca: [HOLD_STOCK_TUTORIAL.md](HOLD_STOCK_TUTORIAL.md)
