<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRController extends Controller
{
    public function generate()
    {
        $googleLoginUrl = route('auth.google', ['from' => 'qr']);
        $qrCode = QrCode::size(300)->generate($googleLoginUrl);
        return view('auth.qr-login', compact('qrCode'));
    }
}
