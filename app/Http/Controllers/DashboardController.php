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
        $userLogin = auth()->user();

        $thisMonth = date('m');

        // Total pengajuan bulan ini
        $totalPengajuanBulanIni = Letter::query()
            ->when($userLogin->role_id == 7 || $userLogin->role_id == 8, function ($query) use ($userLogin) {
                $query->where('prodi_id', $userLogin->prodi_id);
            })
            ->whereMonth('created_at', $thisMonth)->count();

        // Total waiting approval
        $totalMenugguPersetujuanBulanIni = LetterDisposition::where('status', 'Diproses')
            ->whereMonth('created_at', $thisMonth)
            ->when($userLogin->role_id != 1, function ($query) use ($userLogin) {
                $query->whereHas('disposition', function ($subQuery) use ($userLogin) {
                    $subQuery->whereHas('approvers', function ($approverQuery) use ($userLogin) {
                        $approverQuery->where('role_id', $userLogin->role_id);
                    });
                });
            })
            ->count();

        // Total selesai
        $totalSelesai = Letter::query()
            ->when($userLogin->role_id == 7 || $userLogin->role_id == 8, function ($query) use ($userLogin) {
                $query->where('prodi_id', $userLogin->prodi_id);
            })
            ->where('status', 'Selesai')
            ->whereMonth('created_at', $thisMonth)->count();

        // Total ditolak
        $totalTolak = Letter::query()
            ->when($userLogin->role_id == 7 || $userLogin->role_id == 8, function ($query) use ($userLogin) {
                $query->where('prodi_id', $userLogin->prodi_id);
            })
            ->where('status', 'Ditolak')
            ->whereMonth('created_at', $thisMonth)->count();

        // Avg rating SPJ
        $avgRatingSpj = SPJRating::where('spj_id', '!=', null)->avg('rating');
        $avgRatingSpj = round($avgRatingSpj, 1);

        // Avg rating Pengajuan
        $avgRatingPengajuan = SPJRating::where('surat_id', '!=', null)->avg('rating');
        $avgRatingPengajuan = round($avgRatingPengajuan, 1);

        // Avg pengajuan
        $avgPengajuanInMinutes = DB::table('t_surat')
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
        // $avgPengajuanInMinutes = DB::table('t_surat')
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

        return view('dashboard', compact('totalPengajuanBulanIni', 'totalMenugguPersetujuanBulanIni', 'totalSelesai', 'totalTolak', 'avgRatingSpj', 'avgRatingPengajuan', 'avgPengajuan', 'avgSpj'));
    }
}
