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
    * Get data for DataTable
    */
   public function data(Request $request)
   {
      $query = StockBatch::with('product', 'product.category', 'product.subcategory')
         ->where('qty', '>', 0)
         ->latest('updated_at');

      // Filter berdasarkan search
      if ($request->has('search') && $request->search) {
         $query->whereHas('product', function ($q) use ($request) {
            $q->where('nama_produk', 'like', '%' . $request->search . '%')
               ->orWhere('kode_produk', 'like', '%' . $request->search . '%');
         });
      }

      // Filter berdasarkan lokasi
      if ($request->has('location') && $request->location) {
         $query->where('location_type', $request->location);
      }

      return datatables()->eloquent($query)
         ->addIndexColumn()
         ->editColumn('nama_produk', function ($batch) {
            return $batch->product->nama_produk ?? '-';
         })
         ->editColumn('lokasi', function ($batch) {
            if ($batch->location_type === 'store') {
               return 'Toko';
            } else {
               return 'Gudang';
            }
         })
         ->editColumn('qty', function ($batch) {
            return number_format($batch->qty, 2, ',', '.');
         })
         ->addColumn('satuan', function ($batch) {
            return $batch->product->satuan ?? '-';
         })
         ->editColumn('created_at', function ($batch) {
            return $batch->created_at->format('d/m/Y H:i');
         })
         ->addColumn('action', function ($batch) {
            $editBtn = '<a href="javascript:void(0)" class="btn btn-xs btn-info edit-batch" data-id="' . $batch->id . '" title="Edit"><i class="fas fa-edit"></i></a>';
            $deleteBtn = '<a href="javascript:void(0)" class="btn btn-xs btn-danger delete-batch" data-id="' . $batch->id . '" title="Hapus"><i class="fas fa-trash"></i></a>';
            return $editBtn . ' ' . $deleteBtn;
         })
         ->rawColumns(['action'])
         ->make(true);
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

   /**
    * Get total stock per product for DataTable
    *
    * Note: query uses DB-level aggregation (SUM, MAX) to avoid loading all batches into memory.
    * For best performance on large datasets ensure `stock_batches.product_id` and `stock_batches.created_at` are indexed.
    */
   public function getTotalPerProduct(Request $request)
   {
      $location = $request->get('location', '');

      // Query DB-level (remote grouping & aggregation) to avoid loading many rows into memory
      $query = StockBatch::query()
         ->selectRaw("products.id as product_id, products.kode_produk, products.nama_produk, products.satuan, IFNULL(categories.nama_kategori, '-') as category, IFNULL(subcategories.nama_subkategori, '-') as subcategory, SUM(stock_batches.qty) as total_qty, MAX(stock_batches.created_at) as latest_date")
         ->join('products', 'products.id', '=', 'stock_batches.product_id')
         ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
         ->leftJoin('subcategories', 'subcategories.id', '=', 'products.subcategory_id')
         ->whereRaw('stock_batches.qty > 0')
         ->groupBy('products.id', 'products.kode_produk', 'products.nama_produk', 'products.satuan', 'categories.nama_kategori', 'subcategories.nama_subkategori');

      // Filter berdasarkan lokasi
      if ($location) {
         $query->where('stock_batches.location_type', $location);
      }

      // DataTables server-side consumption supports Query Builder / Eloquent
      return datatables()->eloquent($query)
         ->addIndexColumn()
         ->editColumn('total_qty', function ($row) {
            return number_format($row->total_qty, 2, ',', '.');
         })
         ->editColumn('latest_date', function ($row) {
            // Pastikan format tanggal sesuai tampilan sebelumnya (d/m/Y H:i)
            return $row->latest_date ? \Carbon\Carbon::parse($row->latest_date)->format('d/m/Y H:i') : '-';
         })
         ->addColumn('action', function ($data) {
            return '<a href="javascript:void(0)" class="btn btn-xs btn-info">Detail</a>';
         })
         ->rawColumns(['action'])
         ->make(true);
   }
}
