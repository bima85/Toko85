<?php

namespace App\Http\Controllers;

use App\Models\TransactionHistory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    public function test()
    {
        $count = TransactionHistory::count();
        $sample = TransactionHistory::with('user')->first();
        return response()->json([
            'total_records' => $count,
            'sample' => $sample,
        ]);
    }

    public function data(Request $request)
    {
        try {
            // Debug logging
            \Log::info('TransactionController.data called', [
                'filters' => $request->only(['filterType', 'filterStatus', 'filterDateFrom', 'filterDateTo']),
                'draw' => $request->draw,
                'start' => $request->start,
                'length' => $request->length,
            ]);

            // Explicitly select needed columns including id
            $query = TransactionHistory::select(
                'id',
                'transaction_code',
                'transaction_type',
                'description',
                'transaction_date',
                'amount',
                'status',
                'user_id'
            )->with('user');

            // Check if there's any data at all
            $totalBefore = $query->count();
            \Log::info('Total transactions before filters: ' . $totalBefore);

            // Apply filters
            if ($request->has('filterType') && $request->filterType) {
                $query->where('transaction_type', $request->filterType);
            }

            if ($request->has('filterStatus') && $request->filterStatus) {
                $query->where('status', $request->filterStatus);
            }

            if ($request->has('filterDateFrom') && $request->filterDateFrom) {
                $query->where('transaction_date', '>=', $request->filterDateFrom . ' 00:00:00');
            }

            if ($request->has('filterDateTo') && $request->filterDateTo) {
                $query->where('transaction_date', '<=', $request->filterDateTo . ' 23:59:59');
            }

            $result = DataTables::of($query)
                ->setRowId('id')
                ->addIndexColumn()
                ->addColumn('transaction_code', function ($transaction) {
                    return $transaction->transaction_code;
                })
                ->addColumn('transaction_type', function ($transaction) {
                    return $transaction->transaction_type;
                })
                ->addColumn('description', function ($transaction) {
                    return $transaction->description;
                })
                ->addColumn('transaction_date', function ($transaction) {
                    return $transaction->transaction_date->format('Y-m-d H:i');
                })
                ->addColumn('amount', function ($transaction) {
                    return 'Rp ' . number_format($transaction->amount, 0, ',', '.');
                })
                ->addColumn('user_name', function ($transaction) {
                    return $transaction->user->name ?? '-';
                })
                ->addColumn('status', function ($transaction) {
                    return $transaction->status;
                })
                ->addColumn('action', function ($transaction) {
                    return '<button class="btn btn-xs btn-info" title="Detail"><i class="fas fa-eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);

            \Log::info('DataTables result created successfully');
            return $result;
        } catch (\Exception $e) {
            \Log::error('Error in TransactionController.data: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
