<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\SaleTransaction;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PDFcontroller extends Controller
{
    //
    public function generateInventoryValuationReport(Request $request)
    {
        $filter = $request->filter;
        $date = Carbon::now();
        $product = Product::with(['supplier', 'category', 'brand']);

        switch ($filter) {
            case 'Daily':
                $startOfDay = $date->copy()->startOfDay();
                $endOfDay = $date->copy()->endOfDay();
                $datas = $product->whereBetween('created_at', [$startOfDay, $endOfDay])->latest()->get();
                break;
            case 'Weekly':
                $startOfWeek = $date->copy()->startOfWeek();
                $endOfWeek = $date->copy()->endOfWeek();
                $datas = $product->whereBetween('created_at', [$startOfWeek, $endOfWeek])->latest()->get();
                break;
            case 'Monthly':
                $month = $date->format('m');
                $datas = $product->whereMonth('created_at', $month)->latest()->get();
                break;
            case 'Yearly':
                $year = $date->format('Y');
                $datas = $product->whereYear('created_at', $year)->latest()->get();
                break;
            default:
                $datas = $product->latest()->get();
                break;
        }

        $pdf = PDF::loadView('pdf.inventory', compact('datas', 'filter'));
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="report.pdf"');
    }

    public function generateStockMovementsReport(Request $request)
    {
        $filter = $request->filter;
        $date = Carbon::now();
        $stock = StockMovement::with(['product', 'user']);
        switch ($filter) {
            case 'Daily':
                $startOfDay = $date->copy()->startOfDay();
                $endOfDay = $date->copy()->endOfDay();
                $datas = $stock->whereBetween('created_at', [$startOfDay, $endOfDay])->latest()->get();
                break;
            case 'Weekly':
                $startOfWeek = $date->copy()->startOfWeek();
                $endOfWeek = $date->copy()->endOfWeek();
                $datas = $stock->whereBetween('created_at', [$startOfWeek, $endOfWeek])->latest()->get();
                break;
            case 'Monthly':
                $month = $date->format('m');
                $datas = $stock->whereMonth('created_at', $month)->latest()->get();
                break;
            case 'Yearly':
                $year = $date->format('Y');
                $datas = $stock->whereYear('created_at', $year)->latest()->get();
                break;
            default:
                $datas = $stock->latest()->get();
                break;
        }
        $pdf = PDF::loadView('pdf.movement', compact('datas', 'filter'));
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="report.pdf"');
    }

    public function generatePurchaseHistoryReport(Request $request)
    {
        $filter = $request->filter;
        $date = Carbon::now();
        $pos = Purchase::with(['supplier', 'items', 'paymentRecord'])->where('status', 'Delivered')->where('payment_status', 'Paid');

        switch ($filter) {
            case 'Daily':
                $startOfDay = $date->copy()->startOfDay();
                $endOfDay = $date->copy()->endOfDay();
                $datas = $pos->whereBetween('created_at', [$startOfDay, $endOfDay])->latest()->get();
                break;
            case 'Weekly':
                $startOfWeek = $date->copy()->startOfWeek();
                $endOfWeek = $date->copy()->endOfWeek();
                $datas = $pos->whereBetween('created_at', [$startOfWeek, $endOfWeek])->latest()->get();
                break;
            case 'Monthly':
                $month = $date->format('m');
                $datas = $pos->whereMonth('created_at', $month)->latest()->get();
                break;
            case 'Yearly':
                $year = $date->format('Y');
                $datas = $pos->whereYear('created_at', $year)->latest()->get();
                break;
            default:
                $datas = $pos->latest()->get();
                break;
        }

        $pdf = PDF::loadView('pdf.purchase', compact('datas', 'filter'));
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="report.pdf"');
    }

    public function generateSalesReport(Request $request)
    {
        $filter = $request->filter;
        $date = Carbon::now();
        $transaction = SaleTransaction::with(['customer']);
        switch ($filter) {
            case 'Daily':
                $startOfDay = $date->copy()->startOfDay();
                $endOfDay = $date->copy()->endOfDay();
                $datas = $transaction->whereBetween('created_at', [$startOfDay, $endOfDay])->latest()->get();
                break;
            case 'Weekly':
                $startOfWeek = $date->copy()->startOfWeek();
                $endOfWeek = $date->copy()->endOfWeek();
                $datas = $transaction->whereBetween('created_at', [$startOfWeek, $endOfWeek])->latest()->get();
                break;
            case 'Monthly':
                $month = $date->format('m');
                $datas = $transaction->whereMonth('created_at', $month)->latest()->get();
                break;
            case 'Yearly':
                $year = $date->format('Y');
                $datas = $transaction->whereYear('created_at', $year)->latest()->get();
                break;
            default:
                $datas = $transaction->latest()->get();
                break;
        }

        $pdf = PDF::loadView('pdf.sales', compact('datas', 'filter'));
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="report.pdf"');
    }

    public function generateLowStockReport(Request $request)
    {
        $filter = $request->filter;
        $date = Carbon::now();
        $stocks =  Product::with(['supplier', 'category', 'brand'])->where('status', 'Low Stock');

        switch ($filter) {
            case 'Daily':
                $startOfDay = $date->copy()->startOfDay();
                $endOfDay = $date->copy()->endOfDay();
                $datas = $stocks->whereBetween('created_at', [$startOfDay, $endOfDay])->latest()->get();
                break;
            case 'Weekly':
                $startOfWeek = $date->copy()->startOfWeek();
                $endOfWeek = $date->copy()->endOfWeek();
                $datas = $stocks->whereBetween('created_at', [$startOfWeek, $endOfWeek])->latest()->get();
                break;
            case 'Monthly':
                $month = $date->format('m');
                $datas = $stocks->whereMonth('created_at', $month)->latest()->get();
                break;
            case 'Yearly':
                $year = $date->format('Y');
                $datas = $stocks->whereYear('created_at', $year)->latest()->get();
                break;
            default:
                $datas = $stocks->latest()->get();
                break;
        }
        $pdf = PDF::loadView('pdf.low-stock', compact('datas', 'filter'));
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="report.pdf"');
    }

    public function generateOutOfStockReport(Request $request)
    {
        $filter = $request->filter;
        $date = Carbon::now();
        $stocks =  Product::with(['supplier', 'category', 'brand'])->where('status', 'Out of Stock');
        switch ($filter) {
            case 'Daily':
                $startOfDay = $date->copy()->startOfDay();
                $endOfDay = $date->copy()->endOfDay();
                $datas = $stocks->whereBetween('created_at', [$startOfDay, $endOfDay])->latest()->get();
                break;
            case 'Weekly':
                $startOfWeek = $date->copy()->startOfWeek();
                $endOfWeek = $date->copy()->endOfWeek();
                $datas = $stocks->whereBetween('created_at', [$startOfWeek, $endOfWeek])->latest()->get();
                break;
            case 'Monthly':
                $month = $date->format('m');
                $datas = $stocks->whereMonth('created_at', $month)->latest()->get();
                break;
            case 'Yearly':
                $year = $date->format('Y');
                $datas = $stocks->whereYear('created_at', $year)->latest()->get();
                break;
            default:
                $datas = $stocks->latest()->get();
                break;
        }
        $pdf = PDF::loadView('pdf.out-of-stock', compact('datas', 'filter'));
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="report.pdf"');
    }
}
