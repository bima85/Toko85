<?php

namespace App\Helpers;

class PageTitleHelper
{
    public static function getTitle()
    {
        $routeName = request()->route()?->getName() ?? '';

        $titles = [
            // Dashboard
            'admin.dashboard' => 'Dashboard',

            // Master Data
            'admin.users' => 'Users',
            'admin.categories' => 'Kategori',
            'admin.subcategories' => 'Subkategori',
            'admin.units' => 'Unit',
            'admin.suppliers' => 'Pemasok',
            'admin.customers' => 'Pelanggan',
            'admin.warehouses' => 'Gudang',
            'admin.stores' => 'Toko',

            // Products
            'admin.products' => 'Produk',

            // Purchases
            'admin.purchases' => 'Pembelian',

            // Sales
            'admin.sales' => 'Penjualan',

            // Stock Management
            'admin.stock-reports' => 'Laporan Stok',
            'stock-batches.index' => 'Stok Tumpukan',

            // Reports & Analytics
            'admin.transactions' => 'Historis Transaksi',
        ];

        return $titles[$routeName] ?? 'Admin';
    }
}
