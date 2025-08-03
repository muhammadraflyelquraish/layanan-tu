<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRController extends Controller
{
    public function generate()
    {
        $loginUrl = url('/login?type=tracking');
        $qrCode = QrCode::size(300)->generate($loginUrl);
        return view('auth.qr-login', compact('qrCode'));
    }
}
