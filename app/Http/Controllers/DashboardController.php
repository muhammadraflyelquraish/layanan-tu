<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\LetterDisposition;

class DashboardController extends Controller
{
    function index()
    {
        $thisMonth = date('m');

        // Total pengajuan bulan ini
        $totalPengajuanBulanIni = Letter::whereMonth('created_at', $thisMonth)->count();

        // Total waiting approval
        $totalMenugguPersetujuanBulanIni = LetterDisposition::where('status', 'Diproses')
            ->whereMonth('created_at', $thisMonth)
            ->when(auth()->user()->role_id != 1, function ($query) {
                $query->where('position_id', auth()->user()->role_id);
            })
            ->count();

        // Total selesai
        $totalSelesai = Letter::where('status', 'Selesai')->whereMonth('created_at', $thisMonth)->count();

        // Total ditolak
        $totalTolak = Letter::where('status', 'Ditolak')->whereMonth('created_at', $thisMonth)->count();

        return view('dashboard', compact('totalPengajuanBulanIni', 'totalMenugguPersetujuanBulanIni', 'totalSelesai', 'totalTolak'));
    }
}
