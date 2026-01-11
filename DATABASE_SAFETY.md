# Database Safety Guidelines

## 🚨 Praktik Keamanan Database

### Konfirmasi Wajib untuk Operasi Berbahaya

Semua operasi yang dapat menghilangkan atau mengubah data penting **HARUS** dikonfirmasi terlebih dahulu.

### 1. Command Line Operations

#### Database Restore Command

```bash
php artisan db:restore [--file=filename.sql] [--fresh] [--force]
```

**Konfirmasi yang Diperlukan:**

- Konfirmasi umum operasi restore
- Mengetik "RESTORE" untuk konfirmasi
- Konfirmasi final dengan peringatan

**Fitur:**

- Menampilkan status database saat ini
- Menampilkan informasi file backup
- Progress bar saat restore
- Ringkasan hasil restore

#### Database Wipe Command

```bash
php artisan db:wipe-safe [--force]
```

**Konfirmasi yang Diperlukan:**

- Konfirmasi dasar wipe database
- Mengetik nama database yang benar
- Mengetik "WIPE ALL DATA" dalam huruf besar
- Konfirmasi final dengan jumlah total records

### 2. Seeder Operations

#### Bims2916Toko85Seeder

```bash
php artisan db:seed --class=Bims2916Toko85Seeder
```

**Konfirmasi yang Diperlukan:**

- Konfirmasi sebelum import data dari backup SQL
- Menampilkan informasi file backup
- Progress import dengan status sukses/gagal

### 3. Web Interface Operations

#### JavaScript Confirmation Helper

```javascript
// Global helper untuk konfirmasi delete
window.confirmDelete(message, title);

// Contoh penggunaan:
wire: click = 'delete({{ $id }})';
wire: confirm = 'Yakin ingin menghapus data ini?';
```

#### Operasi yang Sudah Ada Konfirmasi:

- ✅ Delete Purchase (Pembelian)
- ✅ Delete Sale (Penjualan)
- ✅ Delete Stock Adjustment (Penyesuaian Stok)
- ✅ Delete Stock Card (Kartu Stok)
- ✅ Bulk Delete Stock Cards
- ✅ Delete Category, Product, Supplier, Customer, etc.

### 4. Best Practices

#### Sebelum Menjalankan Command Berbahaya:

1. **Backup database** terlebih dahulu
2. **Periksa environment** (production/staging)
3. **Baca konfirmasi** dengan teliti
4. **Verifikasi parameter** command
5. **Monitor progress** saat operasi berjalan

#### Recovery Plan:

1. Selalu ada backup terbaru
2. Test restore di environment staging
3. Dokumentasi langkah recovery
4. Contact admin jika ada masalah

### 5. Emergency Commands

#### Force Mode (Hanya untuk Emergency):

```bash
# Restore tanpa konfirmasi (EXTREME CAUTION)
php artisan db:restore --force

# Wipe tanpa konfirmasi (EXTREME CAUTION)
php artisan db:wipe-safe --force
```

**PENTING:** Mode force hanya digunakan dalam situasi emergency dan hanya oleh administrator senior.

### 6. Monitoring & Logging

Semua operasi database dicatat dalam log Laravel untuk audit trail.

---

## 📋 Checklist Sebelum Operasi Database

- [ ] Backup database sudah dibuat
- [ ] Environment sudah benar (bukan production)
- [ ] Command dan parameter sudah diverifikasi
- [ ] Konfirmasi sudah dibaca dan dipahami
- [ ] Recovery plan sudah disiapkan
- [ ] Admin lain sudah diberitahu (jika production)
