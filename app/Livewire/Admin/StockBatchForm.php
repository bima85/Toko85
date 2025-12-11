<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\StockBatch;
use App\Services\StockBatchService;
use Livewire\Component;

class StockBatchForm extends Component
{
   protected StockBatchService $stockBatchService;

   public string $actionType = 'add'; // add, reduce, move
   public ?int $productId = null;
   public ?int $batchId = null;
   public string $locationType = 'store';
   public string $namaTumpukan = 'Tumpukan';
   public float $qty = 0;
   public string $note = '';
   public string $toLocationType = 'warehouse';
   public string $toNamaTumpukan = 'Tumpukan';

   public function mount()
   {
      $this->stockBatchService = app(StockBatchService::class);
   }

   public function getSelectedProductProperty()
   {
      return $this->productId ? Product::find($this->productId) : null;
   }

   public function render()
   {
      $products = Product::all();
      $batches = [];

      if ($this->productId) {
         $batches = StockBatch::active()
            ->where('product_id', $this->productId)
            ->get();
      }

      $locations = ['store' => 'Toko', 'warehouse' => 'Gudang'];

      return view('livewire.admin.stock-batch-form', [
         'products' => $products,
         'batches' => $batches,
         'locations' => $locations,
      ]);
   }

   public function submit()
   {
      match ($this->actionType) {
         'add' => $this->handleAdd(),
         'reduce' => $this->handleReduce(),
         'move' => $this->handleMove(),
         default => null,
      };
   }

   private function handleAdd()
   {
      $this->validate([
         'productId' => 'required|exists:products,id',
         'locationType' => 'required|in:store,warehouse',
         'namaTumpukan' => 'required|string|max:255',
         'qty' => 'required|numeric|min:0.01',
      ]);

      try {
         // Selalu buat batch baru - tidak menggabungkan dengan batch yang sudah ada
         $this->stockBatchService->addStock(
            $this->productId,
            $this->locationType,
            $this->namaTumpukan,
            $this->qty,
            note: $this->note ?: null
         );

         session()->flash('success', 'Stok berhasil ditambahkan!');
         $this->resetForm();
      } catch (\Exception $e) {
         session()->flash('error', $e->getMessage());
      }
   }

   private function handleReduce()
   {
      $this->validate([
         'batchId' => 'required|exists:stock_batches,id',
         'qty' => 'required|numeric|min:0.01',
      ]);

      try {
         $batch = StockBatch::findOrFail($this->batchId);
         $this->stockBatchService->reduceStock(
            $batch,
            $this->qty,
            $this->note ?: null
         );

         session()->flash('success', 'Stok berhasil dikurangi!');
         $this->resetForm();
      } catch (\Exception $e) {
         session()->flash('error', $e->getMessage());
      }
   }

   private function handleMove()
   {
      $this->validate([
         'batchId' => 'required|exists:stock_batches,id',
         'toLocationType' => 'required|in:store,warehouse',
         'toNamaTumpukan' => 'required|string|max:255',
         'qty' => 'required|numeric|min:0.01',
      ]);

      try {
         $batch = StockBatch::findOrFail($this->batchId);
         $this->stockBatchService->moveStock(
            $batch,
            $this->toLocationType,
            $this->toNamaTumpukan,
            $this->qty,
            note: $this->note ?: null
         );

         session()->flash('success', 'Stok berhasil dipindahkan!');
         $this->resetForm();
      } catch (\Exception $e) {
         session()->flash('error', $e->getMessage());
      }
   }

   private function resetForm()
   {
      $this->reset(['productId', 'batchId', 'locationType', 'namaTumpukan', 'qty', 'note', 'toLocationType', 'toNamaTumpukan']);
   }
}
