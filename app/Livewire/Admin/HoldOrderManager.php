<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBatch;
use App\Models\StockCard;
use App\Services\HoldStockService;
use Illuminate\Support\Facades\DB;

class HoldOrderManager extends Component
{
    use WithPagination;

    public $activeTab = 'active_holds'; // active_holds, completed, cancelled
    public $search = '';
    public $selectedOrder = null;
    public $showConfirmDialog = false;
    public $actionType = null; // 'complete' atau 'cancel'
    public $holdType = null; // 'order' atau 'batch'

    protected $holdStockService;

    public function boot()
    {
        $this->holdStockService = app(HoldStockService::class);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Get active hold orders & stock batches combined
     */
    public function getActiveHolds()
    {
        // Get hold orders
        $holdOrders = Sale::where('status', 'hold')
            ->with(['customer', 'saleItems.product', 'user'])
            ->when($this->search, function ($q) {
                $q->where('no_invoice', 'LIKE', "%{$this->search}%")
                    ->orWhereHas('customer', function ($q) {
                        $q->where('nama_customer', 'LIKE', "%{$this->search}%");
                    });
            })
            ->get()
            ->map(function ($order) {
                $order->hold_type = 'order';
                return $order;
            });

        // Get hold stock batches
        $holdBatches = StockBatch::where('status', 'hold')
            ->with('product')
            ->when($this->search, function ($q) {
                $q->whereHas('product', function ($subQ) {
                    $subQ->where('nama_produk', 'LIKE', "%{$this->search}%")
                        ->orWhere('kode_produk', 'LIKE', "%{$this->search}%");
                });
            })
            ->get()
            ->map(function ($batch) {
                $batch->hold_type = 'batch';
                return $batch;
            });

        // Combine & sort
        $combined = collect($holdOrders)
            ->merge($holdBatches)
            ->sortByDesc('created_at')
            ->values();

        // Manual pagination
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $total = $combined->count();
        $items = $combined->forPage($currentPage, $perPage);

        return new \Illuminate\Pagination\Paginator(
            $items,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    /**
     * Get completed orders from hold
     */
    public function getCompletedOrders()
    {
        return Sale::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->with(['customer', 'saleItems.product'])
            ->when($this->search, function ($q) {
                $q->where('no_invoice', 'LIKE', "%{$this->search}%");
            })
            ->orderByDesc('completed_at')
            ->paginate(10);
    }

    /**
     * Get cancelled orders
     */
    public function getCancelledOrders()
    {
        return Sale::where('status', 'cancelled')
            ->whereNotNull('cancelled_at')
            ->with(['customer', 'saleItems.product'])
            ->when($this->search, function ($q) {
                $q->where('no_invoice', 'LIKE', "%{$this->search}%");
            })
            ->orderByDesc('cancelled_at')
            ->paginate(10);
    }

    /**
     * Tampilkan detail order
     */
    public function viewOrder($orderId)
    {
        $this->selectedOrder = Sale::with([
            'customer',
            'saleItems.product',
            'user'
        ])->find($orderId);

        // Ambil info hold stok
        if ($this->selectedOrder) {
            $this->selectedOrder->hold_info = $this->getHoldInfo($orderId);
        }
    }

    /**
     * Ambil informasi stok yang di-hold untuk order ini
     */
    private function getHoldInfo($orderId)
    {
        return StockBatch::where('nama_tumpukan', 'LIKE', "%HOLD #{$orderId}%")
            ->get()
            ->map(function ($batch) {
                return [
                    'product_name' => $batch->product->nama_produk,
                    'tumpukan' => preg_replace("/ - HOLD #\d+/", "", $batch->nama_tumpukan),
                    'qty_hold' => $batch->qty,
                    'held_at' => $batch->created_at,
                ];
            });
    }

    /**
     * Tampilkan dialog konfirmasi untuk complete/cancel
     */
    public function confirmAction($orderId, $action)
    {
        $this->selectedOrder = Sale::find($orderId);
        $this->actionType = $action;
        $this->showConfirmDialog = true;
    }

    /**
     * Confirm action for hold stock batches
     */
    public function confirmBatchAction($batchId, $action)
    {
        $batch = StockBatch::find($batchId);
        if ($batch) {
            $this->selectedOrder = $batch;
            $this->holdType = 'batch';
            $this->actionType = $action;
            $this->showConfirmDialog = true;
        }
    }

    /**
     * Selesaikan hold → complete
     */
    public function completeHold()
    {
        try {
            if ($this->holdType === 'batch') {
                $this->selectedOrder->update(['status' => 'aktual']);
                $message = '✅ Hold stock batch berhasil diaktualkan!';
            } else {
                $result = $this->holdStockService->completeHold($this->selectedOrder);
                $message = $result['message'];
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message,
            ]);

            $this->showConfirmDialog = false;
            $this->selectedOrder = null;
            $this->holdType = null;
            $this->dispatch('refresh-page');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '❌ Gagal: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Batalkan hold → cancel
     */
    public function cancelHold()
    {

        try {
            if ($this->holdType === 'batch') {
                // Catat di StockCard (tipe: cancel_hold_batch manual)
                StockCard::create([
                    'product_id' => $this->selectedOrder->product_id,
                    'batch_id' => $this->selectedOrder->id,
                    'type' => 'cancel_hold',
                    'qty' => $this->selectedOrder->qty,
                    'from_location' => $this->selectedOrder->nama_tumpukan,
                    'to_location' => $this->selectedOrder->nama_tumpukan,
                    'reference_type' => StockBatch::class,
                    'reference_id' => $this->selectedOrder->id,
                    'note' => 'Hold batch manual dibatalkan',
                ]);
                $this->selectedOrder->delete();
                $message = '✅ Hold stock batch berhasil dihapus!';
            } else {
                $result = $this->holdStockService->cancelHold($this->selectedOrder);
                $message = $result['message'];
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message,
            ]);

            $this->showConfirmDialog = false;
            $this->selectedOrder = null;
            $this->holdType = null;
            $this->dispatch('refresh-page');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '❌ Gagal: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get summary statistik hold
     */
    public function getHoldSummary()
    {
        return [
            'active_holds' => Sale::where('status', 'hold')->count(),
            'total_hold_qty' => StockBatch::where('status', 'hold')->sum('qty'),
            'hold_stock_batches' => StockBatch::where('status', 'hold')
                ->with('product')
                ->orderByDesc('created_at')
                ->get(),
            'completed_today' => Sale::where('status', 'completed')
                ->whereDate('completed_at', today())
                ->count(),
            'cancelled_today' => Sale::where('status', 'cancelled')
                ->whereDate('cancelled_at', today())
                ->count(),
        ];
    }

    public function render()
    {
        // Get orders hanya jika bukan tab hold_stock_batches
        $orders = match ($this->activeTab) {
            'active_holds' => $this->getActiveHolds(),
            'completed' => $this->getCompletedOrders(),
            'cancelled' => $this->getCancelledOrders(),
        };

        return view('livewire.admin.hold-order-manager', [
            'orders' => $orders,
            'summary' => $this->getHoldSummary(),
        ]);
    }
}
