<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Resend\Laravel\Facades\Resend; // for resend/resend-laravel
use Illuminate\Support\Facades\Auth;

use Resend\Laravel\Facades\Resend;

class TestResendController extends Controller
{
    public function sendTestEmail()
    {
        Resend::emails()->send([
            'from' => env('MAIL_FROM_ADDRESS'),
            'to' => 'dyper777@gmail.com',
            'subject' => '🎉 Payment Successful!',
            'html' => "
        <h1>Hello {user->name}</h1>
        <p>Thank you for your purchase! ✅</p>
        <p>Here are the details of your order:</p>
        <pre>{planDetails}</pre>
        <p><strong>Total Paid:</strong> \${totalAmount}</p>
        <p>We hope you enjoy your eSIMs! 🌐</p>
    ",
        ]);
    }
}
