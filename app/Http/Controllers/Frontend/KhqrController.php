<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KHqrController extends Controller
{
    public function generateQRCode(Request $request)
    {
        return response()->json([
            'message' => 'hello'
        ], 200);
    }

    public function checkTransactionByMD5(Request $request)
    {
        $md5 = $request->input('md5');

        if (!$md5) {
            Log::warning('MD5 not provided in request');
            return response()->json(['success' => false, 'error' => 'MD5 is required'], 400);
        }

        $token = env('BAKONG_API_TOKEN');
        if (!$token) {
            Log::error('Bakong API token is missing.');
            return response()->json(['success' => false, 'error' => 'Missing Bakong API token'], 500);
        }

        try {
            $bakongKHQR = new BakongKHQR($token);
            $response = $bakongKHQR->checkTransactionByMD5($md5);

            if (($response['responseMessage'] ?? '') !== "Success") {
                Log::error('Bakong API checkTransactionByMD5 failed', ['md5' => $md5, 'response' => $response]);
                return response()->json([
                    'success' => false,
                    'error' => 'Transaction not found or pending',
                    'details' => $response
                ], 500);
            }

            return response()->json([
                'success' => true,
                'transaction' => $response['data'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('Exception in checkTransactionByMD5', ['md5' => $md5, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'error' => 'Bakong API error: ' . $e->getMessage()
            ], 500);
        }
    }
}
