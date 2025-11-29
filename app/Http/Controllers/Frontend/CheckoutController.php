<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Models\Checkout;
use App\Models\Esim;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;
use Resend\Laravel\Facades\Resend;

use Illuminate\Support\Facades\Http;


use App\Http\Controllers\Controller;

class CheckoutController extends Controller
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
    // Show checkout page
    public function index()
    {
        // Fetch user's pending cart items from the database
        $cart = $this->cartItems;


        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Group cart items by plan
        $esimsGrouped = $cart->groupBy('esim.plan.id')->map(function ($group, $planId) {
            $plan = $group->first()->esim->plan;
            $quantity = $group->sum('quantity');
            $total = $group->sum('total_price');

            return (object)[
                'plan_id' => $plan->id,
                'plan_name' => $plan->plan_name,
                'price' => $plan->price,
                'quantity' => $quantity,
                'total' => $total,
                'cart_ids' => $group->pluck('id')->toArray(),
            ];
        })->values();

        $totalAmount = $esimsGrouped->sum('total');

        return view('frontend.checkout.index', compact('esimsGrouped', 'totalAmount'));
    }


    // Process checkout
    public function process(Request $request)
    {
        // Fetch user's pending cart items from the database
        $cart = $this->cartItems;

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $totalAmount = 0;

        foreach ($cart as $item) {
            $totalPrice = $item->total_price;
            $totalAmount += $totalPrice;

            // Create a checkout record
            Checkout::create([
                'user_id'     => $this->userId,
                'esim_id'     => $item->esim_id,
                'quantity'    => $item->quantity,
                'total_price' => $totalPrice,
            ]);

            // Mark the cart item as completed
            $item->update(['status' => 'completed']);
        }

        return redirect()->route('cart.index')->with('success', '✅ Order placed successfully! Total: $' . number_format($totalAmount, 2));
    }

    // CheckoutController.php



    public function generateQr(Request $request)
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $totalAmount = $cartItems->sum('total_price');

        // Generate QR code
        $individualInfo = new IndividualInfo(
            bakongAccountID: 'sopheaktra_peng@aclb',
            merchantName: Auth::user()->name ?? 'Guest',
            merchantCity: 'PHNOM PENH',
            currency: KHQRData::CURRENCY_KHR,
            amount: $totalAmount
        );

        $response = BakongKHQR::generateIndividual($individualInfo);

        if ($response->status['code'] !== 0) {
            return response()->json(['error' => 'Failed to generate QR code'], 500);
        }

        $qrString = $response->data['qr'];
        $qrUrl = 'https://quickchart.io/qr?text=' . urlencode($qrString) . '&size=250';

        // Generate a unique MD5 for this checkout session
        $md5 = $response->data['md5'];

        // Store cart IDs and MD5 in session for later confirmation
        session([
            'checkout_cart_ids' => $cartItems->pluck('id')->toArray(),
            'checkout_md5' => $md5
        ]);

        return response()->json([
            'qrUrl' => $qrUrl,
            'amount' => $totalAmount,
            'md5' => $md5
        ]);
    }


    public function confirmPayment(Request $request)
    {
        $userId = Auth::id();

        // Get user's pending cart items
        $cartItems = Cart::where('user_id', $userId)
            ->where('status', 'pending')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'No pending items in cart.');
        }

        // Mark cart items as completed
        $cartItems->each->update(['status' => 'completed']);

        // Optionally, create checkout records
        foreach ($cartItems as $item) {
            Checkout::create([
                'user_id' => $userId,
                'esim_id' => $item->esim_id,
                'quantity' => $item->quantity,
                'total_price' => $item->total_price,
            ]);
        }

        return redirect()->route('cart.index')->with('success', '✅ Payment successful, order completed!');
    }


    public function checkPayment(Request $request)
    {
        $md5 = $request->input('md5');

        if (!$md5) {
            return response()->json(['paid' => false, 'error' => 'Missing transaction ID'], 400);
        }

        $bakongKHQR = new BakongKHQR('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImlkIjoiNDE2ZTY1NWQ0ZGM3NGUwMiJ9LCJpYXQiOjE3NjQ0MDg1MTEsImV4cCI6MTc3MjE4NDUxMX0.DSCa77FRMGSPenx0t6uiyBVPaxSp0Ms7yF4Dgt53Uro');

        $response = $bakongKHQR->checkTransactionByMD5($md5);

        if ($response['responseMessage'] != "Success") {
            return response()->json(['paid' => false, 'error' => [$response, $md5]]);
        }

        $user = Auth::user();
        $userId = $user->id;

        $cartItems = Cart::where('user_id', $userId)
            ->where('status', 'pending')
            ->with('esim.plan') // eager load for efficiency
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['paid' => false, 'error' => 'No pending items in cart.'], 400);
        }

        $totalAmount = 0;
        $planDetails = '';
        $testmessage = [];

        // Group cart items by plan
        $grouped = $cartItems->groupBy(fn($item) => $item->esim->plan->id);

        foreach ($grouped as $planId => $items) {
            $plan = $items->first()->esim->plan;
            $quantity = $items->sum('quantity');
            $totalPrice = $items->sum('total_price');
            $totalAmount += $totalPrice;

            // Collect all eSIM numbers
            $numbers = $items->map(fn($i) => $i->esim->number)->join(', ');

            $planDetails .= "📌 Plan: {$plan->plan_name}\n";
            $planDetails .= "   Quantity: {$quantity}\n";
            $planDetails .= "   Price: \${$totalPrice}\n";
            $planDetails .= "   eSIM Number(s): {$numbers}\n\n";

            // Mark items as completed and create checkout records
            foreach ($items as $item) {
                $item->update(['status' => 'completed']);

                Checkout::create([
                    'user_id' => $item->user_id,
                    'esim_id' => $item->esim_id,
                    'quantity' => $item->quantity,
                    'total_price' => $item->total_price,
                ]);
            }
        }

        // Send email
        try {
            Resend::emails()->send([
                'from' => 'mail@gifshop.msk.solutions',
                'to' => $user->email,
                'subject' => '🎉 Payment Successful!',
                'html' => "
                <h1>Hello {$user->name}</h1>
                <p>Thank you for your purchase! ✅</p>
                <p>Here are the details of your order:</p>
                <pre>{$planDetails}</pre>
                <p><strong>Total Paid:</strong> \${$totalAmount}</p>
                <p>We hope you enjoy your eSIMs! 🌐</p>
            ",
            ]);
            $testmessage .= "mail working ";
        } catch (\Exception $e) {
            $testmessage = [
                $user->name,
                $user->email,
                $planDetails,
                $totalAmount
            ];
        }

        // Send Telegram notification
        try {
            $botToken = '8201599995:AAEluPEzqH3m8ygQ9HTXiwHnv4GyjRosHRk';
            $chatId = '1694517385';

            $message = "🎉 *New Payment Received!*\n\n";
            $message .= "👤 *User:* {$user->name} ({$user->email})\n";
            $message .= "🛒 *Items Purchased:*\n{$planDetails}\n";
            $message .= "💰 *Total Paid:* \${$totalAmount}\n";
            $message .= "✅ Status: Completed";

            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
            $testmessage .= 'telegram working ';
        } catch (\Exception $e) {
        }

        return response()->json([
            'paid' => true,
            'message' => ['Payment confirmed, email sent, and Telegram notified.', $testmessage]
        ]);
    }
}
