<?php

namespace App\Livewire\Admin;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\User;
use App\Models\Customer;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class Dashboard extends Component
{
   /**
    * Mount the component and enforce admin role.
    */
   public function mount()
   {
      $user = Auth::user();
      // ensure only admins can access
      abort_unless($user && method_exists($user, 'hasRole') && $user->hasRole('admin'), 403);
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

      return view('livewire.admin.dashboard', [
         'totalPurchases' => $totalPurchases,
         'totalRevenue' => $totalRevenue,
         'totalProducts' => $totalProducts,
         'totalCustomers' => $totalCustomers,
         'totalUsers' => $totalUsers,
         'recentPurchases' => $recentPurchases,
         'lowStockProducts' => $lowStockProducts,
         'topProducts' => $topProducts,
      ])->layout('layouts.admin');
   }
}
