<?php

namespace App\Http\Controllers\Frontend;

use App\Models\EsimPlan;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch all plans with their category
        $plans = EsimPlan::with('category')
            ->whereIn('id', [1, 6, 11])
            ->get();


        return view('frontend.home.index', compact('plans'));
    }
}
