<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Models\EsimPlan;
use App\Models\Cart;
use App\Models\Esim;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public $userId;
    public $cartItems;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
        $this->userId = Auth::id();
        $this->cartItems = Cart::where('user_id', $this->userId)
                               ->where('status', 'pending')
                               ->get();
        return $next($request);
    });
    }

    public function index()
    {
        $cartItems = $this->cartItems;

        // 2. Eager load eSIM with plan
        $esims = Esim::whereIn('id', $cartItems->pluck('esim_id'))
            ->with('plan')
            ->get();

        // 3. Group by plan id
        $esimsGrouped = $esims->groupBy('esim_plan_id')->map(function ($group, $planId) use ($cartItems) {
            // Get related plan
            $plan = $group->first()->plan;

            // Count how many cart items correspond to this plan
            $quantity = $cartItems->whereIn('esim_id', $group->pluck('id'))->sum('quantity');

            // Calculate total price
            $total = $cartItems->whereIn('esim_id', $group->pluck('id'))->sum('total_price');

            return (object)[
                'plan_id' => $plan->id,
                'plan_name' => $plan->plan_name,
                'price' => $plan->price,
                'quantity' => $quantity,
                'total' => $total,
                'cart_ids' => $cartItems->whereIn('esim_id', $group->pluck('id'))->pluck('id')->toArray(),
            ];
        })->values();

        $totalAmount = $esimsGrouped->sum('total');

        return view('frontend.cart.index', compact('esimsGrouped', 'totalAmount'));
    }


    // Add an EsimPlan to cart
    public function add(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:esim_plans,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $plan = EsimPlan::findOrFail($request->plan_id);
        $quantity = $request->quantity ?? 1;
        $userId = $this->userId;

        // Get available Esims that are NOT in any cart
        $availableEsims = Esim::where('esim_plan_id', $plan->id)
            ->whereDoesntHave('cart')  // ignore user
            ->inRandomOrder()
            ->take($quantity)
            ->get();

        if ($availableEsims->count() < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough eSIMs available.'
            ], 400);
        }

        $cartItems = [];
        foreach ($availableEsims as $esim) {
            $cartItems[] = Cart::create([
                'user_id'     => $userId,
                'esim_id'     => $esim->id,
                'quantity'    => 1,
                'total_price' => $plan->price,
            ]);
        }

        return response()->json([
            'success'     => true,
            'cart_items'  => $cartItems,
            'message' => 'Plan added to cart successfully.'
        ], 201);
    }



   public function remove($planId)
{
    $userId = $this->userId; // temporary

    // Get user's pending cart items
    $cartItems = Cart::where('user_id', $userId)
        ->where('status', 'pending')
        ->get();

    // Get the eSIM ids inside cart
    $esims = Esim::whereIn('id', $cartItems->pluck('esim_id'))->get();

    // Filter by plan_id
    $esims_same_plan = $esims->where('esim_plan_id', $planId);

    // Get last eSIM for that plan in cart
    $esimToRemove = $esims_same_plan->last();

    if (!$esimToRemove) {
        return response()->json([
            'success' => false,
            'message' => 'No eSIM found for this plan in cart.'
        ], 404);
    }

    // Find the CART record for this eSIM
    $cartRow = Cart::where('esim_id', $esimToRemove->id)
        ->where('user_id', $userId)
        ->where('status', 'pending')
        ->first();

    if (!$cartRow) {
        return response()->json([
            'success' => false,
            'message' => 'Cart record not found.'
        ], 404);
    }

    $cartId = $cartRow->id;
    $cartRow->delete(); // DELETE CART, NOT ESIM

    return response()->json([
        'success'      => true,
        'message'      => 'One eSIM removed from cart.',
        'removed_id'   => $cartId
    ]);
}

    // Remove cart item
    public function destroy($id)
    {

        $cartItems = $this->cartItems;

        $esims = Esim::whereIn('id', $cartItems->pluck('esim_id'))->get();
        $esims_same_plan = $esims->where('esim_plan_id', '=', $id);
        $ItemsToRemove = $cartItems->whereIn('esim_id', $esims_same_plan->pluck('id'));

        foreach ($ItemsToRemove as $esim) {
            Cart::destroy($esim->id);
        }

        return back()->with('success', 'Item removed from cart.');
    }
}
