<?php

namespace App\Http\Controllers;

use App\Models\TransactionHistory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;

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

    /**
     * Get transaction detail by ID
     */
    public function detail($id)
    {
        try {
            $transaction = TransactionHistory::with('user')->findOrFail($id);

            // Get additional details based on transaction type
            $additionalInfo = [];

            if ($transaction->transaction_type === 'penjualan' && $transaction->reference_id) {
                $sale = \App\Models\Sale::with(['saleItems.product', 'saleItems.unit', 'customer'])->find($transaction->reference_id);
                if ($sale) {
                    $additionalInfo['sale'] = [
                        'no_invoice' => $sale->no_invoice,
                        'customer' => $sale->customer->nama_pelanggan ?? 'Umum',
                        'items' => $sale->saleItems->map(function ($item) {
                            return [
                                'product' => $item->product->nama_produk ?? '-',
                                'qty' => $item->qty,
                                'unit' => $item->unit->nama_unit ?? '-',
                                'price' => 'Rp ' . number_format($item->harga_jual, 0, ',', '.'),
                                'subtotal' => 'Rp ' . number_format(($item->qty ?? 0) * ($item->harga_jual ?? 0), 0, ',', '.'),
                            ];
                        }),
                    ];
                }
            } elseif ($transaction->transaction_type === 'pembelian' && $transaction->reference_id) {
                $purchase = \App\Models\Purchase::with(['purchaseItems.product', 'purchaseItems.unit', 'supplier'])->find($transaction->reference_id);
                if ($purchase) {
                    $additionalInfo['purchase'] = [
                        'no_invoice' => $purchase->no_invoice,
                        'supplier' => $purchase->supplier->nama_supplier ?? '-',
                        'items' => $purchase->purchaseItems->map(function ($item) {
                            return [
                                'product' => $item->product->nama_produk ?? '-',
                                'qty' => $item->qty,
                                'unit' => $item->unit->nama_unit ?? '-',
                                'price' => 'Rp ' . number_format($item->harga_beli, 0, ',', '.'),
                                'subtotal' => 'Rp ' . number_format(($item->qty ?? 0) * ($item->harga_beli ?? 0), 0, ',', '.'),
                            ];
                        }),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $transaction->id,
                    'transaction_code' => $transaction->transaction_code,
                    'transaction_type' => $transaction->transaction_type,
                    'description' => $transaction->description,
                    'transaction_date' => $transaction->transaction_date->format('Y-m-d H:i:s'),
                    'transaction_date_formatted' => $transaction->transaction_date->format('d/m/Y H:i'),
                    'amount' => $transaction->amount,
                    'amount_formatted' => 'Rp ' . number_format($transaction->amount, 0, ',', '.'),
                    'status' => $transaction->status,
                    'user_name' => $transaction->user->name ?? '-',
                    'payment_method' => $transaction->payment_method,
                    'notes' => $transaction->notes,
                    'additional_info' => $additionalInfo,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function stats(Request $request)
    {
        try {
            $query = TransactionHistory::query();

            // Apply same filters as data endpoint
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

            // Calculate stats
            $totalAmount = (clone $query)->sum('amount');
            $totalCount = (clone $query)->count();
            $completedCount = (clone $query)->where('status', 'completed')->count();
            $pendingCount = (clone $query)->where('status', 'pending')->count();
            $cancelledCount = (clone $query)->where('status', 'cancelled')->count();

            // Calculate by type
            $penjualanTotal = TransactionHistory::where('transaction_type', 'penjualan')
                ->when($request->filterDateFrom, fn($q) => $q->where('transaction_date', '>=', $request->filterDateFrom . ' 00:00:00'))
                ->when($request->filterDateTo, fn($q) => $q->where('transaction_date', '<=', $request->filterDateTo . ' 23:59:59'))
                ->sum('amount');

            $pembelianTotal = TransactionHistory::where('transaction_type', 'pembelian')
                ->when($request->filterDateFrom, fn($q) => $q->where('transaction_date', '>=', $request->filterDateFrom . ' 00:00:00'))
                ->when($request->filterDateTo, fn($q) => $q->where('transaction_date', '<=', $request->filterDateTo . ' 23:59:59'))
                ->sum('amount');

            return response()->json([
                'total_amount' => $totalAmount,
                'total_amount_formatted' => 'Rp ' . number_format($totalAmount, 0, ',', '.'),
                'total_count' => $totalCount,
                'completed_count' => $completedCount,
                'pending_count' => $pendingCount,
                'cancelled_count' => $cancelledCount,
                'penjualan_total' => $penjualanTotal,
                'penjualan_total_formatted' => 'Rp ' . number_format($penjualanTotal, 0, ',', '.'),
                'pembelian_total' => $pembelianTotal,
                'pembelian_total_formatted' => 'Rp ' . number_format($pembelianTotal, 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in TransactionController.stats: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
                'reference_id',
                'reference_type',
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
                ->addColumn('harga_jual', function ($transaction) {
                    // Harga jual per unit dari sale_items
                    if ($transaction->transaction_type === 'penjualan' && $transaction->reference_id) {
                        $sale = \App\Models\Sale::with('saleItems')->find($transaction->reference_id);
                        if ($sale && $sale->saleItems->count() > 0) {
                            // Ambil harga jual dari item pertama (atau rata-rata jika berbeda)
                            $hargaJual = $sale->saleItems->first()->harga_jual ?? 0;
                            return 'Rp ' . number_format($hargaJual, 0, ',', '.');
                        }
                    }
                    return '-';
                })
                ->addColumn('satuan', function ($transaction) {
                    // Satuan dari sale_items atau purchase_items
                    if ($transaction->transaction_type === 'penjualan' && $transaction->reference_id) {
                        $sale = \App\Models\Sale::with('saleItems.unit')->find($transaction->reference_id);
                        if ($sale && $sale->saleItems->count() > 0) {
                            return $sale->saleItems->first()->unit->nama_unit ?? '-';
                        }
                    } elseif ($transaction->transaction_type === 'pembelian' && $transaction->reference_id) {
                        $purchase = \App\Models\Purchase::with('purchaseItems.unit')->find($transaction->reference_id);
                        if ($purchase && $purchase->purchaseItems->count() > 0) {
                            return $purchase->purchaseItems->first()->unit->nama_unit ?? '-';
                        }
                    }
                    return '-';
                })
                ->addColumn('harga_beli', function ($transaction) {
                    // Harga beli per unit dari purchase_items
                    if ($transaction->transaction_type === 'pembelian' && $transaction->reference_id) {
                        $purchase = \App\Models\Purchase::with('purchaseItems')->find($transaction->reference_id);
                        if ($purchase && $purchase->purchaseItems->count() > 0) {
                            // Ambil harga beli dari item pertama
                            $hargaBeli = $purchase->purchaseItems->first()->harga_beli ?? 0;
                            return 'Rp ' . number_format($hargaBeli, 0, ',', '.');
                        }
                    }
                    return '-';
                })
                ->addColumn('total_amount', function ($transaction) {
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

    /**
     * Export transactions to Excel with current filters
     */
    public function export(Request $request)
    {
        try {
            $query = TransactionHistory::query();

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

            // Apply simple search param (from DataTables search.value)
            if ($request->has('search') && $request->search) {
                $search = is_array($request->search) && isset($request->search['value']) ? $request->search['value'] : $request->search;
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('transaction_code', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                }
            }

            $transactions = $query->with('user')->orderByDesc('transaction_date')->get();

            $rows = [];
            foreach ($transactions as $t) {
                $hasItems = false;

                if ($t->transaction_type === 'penjualan' && $t->reference_id) {
                    $sale = \App\Models\Sale::with(['saleItems.product', 'saleItems.unit'])->find($t->reference_id);
                    if ($sale && $sale->saleItems->count() > 0) {
                        foreach ($sale->saleItems as $item) {
                            $qty = $item->qty ?? 0;
                            $hargaJualNum = is_numeric($item->harga_jual) ? (float) $item->harga_jual : 0;
                            $hargaBeliNum = is_numeric($item->harga_beli) ? (float) $item->harga_beli : 0;
                            $subtotalNum = $qty * $hargaJualNum;
                            $rows[] = [
                                $t->id,
                                $t->transaction_code,
                                $t->transaction_type,
                                $t->transaction_date ? $t->transaction_date->format('Y-m-d H:i:s') : '',
                                $t->status,
                                $t->user?->name ?? '-',
                                $item->product->nama_produk ?? '-',
                                $qty,
                                $item->unit->nama_unit ?? '-',
                                // formatted harga jual
                                $hargaJualNum ? 'Rp ' . number_format($hargaJualNum, 0, ',', '.') : '-',
                                // formatted harga beli
                                $hargaBeliNum ? 'Rp ' . number_format($hargaBeliNum, 0, ',', '.') : '-',
                                // formatted subtotal
                                'Rp ' . number_format($subtotalNum, 0, ',', '.'),
                                // total transaksi formatted
                                'Rp ' . number_format((float) $t->amount, 0, ',', '.'),
                                $t->description,
                            ];
                        }
                        $hasItems = true;
                    }
                } elseif ($t->transaction_type === 'pembelian' && $t->reference_id) {
                    $purchase = \App\Models\Purchase::with(['purchaseItems.product', 'purchaseItems.unit'])->find($t->reference_id);
                    if ($purchase && $purchase->purchaseItems->count() > 0) {
                        foreach ($purchase->purchaseItems as $item) {
                            $qty = $item->qty ?? 0;
                            $hargaBeliNum = is_numeric($item->harga_beli) ? (float) $item->harga_beli : 0;
                            $hargaJualNum = is_numeric($item->harga_jual) ? (float) $item->harga_jual : 0;
                            $subtotalNum = $qty * $hargaBeliNum;
                            $rows[] = [
                                $t->id,
                                $t->transaction_code,
                                $t->transaction_type,
                                $t->transaction_date ? $t->transaction_date->format('Y-m-d H:i:s') : '',
                                $t->status,
                                $t->user?->name ?? '-',
                                $item->product->nama_produk ?? '-',
                                $qty,
                                $item->unit->nama_unit ?? '-',
                                // formatted harga jual if any
                                $hargaJualNum ? 'Rp ' . number_format($hargaJualNum, 0, ',', '.') : '-',
                                // formatted harga beli
                                $hargaBeliNum ? 'Rp ' . number_format($hargaBeliNum, 0, ',', '.') : '-',
                                // formatted subtotal (based on harga_beli)
                                'Rp ' . number_format($subtotalNum, 0, ',', '.'),
                                // total transaksi formatted
                                'Rp ' . number_format((float) $t->amount, 0, ',', '.'),
                                $t->description,
                            ];
                        }
                        $hasItems = true;
                    }
                }

                // If no items, still add a single summary row
                if (! $hasItems) {
                    $rows[] = [
                        $t->id,
                        $t->transaction_code,
                        $t->transaction_type,
                        $t->transaction_date ? $t->transaction_date->format('Y-m-d H:i:s') : '',
                        $t->status,
                        $t->user?->name ?? '-',
                        '-', // product
                        0,   // qty
                        '-', // unit
                        '-', // harga jual
                        '-', // harga beli
                        '-', // subtotal
                        'Rp ' . number_format((float) $t->amount, 0, ',', '.'),
                        $t->description,
                    ];
                }
            }

            $fileName = 'Transaksi_Detail_' . date('Y-m-d_His') . '.xlsx';
            return Excel::download(new TransactionsExport($rows), $fileName);
        } catch (\Exception $e) {
            \Log::error('Error exporting transactions: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
