<?php

namespace App\Http\Controllers;

use App\Models\SalesBudget;
use App\Models\SalesInvoice;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function test()
    {

        $invoice = SalesInvoice::emtGet(model_id: 1, with: ['sales_invoices_products', 'thirdparty'])->first();

        return view('pdf.sales_invoice', [
            'document' => $invoice,
            'data' => [
                'company' => $invoice->company,
                'products_summary' => $invoice ? $invoice->emtGetProductsSummary() : [],
            ],
        ]);


        $sales_budget = SalesBudget::emtGet(model_id: 5, with: ['products', 'thirdparty']);
        $summary = $sales_budget->emtGetProductsSummary();

        dd($sales_budget, $summary);

        return response();
    }
}
