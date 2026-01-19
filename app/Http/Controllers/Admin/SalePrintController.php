<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;

class SalePrintController extends Controller
{
    public function show($id)
    {
        $sale = Sale::with(['saleItems.product', 'saleItems.unit', 'customer', 'store', 'warehouse'])->findOrFail($id);

        return view('admin.sales.print', [
            'sale' => $sale,
        ]);
    }
}
