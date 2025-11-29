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
        $individualInfo = new IndividualInfo(
            bakongAccountID: 'sopheaktra_peng@aclb',
            merchantName: 'Peng Sopheaktra',
            merchantCity: 'PHNOM PENH',
            currency: KHQRData::CURRENCY_KHR,
            amount: 500
        );

        // $qrCodeUrl = BakongKHQR::generateIndividual($individualInfo);

        $response = BakongKHQR::generateIndividual($individualInfo);

        if ($response->status['code'] !== 0) {
            $qrUrl = null;
        } else {
            $qrString = $response->data['qr'];
            // Use QuickChart to render QR code
            $qrUrl = 'https://quickchart.io/qr?text=' . urlencode($qrString) . '&size=250';
        }

        // dd(123);
        return view('qr', compact('qrUrl'));
        }


    public function checkTransactionByMD5(Request $request)
    {
        // $md5 = $request->md5;
        $bakongKHQR = new BakongKHQR('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImlkIjoiNDE2ZTY1NWQ0ZGM3NGUwMiJ9LCJpYXQiOjE3NjQ0MDg1MTEsImV4cCI6MTc3MjE4NDUxMX0.DSCa77FRMGSPenx0t6uiyBVPaxSp0Ms7yF4Dgt53Uro');
        $respone = $bakongKHQR->checkTransactionByMD5('f9d57315dbae3fdc0e52eb70c46f15dd');

        dd($respone['responseMessage']);
    }
}
