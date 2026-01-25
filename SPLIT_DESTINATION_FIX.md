# Test Summary: Split Destination Stock Creation

## Fix Implemented

### Problem

When creating a purchase with both:

- **qty** (for Toko) = 15 units
- **qty_gudang** (for Gudang) = 20 units

Previously, only the **gudang** stock was created (if/elseif logic was mutually exclusive).

### Solution

Changed the stock creation logic from **if/elseif** to **if/if**, allowing both Toko and Gudang stock to be created simultaneously.

### Code Changes

**File: `app/Livewire/Admin/Purchases.php`**

1. **Auto-default Location Logic** (Line ~410-430)
    - Now checks actual `qty` and `qty_gudang` values (not just `destination_type`)
    - Auto-sets `store_id` to first store if any item has `qty > 0`
    - Auto-sets `warehouse_id` to first warehouse if any item has `qty_gudang > 0`

2. **Stock Creation Logic** (Changed if/elseif to if/if)
    - **Process TOKO**: `if ($this->store_id && ($item['qty'] ?? 0) > 0)`
    - **Process GUDANG**: `if ($this->warehouse_id && ($item['qty_gudang'] ?? 0) > 0)`
    - Both conditions are now independent - not mutually exclusive!

### Test Results

#### Scenario 1: Toko Only (qty = 10)

- ✅ Store Batch created: 10 units
- Result: Stock correctly routed to Toko only

#### Scenario 2: Gudang Only (qty_gudang = 20)

- ✅ Warehouse Batch created: 20 units
- Result: Stock correctly routed to Gudang only

#### Scenario 3: Split (qty = 15 + qty_gudang = 20)

- ✅ Store Batch created: 15 units
- ✅ Warehouse Batch created: 20 units
- Result: **BOTH** stocks created successfully!

## How to Test in UI

### Via Purchases Form

1. Navigate to: `http://shop85.test/admin/purchases`
2. Click **"Tambah Pembelian"** (Add Purchase)
3. Fill in:
    - Supplier: Select any supplier
    - Date: Today
    - Product: Any product
    - Qty (Toko): **15**
    - Qty Gudang: **20**
    - **IMPORTANT**: Do NOT select location (leave dropdown empty to test auto-default)
4. Click **Save**
5. Verify:
    - Purchase created successfully
    - Go to **Stock Reports** → Check both "Stok Toko" and "Stok Gudang" tabs
    - Product should appear in BOTH tabs with correct quantities

### Expected Results

- Stok Toko tab: Product with 15 units
- Stok Gudang tab: Product with 20 units

## Database Validation

```bash
# Check StockBatch records
php artisan tinker
>>> StockBatch::where('product_id', 23)->latest('id')->take(4)->get();

# Should show 4 recent batches (2 from test scenarios, 2 from previous tests)
# with mix of location_type 'store' and 'warehouse'
```

## Key Implementation Details

### Why This Works

1. **Auto-default location** handles the case where user forgets to select location
2. **Separate if conditions** allow both Toko and Gudang quantities to be processed
3. **qty vs qty_gudang** distinction is clear:
    - `qty` always goes to Toko (store)
    - `qty_gudang` always goes to Gudang (warehouse)

### Backward Compatibility

- Single qty purchases (qty > 0, qty_gudang = 0) still work
- Single gudang purchases (qty = 0, qty_gudang > 0) still work
- Existing form behavior unchanged
