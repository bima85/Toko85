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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');
Route::get('/login', Login::class)->name('login');
Route::get('/search', function () {
    return view('search');
})->name('search');
Route::get('/cart', function () {
    return view('cart');
})->name('cart');
Route::get('/test-adminlte', function () {
    return view('test-adminlte');
})->name('test-adminlte');
// Map /dashboard to the Admin Livewire Dashboard (keeps same route name)
Route::get('dashboard', \App\Livewire\Admin\Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

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

// Admin area: Livewire components (mount checks role). Single group for admin routes.
Route::middleware(['auth'])->group(function () {
    // Admin dashboard (Livewire component will check role on mount)
    Route::get('admin', Dashboard::class)->name('admin.dashboard');

    // Admin users management
    Route::get('admin/users', Users::class)->name('admin.users');

    // Admin categories management
    Route::get('admin/categories', Categories::class)->name('admin.categories');

    // Admin subcategories management
    Route::get('admin/subcategories', Subcategories::class)->name('admin.subcategories');

    // Admin products management
    Route::get('admin/products', Products::class)->name('admin.products');

    // Admin units management
    Route::get('admin/units', Units::class)->name('admin.units');

    // Admin suppliers management
    Route::get('admin/suppliers', Suppliers::class)->name('admin.suppliers');

    // Admin customers management
    Route::get('admin/customers', Customers::class)->name('admin.customers');

    // Admin warehouses management
    Route::get('admin/warehouses', Warehouses::class)->name('admin.warehouses');

    // Admin stores management
    Route::get('admin/stores', Stores::class)->name('admin.stores');

    // Admin purchases management
    Route::get('admin/purchases', Purchases::class)->name('admin.purchases');
    Route::get('admin/purchases/{id}/items', [PurchaseItemController::class, 'data'])->name('admin.purchases.items');

    // Admin stock reports
    Route::get('admin/stock-reports', StockReports::class)->name('admin.stock-reports');

    // Admin adjustments
    Route::get('admin/adjustments', Adjustments::class)->name('admin.adjustments');

    // Admin stock batch management
    Route::get('admin/stock-batches', StockBatchIndex::class)->name('stock-batches.index');

    // Admin sales management
    Route::get('admin/sales', Sales::class)->name('admin.sales');

    // Admin transaction history
    Route::get('admin/transactions', HistoryIndex::class)->name('admin.transactions');
    Route::get('admin/transactions/data', [TransactionController::class, 'data'])->name('admin.transactions.data');
    Route::get('admin/transactions/test', [TransactionController::class, 'test'])->name('admin.transactions.test');

    // Stock Card management routes
    Route::get('stock-card', StockCardIndex::class)->name('stock-card.index');
    Route::get('stock-card/create', StockCardForm::class)->name('stock-card.create');
    Route::get('stock-card/{stockCard}/edit', StockCardForm::class)->name('stock-card.edit');
    Route::get('stock-card/{stockCard}', StockCardShow::class)->name('stock-card.show');
});
