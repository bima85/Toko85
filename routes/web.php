<?php

use App\Http\Controllers\ProfitMarginController;
use App\Http\Controllers\PurchaseItemController;
use App\Http\Controllers\StockBatchController;
use App\Http\Controllers\TransactionController;
use App\Livewire\Admin\Adjustments;
use App\Livewire\Admin\Categories;
use App\Livewire\Admin\Customers;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\HoldOrderManager;
use App\Livewire\Admin\PermissionMatrix;
use App\Livewire\Admin\Products;
use App\Livewire\Admin\Purchases;
use App\Livewire\Admin\Roles;
use App\Livewire\Admin\Sales;
use App\Livewire\Admin\StockBatchIndex;
use App\Livewire\Admin\StockReports;
use App\Livewire\Admin\Stores;
use App\Livewire\Admin\Subcategories;
use App\Livewire\Admin\Suppliers;
use App\Livewire\Admin\TransactionHistory\HistoryIndex;
use App\Livewire\Admin\Units;
use App\Livewire\Admin\Users;
use App\Livewire\Admin\Warehouses;
use App\Livewire\Auth\Login;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\StockCard\StockCardForm;
use App\Livewire\StockCard\StockCardIndex;
use App\Livewire\StockCard\StockCardShow;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// TEMP DEBUG ROUTE (public) - HAPUS SETELAH FINISH
Route::get('/_debug/purchases-html', function () {
    // Share errors bag to avoid Blade notices
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);

    // Login as dev user id 3 so mount() authorization passes
    \Illuminate\Support\Facades\Auth::loginUsingId(3);

    $component = app(\App\Livewire\Admin\Purchases::class);
    $component->mount();

    return response($component->render()->render(), 200)->header('Content-Type', 'text/html');
});

