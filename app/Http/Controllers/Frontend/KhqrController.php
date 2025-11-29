<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;
use Symfony\Component\HttpFoundation\Request;

class KhqrController extends Controller
{
    public function generateQRCode(Request $request)
{
    try {
        $individualInfo = new IndividualInfo(
            bakongAccountID: 'sopheaktra_peng@aclb',
            merchantName: 'Peng Sopheaktra',
            merchantCity: 'PHNOM PENH',
            currency: KHQRData::CURRENCY_KHR,
            amount: 500
        );

        $response = BakongKHQR::generateIndividual($individualInfo);

        if ($response->status['code'] !== 0) {
            return response()->json([
                'success' => false,
                'error' => $response->status['message'] ?? 'Failed to generate QR'
            ]);
        }

        $qrString = $response->data['qr'] ?? null;
        if (!$qrString) {
            return response()->json([
                'success' => false,
                'error' => 'QR string not found'
            ]);
        }

        $qrUrl = 'https://quickchart.io/qr?text=' . urlencode($qrString) . '&size=250';

        return response()->json([
            'success' => true,
            'qrUrl' => $qrUrl,
            'amount' => 500,
            'md5' => $response->data['md5'] ?? null // return MD5 for polling
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}



    public function checkTransactionByMD5(Request $request)
    {
        // $md5 = $request->md5;
        $bakongKHQR = new BakongKHQR('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImlkIjoiNDE2ZTY1NWQ0ZGM3NGUwMiJ9LCJpYXQiOjE3NjQ0MDg1MTEsImV4cCI6MTc3MjE4NDUxMX0.DSCa77FRMGSPenx0t6uiyBVPaxSp0Ms7yF4Dgt53Uro');
        $respone = $bakongKHQR->checkTransactionByMD5('f9d57315dbae3fdc0e52eb70c46f15dd');

        dd($respone['responseMessage']);
    }
}
