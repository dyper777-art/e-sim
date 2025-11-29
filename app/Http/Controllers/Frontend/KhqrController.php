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


        dd(BakongKHQR::generateIndividual($individualInfo));
    }

    public function checkTransactionByMD5(Request $request)
    {
        // $md5 = $request->md5;
        $bakongKHQR = new BakongKHQR('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImlkIjoiNDE2ZTY1NWQ0ZGM3NGUwMiJ9LCJpYXQiOjE3NjQxNTc0MzYsImV4cCI6MTc3MTkzMzQzNn0.Lf4tfukiWizxJqodxm976a-k_ZhxkoOZSWpgQ2d4AFE');
        $respone = $bakongKHQR->checkTransactionByMD5('a40bddada4ccbfaee26f018ff0ba5196');

        dd($respone);
    }
}
