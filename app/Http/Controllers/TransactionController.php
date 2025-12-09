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
        $query = TransactionHistory::with('user');

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

        return DataTables::of($query)
            ->editColumn('transaction_code', function ($transaction) {
                return '<span class="badge badge-info">' . $transaction->transaction_code . '</span>';
            })
            ->editColumn('transaction_type', function ($transaction) {
                $types = [
                    'penjualan' => 'Penjualan',
                    'pembelian' => 'Pembelian',
                    'adjustment' => 'Adjustment Stok',
                    'return' => 'Return',
                    'other' => 'Lainnya',
                ];
                $label = $types[$transaction->transaction_type] ?? $transaction->transaction_type;
                return '<span class="badge badge-primary">' . $label . '</span>';
            })
            ->editColumn('description', function ($transaction) {
                return \Illuminate\Support\Str::limit($transaction->description, 40);
            })
            ->editColumn('transaction_date', function ($transaction) {
                return $transaction->formatted_date;
            })
            ->editColumn('amount', function ($transaction) {
                return '<span class="badge badge-success">' . $transaction->formatted_amount . '</span>';
            })
            ->addColumn('user_name', function ($transaction) {
                return $transaction->user->name ?? '-';
            })
            ->editColumn('status', function ($transaction) {
                $statusClass = match ($transaction->status) {
                    'completed' => 'success',
                    'pending' => 'warning',
                    'failed' => 'danger',
                    'cancelled' => 'secondary',
                    default => 'info',
                };
                $label = match ($transaction->status) {
                    'completed' => 'Completed',
                    'pending' => 'Pending',
                    'failed' => 'Failed',
                    'cancelled' => 'Cancelled',
                    default => $transaction->status,
                };
                return '<span class="badge badge-' . $statusClass . '">' . $label . '</span>';
            })
            ->addColumn('action', function ($transaction) {
                return '<button class="btn btn-xs btn-info" title="Detail">
                    <i class="fas fa-eye"></i>
                </button>';
            })
            ->filter(function ($query) {
                // Handle DataTables search
                if (request()->has('search') && request('search')['value']) {
                    $search = request('search')['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('transaction_code', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%')
                            ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $search . '%'));
                    });
                }
            })
            ->rawColumns(['transaction_code', 'transaction_type', 'amount', 'status', 'action'])
            ->make(true);
    }
}
