<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeliveryNoteController extends Controller
{
    public function preview(Request $request)
    {
        $dataParam = $request->get('data');
        if (! $dataParam) {
            abort(400, 'Missing data');
        }

        $decoded = json_decode(base64_decode($dataParam), true);
        if (! is_array($decoded)) {
            abort(400, 'Invalid data');
        }

        $saleItems = $decoded['saleItems'] ?? [];
        $customerId = $decoded['customer_id'] ?? null;
        $deliveryNotes = $decoded['deliveryNotes'] ?? '';
        $deliveryDate = $decoded['deliveryDate'] ?? null;
        $deliveryNoteNumber = $decoded['deliveryNoteNumber'] ?? null;

        return view('admin.sales.delivery-note-print', compact('saleItems', 'customerId', 'deliveryNotes', 'deliveryDate', 'deliveryNoteNumber'));
    }
}
