@extends('layouts.master')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-3">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Total Permohonan</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">12</h1>
                        <small>Surat</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Total Permohonan Hari Ini</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">4</h1>
                        <small>Surat</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Total Permohonan Berjalan</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">2</h1>
                        <small>Surat</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Total Permohonan Selesai</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">2</h1>
                        <small>Surat</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@push('script')
<script>
    $(function() {

    })
</script>
@endpush