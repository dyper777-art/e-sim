<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Resend\Laravel\Facades\Resend; // for resend/resend-laravel
use Illuminate\Support\Facades\Auth;

class TestResendController extends Controller
{
    public function sendTestEmail()
    {
        $toEmail = 'dyper777@gmail.com'; // Replace with the email you want to test
        $toName = 'Test User';

        $user = Auth::user();
         Resend::emails()->send([
                'from' => env('MAIL_FROM_ADDRESS'),
                'to' => $user->email,
                'subject' => '🎉 Payment Successful!',
                'html' => "
                <h1>Hello {123}</h1>
                <p>Thank you for your purchase! ✅</p>
                <p>Here are the details of your order:</p>
                <pre>{123}</pre>
                <p><strong>Total Paid:</strong> \${123}</p>
                <p>We hope you enjoy your eSIMs! 🌐</p>
            ",
            ]);

        // try {
        //     Resend::emails()->send([
        //         'from' => env('MAIL_FROM_ADDRESS'),
        //         'to' => $toEmail,
        //         'subject' => 'Test Email from Resend',
        //         'html' => "
        //             <h1>Hello {$toName}</h1>
        //             <p>This is a test email sent using Resend and Laravel!</p>
        //         ",
        //     ]);

        //     return response()->json(['success' => true, 'message' => 'Test email sent!']);
        // } catch (\Exception $e) {
        //     return response()->json(['success' => false, 'message' => $e->getMessage()]);
        // }
    }
}
