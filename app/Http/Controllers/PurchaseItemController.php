<?php

namespace App\Http\Controllers;

use App\Models\PurchaseItem;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class PurchaseItemController extends Controller
{
    /**
     * Handle DataTable request for purchase items
     */
    public function data(Request $request)
    {
        $query = PurchaseItem::with([
            'product.category',
            'product.subcategory',
            'unit'
        ])->where('purchase_id', $request->purchase_id);

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                return '
                    <button class="btn btn-info btn-xs edit-item" data-id="' . $row->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-xs delete-item" data-id="' . $row->id . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->addColumn('category_name', function ($row) {
                return $row->category?->nama_kategori ?? '-';
            })
            ->addColumn('subcategory_name', function ($row) {
                return $row->subcategory?->nama_subkategori ?? '-';
            })
            ->addColumn('product_name', function ($row) {
                return $row->product?->nama_produk ?? '-';
            })
            ->addColumn('unit_name', function ($row) {
                return $row->unit?->nama_unit ?? '-';
            })
            ->addColumn('total_formatted', function ($row) {
                $qty = $row->qty ?? 0;
                $harga = $row->harga_beli ?? 0;
                $unit_conversion = $row->unit?->conversion_value ?? 1;
                $total = ($qty * $unit_conversion) * $harga;
                return 'Rp ' . number_format($total, 0, ',', '.');
            })
            ->editColumn('qty', function ($row) {
                return $row->qty ?? 0;
            })
            ->editColumn('qty_gudang', function ($row) {
                return $row->qty_gudang ?? 0;
            })
            ->editColumn('harga_beli', function ($row) {
                return 'Rp ' . number_format($row->harga_beli ?? 0, 0, ',', '.');
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
