<?php

use App\Livewire\Auth\Login;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Users;
use App\Livewire\Admin\Categories;
use App\Livewire\Admin\Subcategories;
use App\Livewire\Admin\Products;
use App\Livewire\Admin\Units;
use App\Livewire\Admin\Suppliers;
use App\Livewire\Admin\Customers;
use App\Livewire\Admin\Warehouses;
use App\Livewire\Admin\Stores;
use App\Livewire\Admin\Purchases;
use App\Livewire\Admin\StockReports;
use App\Livewire\Admin\Adjustments;
use App\Livewire\Admin\StockBatchIndex;
use App\Livewire\Admin\Sales;
use App\Livewire\Admin\HoldOrderManager;
use App\Livewire\Admin\TransactionHistory\HistoryIndex;
use App\Livewire\StockCard\StockCardIndex;
use App\Livewire\StockCard\StockCardShow;
use App\Livewire\StockCard\StockCardForm;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Http\Controllers\PurchaseItemController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StockBatchController;
use App\Http\Controllers\ProfitMarginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// Halaman utama / beranda

/*Route::get('/', function () {
    return view('welcome');
})->name('home');*/

// Halaman login (komponen Livewire pada root) — beri nama 'home' untuk menghindari duplikat
Route::get('/', Login::class)->name('home');

// // Halaman logout
// Route::post('/logout', function () {
//     Auth::logout();

//     request()->session()->invalidate();
//     request()->session()->regenerateToken();

//     return redirect()->route('login');
// })->middleware('auth')->name('logout');



// Halaman pencarian
Route::get('/search', function () {
    return view('search');
})->name('search');

// Halaman keranjang belanja
Route::get('/cart', function () {
    return view('cart');
})->name('cart');

// Halaman testing AdminLTE
Route::get('/test-adminlte', function () {
    return view('test-adminlte');
})->name('test-adminlte');