// SEMUA ROUTE LIVEWIRE HARUS MENGGUNAKAN MIDDLEWARE 'web'
Route::middleware('web')->group(function () {

    // Halaman login (root)
    Route::get('/', Login::class)->name('home');
    // Expose a named login route for tests and Livewire
    Route::get('/login', Login::class)->name('login');
    // POST login/logout handlers for test-suite compatibility
    Route::post('/login', [\App\Http\Controllers\TestAuthController::class, 'loginStore'])->name('login.store');
    Route::post('/logout', [\App\Http\Controllers\TestAuthController::class, 'logout'])->name('logout');

    // Halaman publik lainnya (jika ada Livewire di sini, tetap aman karena sudah di dalam web)
    Route::get('/search', function () {
        return view('search');
    })->name('search');

    Route::get('/cart', function () {
        return view('cart');
    })->name('cart');

    Route::get('/test-adminlte', function () {
        return view('test-adminlte');
    })->name('test-adminlte');

    // Route yang membutuhkan autentikasi
    Route::middleware('auth')->group(function () {

        // Debug route to inspect current user and roles (temporary)
        Route::get('admin/_debug/whoami', function () {
            $user = auth()->user();
            if (! $user) {
                return response()->json(['user' => null], 200);
            }

            return response()->json([
                'id' => $user->id,
                'email' => $user->email,
                'roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [],
            ], 200);
        })->name('admin._debug.whoami');

        // TEMP: Debug route to dump rendered Livewire HTML for Purchases component
        Route::get('admin/_debug/purchases-html', function () {
            $user = auth()->user();
            if (! $user) {
                abort(403);
            }

            // Ensure view errors bag exists to avoid Blade notices
            view()->share('errors', new \Illuminate\Support\ViewErrorBag);

            $component = app(\App\Livewire\Admin\Purchases::class);
            // Call mount to initialize internal state and enforce authorization check
            $component->mount();

            // Render and return raw HTML
            return response($component->render()->render())->header('Content-Type', 'text/html');
        })->name('admin._debug.purchases-html');

        // Dashboard umum
        Route::get('/dashboard', Dashboard::class)->name('dashboard');

        // ======================
        // AREA ADMIN
        // ======================
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/', Dashboard::class)->name('dashboard');
            Route::get('users', Users::class)->name('users');
            Route::get('categories', Categories::class)->name('categories');
            Route::get('subcategories', Subcategories::class)->name('subcategories');
            Route::get('products', Products::class)->name('products');
            Route::get('units', Units::class)->name('units');
            Route::get('suppliers', Suppliers::class)->name('suppliers');
            Route::get('customers', Customers::class)->name('customers');
            Route::get('warehouses', Warehouses::class)->name('warehouses');
            Route::get('stores', Stores::class)->name('stores');
            Route::get('roles', Roles::class)->name('roles');
            Route::get('permissions', PermissionMatrix::class)->name('permissions');
            Route::get('purchases', Purchases::class)->name('purchases');
            Route::get('purchases/{id}/items', [PurchaseItemController::class, 'data'])->name('purchases.items');
            Route::get('purchases/{id}/print', [PurchaseItemController::class, 'print'])->name('purchases.print');

            Route::get('stock-reports', StockReports::class)->name('stock-reports');
            Route::get('stock-reports/partial', [\App\Http\Controllers\Admin\StockReportController::class, 'partial'])->name('stock-reports.partial');

            Route::get('adjustments', Adjustments::class)->name('adjustments');
            Route::get('stock-batches', StockBatchIndex::class)->name('stock-batches.index');
            Route::get('stock-batches/data', [StockBatchController::class, 'data'])->name('stock-batches.data');
            Route::get('stock-batches/data/total-per-product', [StockBatchController::class, 'getTotalPerProduct'])->withoutMiddleware('auth')->name('stock-batches.total-per-product');

            Route::get('hold-orders', HoldOrderManager::class)->name('hold-orders');
            Route::get('sales', Sales::class)->name('sales');
            Route::get('delivery-notes', \App\Livewire\Admin\DeliveryNotesIndex::class)->name('delivery-notes');
            Route::get('delivery-notes/{sale}/print', function (\App\Models\Sale $sale) {
                return view('admin.delivery-notes.print', compact('sale'));
            })->name('delivery-notes.print');
            Route::get('transactions', HistoryIndex::class)->name('transactions');
            // Realtime transactions management page (penjualan & pembelian)
            Route::get('transactions/manage', \App\Livewire\Admin\Transactions::class)->name('transactions.manage');

            Route::get('transactions/data', [TransactionController::class, 'data'])->name('transactions.data');
            Route::get('transactions/stats', [TransactionController::class, 'stats'])->name('transactions.stats');
            Route::get('transactions/{id}/detail', [TransactionController::class, 'detail'])->name('transactions.detail');
            Route::get('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
            Route::get('transactions/test', [TransactionController::class, 'test'])->name('transactions.test');

            Route::get('profit-margin', \App\Livewire\Admin\ProfitMargin::class)->name('profit-margin');
            Route::get('profit-margin/data', [ProfitMarginController::class, 'data'])->name('profit-margin.data');
            Route::get('profit-margin/stats', [ProfitMarginController::class, 'stats'])->name('profit-margin.stats');
            Route::get('profit-margin/export', [ProfitMarginController::class, 'export'])->name('profit-margin.export');
            Route::get('profit-margin/check-customer', [ProfitMarginController::class, 'checkCustomer'])->name('profit-margin.check');
            // Quick Purchase & Sell (admin utility)
            Route::get('quick-purchase-sell', \App\Livewire\Admin\QuickPurchaseSell::class)->name('quick-purchase-sell');
            Route::get('profit-margin/customers', [ProfitMarginController::class, 'customers'])->name('profit-margin.customers');
        });

        // ======================
        // SETTINGS
        // ======================
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::redirect('/', 'profile');
            Route::get('profile', Profile::class)->name('profile');
            Route::get('password', Password::class)->name('password');
            Route::get('appearance', Appearance::class)->name('appearance');

            Route::get('two-factor', TwoFactor::class)
                ->middleware(
                    when(
                        Features::canManageTwoFactorAuthentication()
                            && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                        ['password.confirm'],
                        []
                    )
                )
                ->name('two-factor');
        });

        // ======================
        // STOCK CARD
        // ======================
        Route::prefix('stock-card')->name('stock-card.')->group(function () {
            Route::get('/', StockCardIndex::class)->name('index');
            Route::get('create', StockCardForm::class)->name('create');
            Route::get('{stockCard}/edit', StockCardForm::class)->name('edit');
            Route::get('{stockCard}', StockCardShow::class)->name('show');
        });
    });

    // Route::get('/clear-cache-secret', function () {
    // Artisan::call('view:clear');
    // Artisan::call('optimize:clear');
    // return "Cache dibersihkan!";
    // });
});

// Fallback named routes used by some views/tests. Define only if not present.
if (! Route::has('user-password.edit')) {
    Route::any('/user/password/edit', function () {
        return redirect('/');
    })->name('user-password.edit');
}

if (! Route::has('two-factor.show')) {
    Route::get('/settings/two-factor', App\Livewire\Settings\TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['auth', 'password.confirm'],
                ['auth']
            )
        )
        ->name('two-factor.show');
}

if (! Route::has('profile.edit')) {
    Route::any('/profile/edit', function () {
        return redirect('/');
    })->name('profile.edit');
}

if (! Route::has('appearance.edit')) {
    Route::any('/settings/appearance/edit', function () {
        return redirect('/settings/appearance');
    })->name('appearance.edit');
}

// Two-factor challenge route (requires auth) used by tests
if (! Route::has('two-factor.login')) {
    Route::get('/two-factor-challenge', function () {
        return response('Two Factor Challenge');
    })->middleware('auth')->name('two-factor.login');
}

// Some views expect un-prefixed admin route names — provide short aliases
if (! Route::has('stock-batches.index')) {
    Route::get('/admin/stock-batches', StockBatchIndex::class)->middleware('auth')->name('stock-batches.index');
}

// Also provide a non-prefixed path for views that call the route without 'admin.' prefix
if (! Route::has('stock-batches.index')) {
    Route::get('/stock-batches', StockBatchIndex::class)->middleware('auth')->name('stock-batches.index');
}

if (! Route::has('admin.stock-batches.index')) {
    Route::get('/admin/stock-batches', StockBatchIndex::class)->middleware('auth')->name('admin.stock-batches.index');
}
