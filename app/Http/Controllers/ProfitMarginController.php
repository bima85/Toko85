<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProfitMarginExport;
use Illuminate\Support\Facades\DB;

class ProfitMarginController extends Controller
{
    /**
     * Get profit margin data for DataTables
     */
    public function data(Request $request)
    {
        try {
            // Query penjualan dengan items
            $query = Sale::with(['saleItems.product', 'saleItems.unit', 'customer', 'user'])
                ->where('status', 'completed')
                ->orderByDesc('tanggal_penjualan');

            // Apply filters
            if ($request->has('filterDateFrom') && $request->filterDateFrom) {
                $query->where('tanggal_penjualan', '>=', $request->filterDateFrom);
            }

            if ($request->has('filterDateTo') && $request->filterDateTo) {
                $query->where('tanggal_penjualan', '<=', $request->filterDateTo);
            }

            if ($request->has('filterCustomer') && $request->filterCustomer) {
                $query->where('customer_id', $request->filterCustomer);
            }

            $sales = $query->get();

            // Build rows with profit calculation
            $data = [];
            foreach ($sales as $sale) {
                // Get sale total from database
                $saleTotal = is_numeric($sale->total_amount) ? (float) $sale->total_amount : 0;

                // Check if sale has items
                if ($sale->saleItems->isEmpty()) {
                    // If no items, add a row for the sale itself
                    $data[] = [
                        'id' => $sale->id,
                        'no_invoice' => $sale->no_invoice,
                        'tanggal' => $sale->tanggal_penjualan->format('Y-m-d'),
                        'customer' => $sale->customer->nama_pelanggan ?? 'Umum',
                        'product' => '(Tidak ada item)',
                        'qty' => 0,
                        'unit' => '-',
                        'harga_beli' => 0,
                        'harga_jual' => 0,
                        'total_beli' => 0,
                        'total_jual' => $saleTotal,
                        'profit' => $saleTotal,
                        'margin_persen' => 100,
                        'user' => $sale->user->name ?? '-',
                    ];
                    continue;
                }

                // Calculate total from items for proportional distribution
                $itemsTotal = 0;
                foreach ($sale->saleItems as $item) {
                    $qty = $item->qty ?? 0;
                    $hargaJual = is_numeric($item->harga_jual) ? (float) $item->harga_jual : 0;
                    $itemsTotal += ($qty * $hargaJual);
                }

                foreach ($sale->saleItems as $item) {
                    $qty = $item->qty ?? 0;
                    $hargaJual = is_numeric($item->harga_jual) ? (float) $item->harga_jual : 0;
                    $hargaBeli = is_numeric($item->harga_beli) ? (float) $item->harga_beli : 0;

                    // Calculate item subtotal from qty * price
                    $itemSubtotal = $qty * $hargaJual;

                    // Distribute sale total proportionally to this item
                    $totalJual = $itemsTotal > 0 ? ($itemSubtotal / $itemsTotal) * $saleTotal : $itemSubtotal;

                    $totalBeli = $qty * $hargaBeli;
                    $profit = $totalJual - $totalBeli;
                    $marginPersen = $totalJual > 0 ? (($profit / $totalJual) * 100) : 0;

                    $data[] = [
                        'id' => $item->id,
                        'no_invoice' => $sale->no_invoice,
                        'tanggal' => $sale->tanggal_penjualan->format('Y-m-d'),
                        'customer' => $sale->customer->nama_pelanggan ?? 'Umum',
                        'product' => $item->product->nama_produk ?? '-',
                        'qty' => $qty,
                        'unit' => $item->unit->nama_unit ?? '-',
                        'harga_beli' => $hargaBeli,
                        'harga_jual' => $hargaJual,
                        'total_beli' => $totalBeli,
                        'total_jual' => $totalJual,
                        'profit' => $profit,
                        'margin_persen' => $marginPersen,
                        'user' => $sale->user->name ?? '-',
                    ];
                }
            }

            return DataTables::of(collect($data))
                ->addIndexColumn()
                ->addColumn('no_invoice', function ($row) {
                    return $row['no_invoice'];
                })
                ->addColumn('tanggal', function ($row) {
                    return $row['tanggal'];
                })
                ->addColumn('customer', function ($row) {
                    return $row['customer'];
                })
                ->addColumn('product', function ($row) {
                    return $row['product'];
                })
                ->addColumn('qty', function ($row) {
                    return $row['qty'];
                })
                ->addColumn('unit', function ($row) {
                    return $row['unit'];
                })
                ->addColumn('harga_beli_formatted', function ($row) {
                    return 'Rp ' . number_format($row['harga_beli'], 0, ',', '.');
                })
                ->addColumn('harga_jual_formatted', function ($row) {
                    return 'Rp ' . number_format($row['harga_jual'], 0, ',', '.');
                })
                ->addColumn('total_beli_formatted', function ($row) {
                    return 'Rp ' . number_format($row['total_beli'], 0, ',', '.');
                })
                ->addColumn('total_jual_formatted', function ($row) {
                    return 'Rp ' . number_format($row['total_jual'], 0, ',', '.');
                })
                ->addColumn('profit_formatted', function ($row) {
                    $color = $row['profit'] >= 0 ? 'success' : 'danger';
                    return '<strong class="text-' . $color . '">Rp ' . number_format($row['profit'], 0, ',', '.') . '</strong>';
                })
                ->addColumn('margin_persen_formatted', function ($row) {
                    $color = $row['margin_persen'] >= 30 ? 'success' : ($row['margin_persen'] >= 15 ? 'warning' : 'danger');
                    return '<span class="badge badge-' . $color . '">' . number_format($row['margin_persen'], 2) . '%</span>';
                })
                ->addColumn('user', function ($row) {
                    return $row['user'];
                })
                ->addColumn('total_beli', function ($row) {
                    return $row['total_beli'];
                })
                ->addColumn('total_jual', function ($row) {
                    return $row['total_jual'];
                })
                ->addColumn('profit', function ($row) {
                    return $row['profit'];
                })
                ->rawColumns(['profit_formatted', 'margin_persen_formatted'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('Error in ProfitMarginController.data: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Check if a customer has transactions within the current date filters
     * Returns counts: countFiltered, countAll
     */
    public function checkCustomer(Request $request)
    {
        try {
            $customerId = $request->filterCustomer;

            if (!$customerId) {
                return response()->json(['error' => 'filterCustomer is required'], 400);
            }

            $filteredQuery = Sale::where('customer_id', $customerId)
                ->where('status', 'completed');

            if ($request->has('filterDateFrom') && $request->filterDateFrom) {
                $filteredQuery->where('tanggal_penjualan', '>=', $request->filterDateFrom);
            }

            if ($request->has('filterDateTo') && $request->filterDateTo) {
                $filteredQuery->where('tanggal_penjualan', '<=', $request->filterDateTo);
            }

            $countFiltered = $filteredQuery->count();
            $countAll = Sale::where('customer_id', $customerId)->where('status', 'completed')->count();

            return response()->json([
                'countFiltered' => $countFiltered,
                'countAll' => $countAll,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in ProfitMarginController.checkCustomer: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Return list of customers that have transactions (optionally within date range)
     */
    public function customers(Request $request)
    {
        try {
            $query = Sale::query()->where('status', 'completed');

            if ($request->has('filterDateFrom') && $request->filterDateFrom) {
                $query->where('tanggal_penjualan', '>=', $request->filterDateFrom);
            }
            if ($request->has('filterDateTo') && $request->filterDateTo) {
                $query->where('tanggal_penjualan', '<=', $request->filterDateTo);
            }

            $customerIds = $query->whereNotNull('customer_id')->distinct()->pluck('customer_id')->toArray();

            $customers = Customer::whereIn('id', $customerIds)->orderBy('nama_pelanggan')->get(['id', 'nama_pelanggan']);

            return response()->json(['customers' => $customers]);
        } catch (\Exception $e) {
            \Log::error('Error in ProfitMarginController.customers: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get statistics for profit margin
     */
    public function stats(Request $request)
    {
        try {
            $query = Sale::with(['saleItems.product'])
                ->where('status', 'completed');

            if ($request->has('filterDateFrom') && $request->filterDateFrom) {
                $query->where('tanggal_penjualan', '>=', $request->filterDateFrom);
            }

            if ($request->has('filterDateTo') && $request->filterDateTo) {
                $query->where('tanggal_penjualan', '<=', $request->filterDateTo);
            }

            if ($request->has('filterCustomer') && $request->filterCustomer) {
                $query->where('customer_id', $request->filterCustomer);
            }

            $sales = $query->get();

            $totalPenjualan = 0;
            $totalModal = 0;
            $totalProfit = 0;
            $itemCount = 0;

            foreach ($sales as $sale) {
                // Use sale total_amount instead of recalculating
                $saleTotal = is_numeric($sale->total_amount) ? (float) $sale->total_amount : 0;
                $totalPenjualan += $saleTotal;

                foreach ($sale->saleItems as $item) {
                    $qty = $item->qty ?? 0;
                    $hargaBeli = is_numeric($item->harga_beli) ? (float) $item->harga_beli : 0;

                    $totalBeli = $qty * $hargaBeli;
                    $totalModal += $totalBeli;
                    $itemCount++;
                }
            }

            $totalProfit = $totalPenjualan - $totalModal;
            $avgMargin = $totalPenjualan > 0 ? ($totalProfit / $totalPenjualan) * 100 : 0;

            return response()->json([
                'total_penjualan' => $totalPenjualan,
                'total_penjualan_formatted' => 'Rp ' . number_format($totalPenjualan, 0, ',', '.'),
                'total_modal' => $totalModal,
                'total_modal_formatted' => 'Rp ' . number_format($totalModal, 0, ',', '.'),
                'total_profit' => $totalProfit,
                'total_profit_formatted' => 'Rp ' . number_format($totalProfit, 0, ',', '.'),
                'avg_margin' => $avgMargin,
                'avg_margin_formatted' => number_format($avgMargin, 2) . '%',
                'item_count' => $itemCount,
                'transaction_count' => $sales->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in ProfitMarginController.stats: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Export profit margin data to Excel
     */
    public function export(Request $request)
    {
        try {
            $query = Sale::with(['saleItems.product', 'saleItems.unit', 'customer'])
                ->where('status', 'completed')
                ->orderByDesc('tanggal_penjualan');

            if ($request->has('filterDateFrom') && $request->filterDateFrom) {
                $query->where('tanggal_penjualan', '>=', $request->filterDateFrom);
            }

            if ($request->has('filterDateTo') && $request->filterDateTo) {
                $query->where('tanggal_penjualan', '<=', $request->filterDateTo);
            }

            if ($request->has('filterCustomer') && $request->filterCustomer) {
                $query->where('customer_id', $request->filterCustomer);
            }

            $sales = $query->get();

            $rows = [];
            $grandTotalBeli = 0;
            $grandTotalJual = 0;
            $grandProfit = 0;

            foreach ($sales as $sale) {
                // Get sale total from database
                $saleTotal = is_numeric($sale->total_amount) ? (float) $sale->total_amount : 0;

                // Check if sale has items
                if ($sale->saleItems->isEmpty()) {
                    $rows[] = [
                        $sale->no_invoice,
                        $sale->tanggal_penjualan->format('Y-m-d'),
                        $sale->customer->nama_pelanggan ?? 'Umum',
                        '(Tidak ada item)',
                        0,
                        '-',
                        'Rp 0',
                        'Rp 0',
                        'Rp 0',
                        'Rp ' . number_format($saleTotal, 0, ',', '.'),
                        'Rp ' . number_format($saleTotal, 0, ',', '.'),
                        '100.00%',
                        $sale->user->name ?? '-',
                    ];
                    $grandTotalJual += $saleTotal;
                    $grandProfit += $saleTotal;
                    continue;
                }

                // Calculate total from items for proportional distribution
                $itemsTotal = 0;
                foreach ($sale->saleItems as $item) {
                    $qty = $item->qty ?? 0;
                    $hargaJual = is_numeric($item->harga_jual) ? (float) $item->harga_jual : 0;
                    $itemsTotal += ($qty * $hargaJual);
                }

                foreach ($sale->saleItems as $item) {
                    $qty = $item->qty ?? 0;
                    $hargaJual = is_numeric($item->harga_jual) ? (float) $item->harga_jual : 0;
                    $hargaBeli = is_numeric($item->harga_beli) ? (float) $item->harga_beli : 0;

                    // Calculate item subtotal from qty * price
                    $itemSubtotal = $qty * $hargaJual;

                    // Distribute sale total proportionally to this item
                    $totalJual = $itemsTotal > 0 ? ($itemSubtotal / $itemsTotal) * $saleTotal : $itemSubtotal;

                    $totalBeli = $qty * $hargaBeli;
                    $profit = $totalJual - $totalBeli;
                    $marginPersen = $totalJual > 0 ? (($profit / $totalJual) * 100) : 0;

                    $rows[] = [
                        $sale->no_invoice,
                        $sale->tanggal_penjualan->format('Y-m-d'),
                        $sale->customer->nama_pelanggan ?? 'Umum',
                        $item->product->nama_produk ?? '-',
                        $qty,
                        $item->unit->nama_unit ?? '-',
                        'Rp ' . number_format($hargaBeli, 0, ',', '.'),
                        'Rp ' . number_format($hargaJual, 0, ',', '.'),
                        'Rp ' . number_format($totalBeli, 0, ',', '.'),
                        'Rp ' . number_format($totalJual, 0, ',', '.'),
                        'Rp ' . number_format($profit, 0, ',', '.'),
                        number_format($marginPersen, 2) . '%',
                        $sale->user->name ?? '-',
                    ];

                    $grandTotalBeli += $totalBeli;
                    $grandTotalJual += $totalJual;
                    $grandProfit += $profit;
                }
            }

            // Add grand total row
            $grandMargin = $grandTotalJual > 0 ? (($grandProfit / $grandTotalJual) * 100) : 0;
            $rows[] = [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'GRAND TOTAL:',
                'Rp ' . number_format($grandTotalBeli, 0, ',', '.'),
                'Rp ' . number_format($grandTotalJual, 0, ',', '.'),
                'Rp ' . number_format($grandProfit, 0, ',', '.'),
                number_format($grandMargin, 2) . '%',
                '',
            ];

            $fileName = 'Profit_Margin_' . date('Y-m-d_His') . '.xlsx';
            return Excel::download(new ProfitMarginExport($rows), $fileName);
        } catch (\Exception $e) {
            \Log::error('Error exporting profit margin: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
