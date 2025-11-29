<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Resend\Laravel\Facades\Resend; // for resend/resend-laravel
use Illuminate\Support\Facades\Auth;

use Resend\Resend;

class TestResendController extends Controller
{
    public function sendTestEmail()
    {
        try {
    $resend = new Resend('re_Px1ZvZ8b_N97WP4XZ3kSsE4dyPmWApQ1p'); // or pass API key directly
    $resend->emails()->send([
        'from' => 'mail@gifshop.msk.solutions',
        'to' => 'user->email',
        'subject' => '🎉 Payment Successful!',
        'html' => "
            <h1>Hello {'user->name'}</h1>
            <p>Thank you for your purchase! ✅</p>
            <p>Here are the details of your order:</p>
            <pre>{planDetails}</pre>
            <p><strong>Total Paid:</strong> \${totalAmount}</p>
            <p>We hope you enjoy your eSIMs! 🌐</p>
        ",
    ]);

    $testmessage = "mail working ";

} catch (\Exception $e) {
    $testmessage = [
        'error' => $e->getMessage(),
        'user' => '123',
        'planDetails' => ',',
        'totalAmount' => 'uuy'
    ];
}
    }
}
