<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\StockBatch;
use App\Models\Category;
use App\Models\Subcategory;
use App\Services\StockBatchService;
use Illuminate\Support\Str;
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

   // Category / Subcategory inline add
   // Untyped so sentinel selection like '__add__' can be used in options
   public $categoryId = null;
   public $subcategoryId = null;
   public string $newCategoryName = '';
   public string $newSubcategoryName = '';
   public string $newCategoryCode = '';
   public string $newSubcategoryCode = '';
   public bool $showCategoryModal = false;
   public bool $showSubcategoryModal = false;

   public function mount()
   {
      $this->stockBatchService = app(StockBatchService::class);
   }

   public function updatedCategoryId($value)
   {
      if ($value === '__add__') {
         $this->showCategoryModal = true;
         $this->categoryId = null;
      }
   }

   public function updatedSubcategoryId($value)
   {
      if ($value === '__add__') {
         $this->showSubcategoryModal = true;
         $this->subcategoryId = null;
      }
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

      $categories = Category::all();
      if ($this->categoryId) {
         $subcategories = Subcategory::where('category_id', $this->categoryId)->get();
      } else {
         $subcategories = Subcategory::all();
      }

      return view('livewire.admin.stock-batch-form', [
         'products' => $products,
         'batches' => $batches,
         'locations' => $locations,
         'categories' => $categories,
         'subcategories' => $subcategories,
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
         'categoryId' => 'nullable|exists:categories,id',
         'subcategoryId' => 'nullable|exists:subcategories,id',
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

   public function createCategory()
   {
      $this->validate([
         'newCategoryName' => 'required|string|max:255',
         'newCategoryCode' => 'nullable|string|max:50|unique:categories,kode_kategori',
      ], [
         'newCategoryCode.unique' => 'Kode kategori sudah digunakan',
      ]);

      $kode = $this->newCategoryCode ?: strtoupper(Str::slug($this->newCategoryName, '_'));

      $cat = Category::create(['nama_kategori' => $this->newCategoryName, 'kode_kategori' => $kode]);
      session()->flash('success', "Kategori '{$cat->nama_kategori}' berhasil dibuat.");
      $this->categoryId = $cat->id;
      $this->newCategoryName = '';
      $this->newCategoryCode = '';
      $this->showCategoryModal = false;
   }

   public function createSubcategory()
   {
      $this->validate([
         'newSubcategoryName' => 'required|string|max:255',
         'newSubcategoryCode' => 'nullable|string|max:50|unique:subcategories,kode_subkategori',
         'categoryId' => 'required|exists:categories,id',
      ], [
         'categoryId.required' => 'Pilih kategori terlebih dahulu.',
         'newSubcategoryCode.unique' => 'Kode subkategori sudah digunakan',
      ]);

      $kode = $this->newSubcategoryCode ?: strtoupper(Str::slug($this->newSubcategoryName, '_'));

      $sub = Subcategory::create([
         'nama_subkategori' => $this->newSubcategoryName,
         'kode_subkategori' => $kode,
         'category_id' => $this->categoryId,
      ]);

      session()->flash('success', "Subkategori '{$sub->nama_subkategori}' berhasil dibuat.");
      $this->subcategoryId = $sub->id;
      $this->newSubcategoryName = '';
      $this->newSubcategoryCode = '';
      $this->showSubcategoryModal = false;
   }

   private function resetForm()
   {
      $this->reset(['productId', 'batchId', 'locationType', 'namaTumpukan', 'qty', 'note', 'toLocationType', 'toNamaTumpukan', 'categoryId', 'subcategoryId', 'newCategoryCode', 'newSubcategoryCode']);
   }
}
