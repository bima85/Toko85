<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockBatch;
use App\Models\Product;
use App\Models\Store;
use App\Models\Warehouse;

class StockReportController extends Controller
{
    public function partial(Request $request)
    {
        $type = $request->query('type', 'store');
        $locationId = $request->query('locationId');
        $search = trim($request->query('search', ''));
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');

        $query = StockBatch::query()
            ->where('status', 'aktual')
            ->where('qty', '>', 0)
            ->with('product.category', 'product.subcategory');

        // apply date filters if provided
        if ($startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate)->startOfDay();
            $end = \Carbon\Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('updated_at', [$start, $end]);
        } elseif ($startDate) {
            $query->where('updated_at', '>=', \Carbon\Carbon::parse($startDate)->startOfDay());
        } elseif ($endDate) {
            $query->where('updated_at', '<=', \Carbon\Carbon::parse($endDate)->endOfDay());
        }

        if ($type === 'store') {
            $query->where('location_type', 'store');
        } elseif ($type === 'warehouse') {
            $query->where('location_type', 'warehouse');
        }

        if (!empty($locationId)) {
            $query->where('location_id', $locationId);
        }

        if (!empty($search)) {
            $q = $search;
            $query->whereHas('product', function ($sub) use ($q) {
                $sub->where('nama_produk', 'like', "%$q%")
                    ->orWhere('kode_produk', 'like', "%$q%");
            });
        }

        $rows = $query->selectRaw('product_id, SUM(qty) as total_qty')
            ->groupBy('product_id')
            ->get()
            ->map(function ($batch) use ($type, $locationId, $startDate, $endDate) {
                $product = Product::find($batch->product_id);

                $lokasi = 'Semua Lokasi';
                if ($locationId) {
                    if ($type === 'warehouse') {
                        $w = Warehouse::find($locationId);
                        $lokasi = $w?->nama_gudang ?? $lokasi;
                    } else {
                        $s = Store::find($locationId);
                        $lokasi = $s?->nama_toko ?? $lokasi;
                    }
                } else {
                    if ($type === 'warehouse') $lokasi = 'Semua Gudang';
                    if ($type === 'store') $lokasi = 'Semua Toko';
                }

                $holdQuery = StockBatch::where('product_id', $batch->product_id)->where('status', 'hold')->where('qty', '>', 0);
                if ($type === 'store') {
                    $holdQuery->where('location_type', 'store');
                } elseif ($type === 'warehouse') {
                    $holdQuery->where('location_type', 'warehouse');
                }
                if ($locationId) {
                    $holdQuery->where('location_id', $locationId);
                }
                $holdQty = (float) $holdQuery->sum('qty');

                return [
                    'product_id' => $product?->id ?: null,
                    'kode_produk' => $product?->kode_produk ?: '-',
                    'nama_produk' => $product?->nama_produk ?: '-',
                    'kategori' => $product?->category?->nama_kategori ?? '-',
                    'sub_kategori' => $product?->subcategory?->nama_subkategori ?? '-',
                    'unit' => $product?->satuan ?? '-',
                    'stok_akhir' => (float) $batch->total_qty,
                    'lokasi' => $lokasi,
                    'hold_qty' => $holdQty,
                    'has_hold' => $holdQty > 0,
                ];
            })->values();

        // apply php search fallback
        if (!empty($search)) {
            $lower = mb_strtolower($search);
            $rows = $rows->filter(function ($r) use ($lower) {
                $hay = mb_strtolower(($r['nama_produk'] ?? '') . ' ' . ($r['kode_produk'] ?? ''));
                return mb_strpos($hay, $lower) !== false;
            })->values();
        }

        $html = view('livewire.admin._stock_table', ['rows' => $rows])->render();

        return response()->json(['html' => $html, 'time' => now()->format('Y-m-d H:i:s')]);
    }
}
