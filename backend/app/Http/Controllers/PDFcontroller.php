<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\SaleTransaction;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PDFcontroller extends Controller
{
    //
    public function generateInventoryValuationReport()
    {
        $datas = Product::with(['supplier', 'category', 'brand'])->latest()->get();
        $pdf = PDF::loadView('pdf.inventory', ['datas' => $datas]);
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="report.pdf"');
    }

    public function generateStockMovementsReport()
    {
        $stock = StockMovement::with(['product', 'user'])->latest()->get();
        $pdf = PDF::loadView('pdf.movement', ['datas' => $stock]);
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="report.pdf"');
    }

    public function generatePurchaseHistoryReport()
    {
        $pos = Purchase::with(['supplier', 'items', 'paymentRecord'])->latest()->get();
        $pdf = PDF::loadView('pdf.purchase', ['datas' => $pos]);
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="report.pdf"');
    }

    public function generateSalesReport()
    {
        $transaction = SaleTransaction::with(['customer'])->latest()->get();
        $pdf = PDF::loadView('pdf.sales', ['datas' => $transaction]);
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="report.pdf"');
    }

    public function generateLowStockReport()
    {
        $stocks =  Product::with(['supplier', 'category', 'brand'])->where('status', 'Low Stock')->get();
        $pdf = PDF::loadView('pdf.low-stock', ['datas' => $stocks]);
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="report.pdf"');
    }

    public function generateOutOfStockReport()
    {
        $stocks =  Product::with(['supplier', 'category', 'brand'])->where('status', 'Out of Stock')->get();
        $pdf = PDF::loadView('pdf.out-of-stock', ['datas' => $stocks]);
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="report.pdf"');
    }
}
