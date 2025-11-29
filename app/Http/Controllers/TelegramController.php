<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    public function sendTest()
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        $message = "Hello! This is a test message from Laravel.";

        $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        return $response->json();
    }
}
