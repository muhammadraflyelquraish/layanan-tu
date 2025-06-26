<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\LetterDisposition;
use App\Models\SPJRating;
use Illuminate\Support\Facades\DB;

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

        // Avg rating
        $avgRating = SPJRating::avg('rating');
        $avgRating = round($avgRating, 1);

        // Avg pengajuan
        $avgPengajuanInMinutes = DB::table('t_letter')
            ->whereNotNull('tanggal_diterima')
            ->whereNotNull('tanggal_selesai')
            ->selectRaw("AVG(EXTRACT(EPOCH FROM tanggal_selesai - tanggal_diterima) / 60) as avg_minutes")
            ->value('avg_minutes');

        $avgPengajuan = "";
        if ($avgPengajuanInMinutes === null) {
            $avgPengajuan = '-';
        } else {
            $days = floor($avgPengajuanInMinutes / 1440);
            $remainingMinutes = $avgPengajuanInMinutes % 1440;

            $hours = floor($remainingMinutes / 60);
            $minutes = round($remainingMinutes % 60);

            $avgPengajuan = "{$days} Hari, {$hours} Jam, {$minutes} Menit";
        }

        // Avg SPJ
        $avgSpjInMinutes = DB::table('t_spj')
            ->whereNotNull('tanggal_proses')
            ->whereNotNull('tanggal_selesai')
            ->selectRaw("AVG(EXTRACT(EPOCH FROM tanggal_selesai - tanggal_proses) / 60) as avg_minutes")
            ->value('avg_minutes');

        $avgSpj = "";
        if ($avgSpjInMinutes === null) {
            $avgSpj = '-';
        } else {
            $days = floor($avgSpjInMinutes / 1440);
            $remainingMinutes = $avgSpjInMinutes % 1440;

            $hours = floor($remainingMinutes / 60);
            $minutes = round($remainingMinutes % 60);

            $avgSpj = "{$days} Hari, {$hours} Jam, {$minutes} Menit";
        }

        // // Avg pengajuan
        // $avgPengajuanInMinutes = DB::table('t_letter')
        //     ->whereNotNull('tanggal_diterima')
        //     ->whereNotNull('tanggal_selesai')
        //     ->selectRaw("AVG(TIMESTAMPDIFF(MINUTE, tanggal_diterima, tanggal_selesai)) as avg_minutes")
        //     ->value('avg_minutes');

        // $avgPengajuan = "";
        // if ($avgPengajuanInMinutes === null) {
        //     $avgPengajuan = '-';
        // } else {
        //     $days = floor($avgPengajuanInMinutes / 1440);
        //     $remainingMinutes = $avgPengajuanInMinutes % 1440;

        //     $hours = floor($remainingMinutes / 60);
        //     $minutes = round($remainingMinutes % 60);

        //     $avgPengajuan = "{$days} Hari, {$hours} Jam, {$minutes} Menit";
        // }

        // // Avg SPJ
        // $avgSpjInMinutes = DB::table('t_spj')
        //     ->whereNotNull('tanggal_proses')
        //     ->whereNotNull('tanggal_selesai')
        //     ->selectRaw("AVG(TIMESTAMPDIFF(MINUTE, tanggal_proses, tanggal_selesai)) as avg_minutes")
        //     ->value('avg_minutes');

        // $avgSpj = "";
        // if ($avgSpjInMinutes === null) {
        //     $avgSpj = '-';
        // } else {
        //     $days = floor($avgSpjInMinutes / 1440);
        //     $remainingMinutes = $avgSpjInMinutes % 1440;

        //     $hours = floor($remainingMinutes / 60);
        //     $minutes = round($remainingMinutes % 60);

        //     $avgSpj = "{$days} Hari, {$hours} Jam, {$minutes} Menit";
        // }

        return view('dashboard', compact('totalPengajuanBulanIni', 'totalMenugguPersetujuanBulanIni', 'totalSelesai', 'totalTolak', 'avgRating', 'avgPengajuan', 'avgSpj'));
    }
}
