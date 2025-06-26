@extends('layouts.master')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-3">
                <a href="{{ route('arsip.index') }}">
                    <div class="ibox">
                        <div class="ibox-title" style="padding: 15px 15px 8px 15px;">
                            <h5>Total Surat</h5>
                            <span class="label label-success float-right">Bulan Ini</span>
                        </div>
                        <div class="ibox-content">
                            <h1 class="no-margins">{{ $totalPengajuanBulanIni }}</h1>
                            <small>Surat</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3">
                <a href="{{ route('letter.index') }}">
                    <div class="ibox ">
                        <div class="ibox-title" style="padding: 15px 15px 8px 15px;">
                            <h5>Menunggu Persetujuan</h5>
                        </div>
                        <div class="ibox-content">
                            <h1 class="no-margins">{{ $totalMenugguPersetujuanBulanIni }}</h1>
                            <small>Surat</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3">
                <a href="{{ route('arsip.index') }}">
                    <div class="ibox">
                        <div class="ibox-title" style="padding: 15px 15px 8px 15px;">
                            <h5>Total Selesai</h5>
                            <span class="label label-success float-right">Bulan Ini</span>
                        </div>
                        <div class="ibox-content">
                            <h1 class="no-margins">{{ $totalSelesai }}</h1>
                            <small>Surat</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3">
                <a href="{{ route('arsip.index') }}">
                    <div class="ibox ">
                        <div class="ibox-title" style="padding: 15px 15px 8px 15px;">
                            <h5>Total Ditolak</h5>
                            <span class="label label-success float-right">Bulan Ini</span>
                        </div>
                        <div class="ibox-content">
                            <h1 class="no-margins">{{ $totalTolak }}</h1>
                            <small>Surat</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Kinerja Pelayanan</h5>
                    </div>
                    <a href="{{ route('spj.index') }}">
                        <div class="ibox-content ibox-heading">
                            <h3>Rating SPJ
                                <div class="stat-percent text-primary"><i class="fa fa-star"></i> {{ $avgRating }}</div>
                            </h3>
                        </div>
                    </a>
                    <a href="{{ route('letter.index') }}">
                        <div class="ibox-content">
                            <div>
                                <div class="float-right text-right">
                                    <span class="font-bold"><i class="fa fa-clock-o"></i> {{ $avgPengajuan }}</span>
                                </div>
                                <h4>Rata-Rata Waktu Proses Pengajuan</h4>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('spj.index') }}">
                        <div class="ibox-content">
                            <div>
                                <div class="float-right text-right">
                                    <span class="font-bold"><i class="fa fa-clock-o"></i> {{ $avgSpj }}</span>
                                </div>
                                <h4>Rata-Rata Waktu Proses SPJ</h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection