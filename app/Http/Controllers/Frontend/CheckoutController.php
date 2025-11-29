<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Models\Checkout;
use App\Models\Esim;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

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
                ->with('esim.plan')
                ->get();
            return $next($request);
        });
    }

    // Show checkout page
    public function index()
    {
        $cart = $this->cartItems;

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Group cart items by plan
        $esimsGrouped = $cart->groupBy(fn($item) => $item->esim->plan->id)
            ->map(function ($group) {
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

    // Generate QR Code
    public function generateQr(Request $request)
    {
        $cartItems = $this->cartItems;

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $totalAmount = $cartItems->sum('total_price');

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
        $md5 = $response->data['md5'];

        // Store in session
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

    // Manual payment API
    public function manualPayment(Request $request)
    {
        $user = Auth::user();
        $cartItems = $this->cartItems;

        if ($cartItems->isEmpty()) {
            return response()->json(['paid' => false, 'error' => 'No pending items'], 400);
        }

        return response()->json(
            $this->finalizePayment($cartItems, $user)
        );
    }

    // Check payment via MD5 or manual
    public function checkPayment(Request $request)
    {
        $user = Auth::user();
        $cartItems = $this->cartItems;

        if ($cartItems->isEmpty()) {
            return response()->json(['paid' => false, 'error' => 'No pending items'], 400);
        }

        // Manual pay
        if ($request->manualPay) {
            return response()->json($this->finalizePayment($cartItems, $user));
        }

        // MD5 check
        $md5 = $request->md5;
        if (!$md5) {
            return response()->json(['paid' => false, 'error' => 'Missing md5'], 400);
        }

        $bakong = new BakongKHQR(env('BAKONG_API_TOKEN'));

        try {
            $response = $bakong->checkTransactionByMD5($md5);
        } catch (\Exception $e) {
            return response()->json(['paid' => false, 'error' => $e->getMessage()], 500);
        }

        if (($response['responseMessage'] ?? '') !== "Success") {
            return response()->json(['paid' => false]);
        }

        return response()->json($this->finalizePayment($cartItems, $user));
    }

    // -------------------------
    // HELPER FUNCTIONS
    // -------------------------

    // Complete checkout, send email, Telegram
    private function finalizePayment($cartItems, $user)
    {
        [$planDetails, $totalAmount] = $this->generatePlanDetailsAndTotal($cartItems);

        $this->completeCartItems($cartItems, $user);

        $emailStatus = $this->sendEmailReceipt($user, $planDetails, $totalAmount);
        $telegramStatus = $this->sendTelegramNotification($user, $planDetails, $totalAmount);

        return [
            'paid' => true,
            'total' => $totalAmount,
            'planDetails' => $planDetails,
            'email' => $emailStatus,
            'telegram' => $telegramStatus
        ];
    }

    // Generate plan details string and total
    private function generatePlanDetailsAndTotal($cartItems)
    {
        $planDetails = '';
        $totalAmount = 0;

        // Group cart items by plan
        $grouped = $cartItems->groupBy(fn($item) => $item->esim->plan->id);

        foreach ($grouped as $items) {
            $plan = $items->first()->esim->plan;

            // Total quantity of eSIMs in this plan
            $quantity = $items->sum('quantity');

            // Total price for this plan
            $totalPrice = $items->sum('total_price');
            $totalAmount += $totalPrice;

            // Get all eSIM numbers for this plan

            $planDetails .= "📌 Plan: {$plan->plan_name}\n";
            $planDetails .= "   Quantity: {$quantity}\n";
            $planDetails .= "   Price: \${$totalPrice}\n";
        }

        return [$planDetails, $totalAmount];
    }


    // Complete cart items and create checkout records
    private function completeCartItems($cartItems, $user)
    {
        foreach ($cartItems as $item) {
            $item->update(['status' => 'completed']);

            Checkout::create([
                'user_id' => $user->id,
                'esim_id' => $item->esim_id,
                'quantity' => $item->quantity,
                'total_price' => $item->total_price,
            ]);
        }
    }

    // Send email receipt
    private function sendEmailReceipt($user, $planDetails, $totalAmount)
    {
        try {

            Resend::emails()->send([
                'from' => env('MAIL_FROM_ADDRESS'),
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


            return 'Email sent successfully';
        } catch (\Exception $e) {
            Log::error('Email sending failed', ['error' => $e->getMessage()]);
            return 'Email failed: ' . $e->getMessage();
        }
    }

    // Send Telegram notification
    private function sendTelegramNotification($user, $planDetails, $totalAmount)
    {
        try {
            $botToken = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');

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

            return 'Telegram sent successfully';
        } catch (\Exception $e) {
            Log::error('Telegram error', ['error' => $e->getMessage()]);
            return 'Telegram failed: ' . $e->getMessage();
        }
    }
}