// Halaman dashboard admin (memerlukan autentikasi dan verifikasi email)
Route::get('dashboard', \App\Livewire\Admin\Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Grup route pengaturan pengguna (memerlukan autentikasi)
Route::middleware(['auth'])->group(function () {
    // Redirect /settings ke /settings/profile
    Route::redirect('settings', 'settings/profile');

    // Halaman pengaturan profil
    Route::get('settings/profile', Profile::class)->name('profile.edit');

    // Halaman pengaturan password
    Route::get('settings/password', Password::class)->name('user-password.edit');

    // Halaman pengaturan tampilan
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    // Halaman pengaturan autentikasi dua faktor
    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

// Grup route admin (memerlukan autentikasi, komponen akan memeriksa role saat mount)
Route::middleware(['auth'])->group(function () {
    // Halaman dashboard admin
    Route::get('admin', Dashboard::class)->name('admin.dashboard');

    // Halaman manajemen pengguna
    Route::get('admin/users', Users::class)->name('admin.users');

    // Halaman manajemen kategori
    Route::get('admin/categories', Categories::class)->name('admin.categories');

    // Halaman manajemen subkategori
    Route::get('admin/subcategories', Subcategories::class)->name('admin.subcategories');

    // Halaman manajemen produk
    Route::get('admin/products', Products::class)->name('admin.products');

    // Halaman manajemen satuan
    Route::get('admin/units', Units::class)->name('admin.units');

    // Halaman manajemen supplier
    Route::get('admin/suppliers', Suppliers::class)->name('admin.suppliers');

    // Halaman manajemen pelanggan
    Route::get('admin/customers', Customers::class)->name('admin.customers');

    // Halaman manajemen gudang
    Route::get('admin/warehouses', Warehouses::class)->name('admin.warehouses');

    // Halaman manajemen toko
    Route::get('admin/stores', Stores::class)->name('admin.stores');

    // Halaman manajemen pembelian
    Route::get('admin/purchases', Purchases::class)->name('admin.purchases');

    // Endpoint API untuk data item pembelian
    Route::get('admin/purchases/{id}/items', [PurchaseItemController::class, 'data'])->name('admin.purchases.items');

    // Halaman laporan stok
    Route::get('admin/stock-reports', StockReports::class)->name('admin.stock-reports');
    // Endpoint partial untuk refresh tabel tanpa bergantung pada Livewire client
    Route::get('admin/stock-reports/partial', [\App\Http\Controllers\Admin\StockReportController::class, 'partial'])->name('admin.stock-reports.partial');

    // Halaman penyesuaian stok
    Route::get('admin/adjustments', Adjustments::class)->name('admin.adjustments');

    // Halaman manajemen batch stok
    Route::get('admin/stock-batches', StockBatchIndex::class)->name('stock-batches.index');

    // Endpoint API untuk data batch stok (DataTable)
    Route::get('admin/stock-batches/data', [StockBatchController::class, 'data'])->name('admin.stock-batches.data');

    // Halaman manajemen hold orders
    Route::get('admin/hold-orders', HoldOrderManager::class)->name('admin.hold-orders');

    // Endpoint API untuk data total per produk (DataTable)
    Route::get('admin/stock-batches/data/total-per-product', [StockBatchController::class, 'getTotalPerProduct'])->name('admin.stock-batches.total-per-product');

    // Halaman manajemen penjualan
    Route::get('admin/sales', Sales::class)->name('admin.sales');

    // Halaman riwayat transaksi
    Route::get('admin/transactions', HistoryIndex::class)->name('admin.transactions');

    // Endpoint API untuk data transaksi
    Route::get('admin/transactions/data', [TransactionController::class, 'data'])->name('admin.transactions.data');

    // Endpoint API untuk statistik transaksi
    Route::get('admin/transactions/stats', [TransactionController::class, 'stats'])->name('admin.transactions.stats');

    // Endpoint API untuk detail transaksi
    Route::get('admin/transactions/{id}/detail', [TransactionController::class, 'detail'])->name('admin.transactions.detail');

    // Endpoint untuk export transaksi ke Excel
    Route::get('admin/transactions/export', [TransactionController::class, 'export'])->name('admin.transactions.export');

    // Endpoint API untuk testing transaksi
    Route::get('admin/transactions/test', [TransactionController::class, 'test'])->name('admin.transactions.test');

    // Halaman profit margin
    Route::get('admin/profit-margin', \App\Livewire\Admin\ProfitMargin::class)->name('admin.profit-margin');

    // Endpoint API untuk data profit margin
    Route::get('admin/profit-margin/data', [ProfitMarginController::class, 'data'])->name('admin.profit-margin.data');

    // Endpoint API untuk statistik profit margin
    Route::get('admin/profit-margin/stats', [ProfitMarginController::class, 'stats'])->name('admin.profit-margin.stats');

    // Endpoint untuk export profit margin ke Excel
    Route::get('admin/profit-margin/export', [ProfitMarginController::class, 'export'])->name('admin.profit-margin.export');

    // Check endpoint to validate customer transactions against date filters
    Route::get('admin/profit-margin/check-customer', [ProfitMarginController::class, 'checkCustomer'])->name('admin.profit-margin.check');
    Route::get('admin/profit-margin/customers', [ProfitMarginController::class, 'customers'])->name('admin.profit-margin.customers');

    // Halaman daftar kartu stok
    Route::get('stock-card', StockCardIndex::class)->name('stock-card.index');

    // Halaman tambah kartu stok baru
    Route::get('stock-card/create', StockCardForm::class)->name('stock-card.create');

    // Halaman edit kartu stok
    Route::get('stock-card/{stockCard}/edit', StockCardForm::class)->name('stock-card.edit');

    // Halaman detail kartu stok
    Route::get('stock-card/{stockCard}', StockCardShow::class)->name('stock-card.show');
});
