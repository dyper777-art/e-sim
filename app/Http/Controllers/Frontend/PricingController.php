<?php

namespace App\Http\Controllers\Frontend;

use App\Models\EsimPlan;

use App\Http\Controllers\Controller;
use App\Models\Category;

class PricingController extends Controller
{
    public function index()
    {
        $plans = EsimPlan::join('categories', 'esim_plans.category_id', '=', 'categories.id')
        ->select('esim_plans.*', 'categories.name as category_name')
        ->get();

        $categories = Category::all();

        return view('frontend.pricing.index', compact('plans', 'categories'));
    }
}
