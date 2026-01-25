<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class QuickPurchaseSell extends Component
{
    public $items = [];

    public $stores = [];

    public $warehouses = [];

    public $location_type = 'store';

    public $location_id = null;

    protected $rules = [
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.purchase_qty' => 'required|numeric|min:0.01',
        'items.*.purchase_price' => 'nullable|numeric|min:0',
        'items.*.sale_qty' => 'required|numeric|min:0.01',
        'items.*.sale_price' => 'nullable|numeric|min:0',
    ];

    public function mount()
    {
        $this->items = [
            ['product_id' => null, 'purchase_qty' => 1, 'purchase_price' => 0, 'sale_qty' => 1, 'sale_price' => 0],
        ];

        $this->stores = \App\Models\Store::orderBy('nama_toko')->get();
        $this->warehouses = \App\Models\Warehouse::orderBy('nama_gudang')->get();
    }

    public function addItem()
    {
        $this->items[] = ['product_id' => null, 'purchase_qty' => 1, 'purchase_price' => 0, 'sale_qty' => 1, 'sale_price' => 0];
    }

    public function removeItem($index)
    {
        array_splice($this->items, $index, 1);
    }

    public function submit()
    {
        $this->validate();

        $purchaseMeta = [
            'supplier_id' => null,
            'no_invoice' => 'IMP-'.now()->format('YmdHis'),
            'tanggal_pembelian' => now(),
            'user_id' => Auth::id(),
        ];

        $saleMeta = [
            'customer_id' => null,
            'no_invoice' => 'IS-'.now()->format('YmdHis'),
            'tanggal_penjualan' => now(),
            'user_id' => Auth::id(),
            'store_id' => $this->location_type === 'store' ? $this->location_id : null,
            'warehouse_id' => $this->location_type === 'warehouse' ? $this->location_id : null,
        ];

        $purchaseItems = [];
        $saleItems = [];

        foreach ($this->items as $it) {
            $purchaseItems[] = [
                'product_id' => $it['product_id'],
                'qty' => $it['purchase_qty'],
                'harga_beli' => $it['purchase_price'],
            ];

            $saleItems[] = [
                'product_id' => $it['product_id'],
                'qty' => $it['sale_qty'],
                'harga_jual' => $it['sale_price'],
            ];
        }

        $service = app(\App\Services\ImmediatePurchaseSaleService::class);

        try {
            $result = $service->process([
                'meta' => $purchaseMeta,
                'items' => $purchaseItems,
            ], [
                'meta' => $saleMeta,
                'items' => $saleItems,
            ]);

            session()->flash('message', 'Immediate Purchase & Sale processed. Purchase ID: '.$result['purchase']->id.', Sale ID: '.$result['sale']->id);

            return redirect()->route('admin.purchases');
        } catch (\Exception $e) {
            Log::error('ImmediatePurchaseSale failed: '.$e->getMessage());
            $this->addError('items', 'Gagal memproses transaksi: '.$e->getMessage());
        }
    }

    public function render()
    {
        $products = \App\Models\Product::orderBy('nama_produk')->limit(200)->get();

        return view('livewire.admin.purchases.quick-purchase-sell', [
            'products' => $products,
        ]);
    }
}
