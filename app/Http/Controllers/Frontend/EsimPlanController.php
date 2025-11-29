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

    public function detail($id)
    {
        // Fetch the specific eSIM plan by ID with its category and eSIMs
        $esimPlan = EsimPlan::with(['category', 'esims'])->findOrFail($id);

        // Pass it to the frontend detail page
        return view('frontend.detail.index', compact('esimPlan'));
    }
}
