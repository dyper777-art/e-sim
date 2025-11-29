<?php

namespace App\Http\Controllers\Frontend;

use App\Models\EsimPlan;

use App\Http\Controllers\Controller;

class EsimPlanController extends Controller
{
    public function index()
    {
        // Fetch all plans with their category
        $plans = EsimPlan::with('category')->get();

        return view('frontend.home.index', compact('plans'));
    }
}
