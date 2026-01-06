<?php

namespace App\Http\Controllers;

use App\Models\SalesBudget;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function test()
    {
        $sales_budget = SalesBudget::emtGet(model_id: 5, with: ['products', 'thirdparty']);
        $summary = $sales_budget->emtGetProductsSummary();

        dd($sales_budget, $summary);

        return response();
    }
}
