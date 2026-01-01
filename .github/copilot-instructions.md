# AI Coding Agent Instructions for Shop85

## Architecture Overview

This is a Laravel 12 + Livewire inventory management system for retail shops. Core entities: Products (with batches), Sales, Purchases, StockAdjustments. Stock tracking uses FIFO batches with audit trail via StockCard model.

**Key Data Flow**: Transactions (Sale/Purchase/Adjustment) → Update StockBatch quantities → Create StockCard entries for traceability.

**UI Pattern**: All admin interfaces use Livewire components in `app/Livewire/Admin/`. Reactive forms with pagination, search, modals.

## Essential Patterns

- **Stock Movements**: Always record via StockCard with polymorphic `reference_type`/`reference_id` (e.g., `App\Models\Sale`). Use types: 'purchase', 'sale', 'adjustment', 'hold', 'cancel_hold'.
- **Batch Management**: Products have multiple StockBatches (FIFO). Hold feature creates separate 'hold' status batches for pending sales.
- **Hold Stock Logic**: See `app/Services/HoldStockService.php` - moves stock to HOLD batches, tracks via StockCard, allows cancel/complete.
- **Exports**: Use Maatwebsite Excel in `app/Exports/` classes. Reference: `StockReportExport.php` for complex queries with joins.

## Development Workflow

- **Setup**: Run `composer run setup` (installs deps, copies .env, migrates, builds assets).
- **Development**: Use `npm run dev:all` for concurrent Laravel server + Vite dev server.
- **Database Reset**: `php artisan migrate:fresh --seed` to reset with sample data.
- **Testing**: `php artisan test` (PHPUnit). Focus on Feature tests for Livewire components.
- **Code Style**: Use Prettier for JS/Blade (`npm run format`). Pint for PHP.

## Conventions

- **Naming**: Indonesian field names (e.g., `nama_produk`, `tanggal_penjualan`). Use `kode_produk` for product codes.
- **Relationships**: Standard Laravel, but check StockCard's polymorphic `reference()` method.
- **Livewire**: Use `#[Layout('layouts.admin')]` for admin pages. Pagination with Bootstrap theme.
- **Permissions**: Use Spatie Laravel Permission for role-based access.

## Key Files to Reference

- `app/Models/StockCard.php`: Audit trail pattern
- `app/Services/HoldStockService.php`: Hold stock business logic
- `app/Livewire/Admin/StockReports.php`: Complex Livewire with tabs, modals, exports
- `HOLD_STOCK_README.md`: Feature documentation

## Common Pitfalls

- Always update StockCard when modifying stock quantities.
- Hold batches use `status = 'hold'` and special naming (`- HOLD #sale_id`).
- Use DB transactions for stock operations (see HoldStockService examples).</content>
  <parameter name="filePath">c:/Herd/Shop85/.github/copilot-instructions.md
