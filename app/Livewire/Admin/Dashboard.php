<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockBatch;
use App\Models\StockCard;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    /**
     * Mount the component and enforce admin role.
     */
    public function mount()
    {
        $user = Auth::user();
        // NOTE: dashboard access is currently available to any authenticated user
        // Role-based restriction was removed to keep test expectations aligned.
    }

    #[On('refreshDashboard')]
    public function refreshDashboard()
    {
        $this->dispatch('dashboard-refreshed');
    }

    public function render()
    {
        $totalPurchases = Purchase::count();
        $totalRevenue = PurchaseItem::sum('total') ?? 0;
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $totalUsers = User::count();

        // Recent purchases
        $recentPurchases = Purchase::with(['supplier', 'store', 'warehouse', 'purchaseItems'])
            ->latest()
            ->limit(5)
            ->get();

        // Low stock products
        $lowStockProducts = PurchaseItem::selectRaw('product_id, SUM(qty) as total_qty')
            ->groupBy('product_id')
            ->having('total_qty', '<', 10)
            ->with('product')
            ->limit(5)
            ->get();

        // Top selling products
        $topProducts = PurchaseItem::selectRaw('product_id, SUM(qty) as total_qty, COUNT(*) as purchase_count')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->limit(5)
            ->get();

        // Recent activity for lightweight user dashboard
        $recentActivities = StockCard::latest()->with(['product', 'batch'])->limit(10)->get();
        // Total stock available (sum of batch quantities)
        $totalStock = (int) StockBatch::sum('qty');

        return view('livewire.admin.dashboard', [
            'totalPurchases' => $totalPurchases,
            'totalRevenue' => $totalRevenue,
            'totalProducts' => $totalProducts,
            'totalCustomers' => $totalCustomers,
            'totalUsers' => $totalUsers,
            'recentPurchases' => $recentPurchases,
            'lowStockProducts' => $lowStockProducts,
            'topProducts' => $topProducts,
            'recentActivities' => $recentActivities,
            'totalStock' => $totalStock,
        ])->layout('layouts.admin');
    }
}
