<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockBatch;
use App\Services\StockBatchService;
use Illuminate\Http\Request;

class StockBatchController extends Controller
{
   protected StockBatchService $stockBatchService;

   public function __construct(StockBatchService $stockBatchService)
   {
      $this->stockBatchService = $stockBatchService;
   }

   /**
    * Display a listing of stock batches
    */
   public function index()
   {
      return view('stock-batch.index');
   }

   /**
    * Show the form for creating a new stock batch
    */
   public function create()
   {
      $products = Product::all();
      return view('stock-batch.create', compact('products'));
   }

   /**
    * Store a newly created stock batch in storage
    */
   public function store(Request $request)
   {
      $validated = $request->validate([
         'product_id' => 'required|exists:products,id',
         'location_type' => 'required|in:store,warehouse',
         'nama_tumpukan' => 'required|string|max:255',
         'qty' => 'required|numeric|min:0.01',
         'note' => 'nullable|string',
      ]);

      try {
         $this->stockBatchService->addStock(
            $validated['product_id'],
            $validated['location_type'],
            $validated['nama_tumpukan'],
            $validated['qty'],
            note: $validated['note'] ?? null
         );

         return redirect()->route('stock-batches.index')
            ->with('success', 'Stok batch berhasil ditambahkan');
      } catch (\Exception $e) {
         return back()->with('error', $e->getMessage());
      }
   }

   /**
    * Display the specified stock batch
    */
   public function show(StockBatch $stockBatch)
   {
      return view('stock-batch.show', compact('stockBatch'));
   }

   /**
    * Show the form for editing the specified stock batch
    */
   public function edit(StockBatch $stockBatch)
   {
      return view('stock-batch.edit', compact('stockBatch'));
   }

   /**
    * Update the specified stock batch in storage
    */
   public function update(Request $request, StockBatch $stockBatch)
   {
      $validated = $request->validate([
         'nama_tumpukan' => 'required|string|max:255',
         'qty' => 'required|numeric|min:0.01',
      ]);

      $stockBatch->update($validated);

      return redirect()->route('stock-batches.show', $stockBatch)
         ->with('success', 'Stok batch berhasil diperbarui');
   }

   /**
    * Remove the specified stock batch from storage
    */
   public function destroy(StockBatch $stockBatch)
   {
      $stockBatch->delete();

      return redirect()->route('stock-batches.index')
         ->with('success', 'Stok batch berhasil dihapus');
   }

   /**
    * Reduce stock from batch
    */
   public function reduceStock(Request $request, StockBatch $stockBatch)
   {
      $validated = $request->validate([
         'qty' => 'required|numeric|min:0.01',
         'note' => 'nullable|string',
      ]);

      try {
         $this->stockBatchService->reduceStock(
            $stockBatch,
            $validated['qty'],
            $validated['note'] ?? null
         );

         return redirect()->route('stock-batches.index')
            ->with('success', 'Stok batch berhasil dikurangi');
      } catch (\Exception $e) {
         return back()->with('error', $e->getMessage());
      }
   }

   /**
    * Move stock between batches/locations
    */
   public function moveStock(Request $request, StockBatch $stockBatch)
   {
      $validated = $request->validate([
         'to_location_type' => 'required|in:store,warehouse',
         'to_nama_tumpukan' => 'required|string|max:255',
         'qty' => 'required|numeric|min:0.01',
         'note' => 'nullable|string',
      ]);

      try {
         $this->stockBatchService->moveStock(
            $stockBatch,
            $validated['to_location_type'],
            $validated['to_nama_tumpukan'],
            $validated['qty'],
            note: $validated['note'] ?? null
         );

         return redirect()->route('stock-batches.index')
            ->with('success', 'Stok berhasil dipindahkan');
      } catch (\Exception $e) {
         return back()->with('error', $e->getMessage());
      }
   }
}
