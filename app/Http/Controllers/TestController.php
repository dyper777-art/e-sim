<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use KHQR\BakongKHQR;

class TestController extends Controller
{
    public function test(Request $request)
    {
        $bakongKHQR = new BakongKHQR('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImlkIjoiNDE2ZTY1NWQ0ZGM3NGUwMiJ9LCJpYXQiOjE3NjQ0MDg1MTEsImV4cCI6MTc3MjE4NDUxMX0.DSCa77FRMGSPenx0t6uiyBVPaxSp0Ms7yF4Dgt53Uro');

        $response = $bakongKHQR->checkTransactionByMD5('21a85838d75b501b0706d709e236652c');

        return response()->json([
            'message' => $response
        ], 200);
    }
}
