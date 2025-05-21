@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Proposal</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <strong>Proposal</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    @if(auth()->user()->role_id != 2)
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h4>Filter</h4>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Search</label>
                                <input name="search" id="search" class="form-control" placeholder="Kode/Pemohon/Surat...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Pemohon</label>
                                <select name="pemohon_id" id="pemohon_id" class="form-control">
                                    <option value="">--Filter Pemohon--</option>
                                    @foreach($pemohon as $pm)
                                    <option value="{{ $pm->id }}">{{ $pm->name }} ({{$pm->no_identity}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">--Filter Status--</option>
                                    <option value="Diproses">Diproses</option>
                                    <option value="Selesai">Selesai</option>
                                    <option value="Menuggu Konfirmasi TU">Menuggu Konfirmasi TU</option>
                                    @foreach(App\Models\Role::where('is_disposition', '=', true)->pluck('name', 'id') as $id => $role)
                                    <option value="{{ $id }}">Menunggu Approval {{ $role }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Jenis Surat</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">--Filter Jenis Surat--</option>
                                    <option value="1">Disertai Pengajuan Data</option>
                                    <option value="0">Tidak Pengajuan Data</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success" style="margin-top: 26px;" id="applyFilter" type="button">Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    @if(auth()->user()->role_id != 2)
                    <h5><button class="btn btn-primary btn-sm" data-toggle="modal" data-mode="add" data-target="#ModalAddEdit"><i class="fa fa-plus-square mr-1"></i> Buat Pengajuan</button></h5>
                    @else
                    <h5><button class="btn btn-primary btn-sm" data-toggle="modal" data-mode="add" data-target="#ModalAddEditPemohon"><i class="fa fa-plus-square mr-1"></i> Buat Pengajuan</button></h5>
                    @endif
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">

                        @if (auth()->user()->role_id == 2)
                        <table class="table table-striped table-bordered table-hover dataTables" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1px">No</th>
                                    <th>Kode Pengajuan</th>
                                    <th>Asal Surat</th>
                                    <th>Jenis Surat</th>
                                    <th>Hal</th>
                                    <th>Status</th>
                                    <th>Proposal</th>
                                    <th class="text-right" width="1px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        @else
                        <table class="table table-striped table-bordered table-hover dataTables" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1px">No</th>
                                    <th>Kode Pengajuan</th>
                                    <th>Pemohon</th>
                                    <th>Tanggal Diterima</th>
                                    <th>Nomor Surat</th>
                                    <th>Asal Surat</th>
                                    <th>Hal</th>
                                    <th>Untuk</th>
                                    <th>Pengajuan Dana</th>
                                    <th>Status</th>
                                    <th>Proposal</th>
                                    <th class="text-right" width="1px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalAddEditPemohon" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formAddEditPemohon" method="POST">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Tanggal Surat</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control" id="tanggal_surat" name="tanggal_surat" tabindex="3" required>
                            <small class="text-danger" id="tanggal_surat_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Asal Surat</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="asal_surat" name="asal_surat" tabindex="4" required>
                            <small class="text-danger" id="asal_surat_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Jenis Surat</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="disertai_dana" id="disertai_dana" required>
                                <option value="">--Pilih Jenis Dokumen--</option>
                                <option value="1">Disertai Pengajuan Dana</option>
                                <option value="0">Tanpa Pengajuan Dana</option>
                            </select>
                            <small class="text-danger" id="disertai_dana_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Hal</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" id="hal" name="hal" required tabindex="5"></textarea>
                            <small class="text-danger" id="hal_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Proposal</label>
                        <div class="col-sm-8">
                            <input type="file" class="form-control" id="proposal_file" name="proposal_file" tabindex="7">
                            <small class="text-danger" id="proposal_file_error"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-rectangle-o mr-1"></i>Tutup [Esc]</button>
                    <button type="submit" class="btn btn-primary ladda-button ladda-button-demo" data-style="zoom-in" id="submit" tabindex="8"><i class="fa fa-check-square mr-1"></i>Simpan [Enter]</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalAddEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formAddEdit" method="POST">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Pemohon</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="pemohon_id" id="pemohon_id" required>
                                <option value="" selected disabled>Pilih Pemohon</option>
                                @foreach($pemohon as $pm)
                                @php
                                $pemohonId = $pm->id;
                                @endphp
                                <option value="{{ $pemohonId }}" @selected(old('pemohon_id')==$pemohonId)>{{ $pm->name }} ({{$pm->no_identity}})</option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="pemohon_id_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Nomor Agenda</label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" id="nomor_agenda" name="nomor_agenda" tabindex="2" required>
                            <small class="text-danger" id="nomor_agenda_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Tanggal Surat</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control" id="tanggal_surat" name="tanggal_surat" tabindex="3" required>
                            <small class="text-danger" id="tanggal_surat_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Nomor Surat</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="nomor_surat" name="nomor_surat" tabindex="3" required>
                            <small class="text-danger" id="nomor_surat_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Asal Surat</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="asal_surat" name="asal_surat" tabindex="4" required>
                            <small class="text-danger" id="asal_surat_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Hal</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" id="hal" name="hal" required tabindex="5"></textarea>
                            <small class="text-danger" id="hal_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Tanggal Diterima</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control" id="tanggal_diterima" name="tanggal_diterima" tabindex="6" required>
                            <small class="text-danger" id="tanggal_diterima_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Untuk</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="untuk" name="untuk" tabindex="7" required>
                            <small class="text-danger" id="untuk_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Jenis Surat</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="disertai_dana" id="disertai_dana" required>
                                <option value="1">Disertai Pengajuan Dana</option>
                                <option value="0">Tanpa Pengajuan Dana</option>
                            </select>
                            <small class="text-danger" id="disertai_dana_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Proposal</label>
                        <div class="col-sm-8">
                            <input type="file" class="form-control" id="proposal_file" name="proposal_file" tabindex="7">
                            <small class="text-danger" id="proposal_file_error"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-rectangle-o mr-1"></i>Tutup [Esc]</button>
                    <button type="submit" class="btn btn-primary ladda-button ladda-button-demo" data-style="zoom-in" id="submit" tabindex="8"><i class="fa fa-check-square mr-1"></i>Simpan [Enter]</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDetail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title">Detail Pengajuan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <h5>Informasi Surat</h5>
                    <table class="table" id="detailInformation">
                        <tr>
                            <th>Kode Pengajuan</th>
                            <td class="text-center">:</td>
                            <td id="kode"></td>
                            <th class="text-right">Pemohon</th>
                            <td class="text-center">:</td>
                            <td id="pemohon_id"></td>
                        </tr>
                        <tr>
                            <th>Nomor Agenda</th>
                            <td class="text-center">:</td>
                            <td id="nomor_agenda"></td>
                            <th class="text-right">Diterima Tanggal</th>
                            <td class="text-center">:</td>
                            <td id="tanggal_diterima"></td>
                        </tr>
                        <tr>
                            <th>Tanggal Surat</th>
                            <td class="text-center">:</td>
                            <td id="tanggal_surat"></td>
                            <th class="text-right">Untuk</th>
                            <td class="text-center">:</td>
                            <td id="untuk"></td>
                        </tr>
                        <tr>
                            <th>Nomor Surat</th>
                            <td class="text-center">:</td>
                            <td id="nomor_surat"></td>
                            <th class="text-right">Status</th>
                            <td class="text-center">:</td>
                            <td id="status"></td>
                        </tr>
                        <tr>
                            <th>Asal Surat</th>
                            <td class="text-center">:</td>
                            <td id="asal_surat"></td>
                            <th class="text-right">File</th>
                            <td class="text-center">:</td>
                            <td id="proposal_file"></td>
                        </tr>
                        <tr>
                            <th>Hal</th>
                            <td class="text-center">:</td>
                            <td id="hal"></td>
                            <th class="text-right">Jenis Surat</th>
                            <td class="text-center">:</td>
                            <td id="disertai_dana"></td>
                        </tr>
                        <tr>
                            <th>Alasan Penolakan</th>
                            <td class="text-center">:</td>
                            <td id="alasan_penolakan" colspan="4"></td>
                        </tr>
                    </table>
                    <h5>Detail Disposisi</h5>
                    <table class="table table-bordered" id="detailDisposition">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Disposisi</th>
                                <th>Status</th>
                                <th>Tanggal Diterima</th>
                                <th>Tanggal Proses</th>
                                <th>Diverfikasi Oleh</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-rectangle-o mr-1"></i>Tutup [Esc]</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDisposition" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formDisposition" method="PUT">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Konfirmasi</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Status</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="status" id="status" required>
                                <option value="" disabled selected>--Pilih Status--</option>
                                <option value="Disposisi">{{ auth()->user()->role_id == 3 ? "Disposisi" : "Disetujui" }}</option>
                                <option value="Ditolak">Tolak</option>
                                @if (auth()->user()->role_id == 3)
                                <option value="Selesai">Selesai</option>
                                @endif
                            </select>
                            <small class="text-danger" id="status_error"></small>
                        </div>
                    </div>
                    <div class="form-group row" hidden>
                        <label class="col-sm-4 col-form-label">Tujuan Disposisi</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="position_id" id="position_id" required>
                                <option value="" disabled selected>Pilih Tujuan</option>
                                @foreach(App\Models\Role::where('is_disposition', '=', true)->where('id', '!=', auth()->user()->role_id)->pluck('name', 'id') as $id => $role)
                                <option value="{{ $id }}">{{ $role }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="position_id_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Catatan</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" id="keterangan" name="keterangan"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-rectangle-o mr-1"></i>Tutup [Esc]</button>
                    <button type="submit" class="btn btn-primary ladda-button ladda-button-demo" data-style="zoom-in" id="submit" tabindex="8"><i class="fa fa-check-square mr-1"></i>Simpan [Enter]</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalConfirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formConfirmation" method="PUT">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Konfirmasi</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Status</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="status" id="status" required>
                                <option value="" disabled selected>Pilih Status</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                            <small class="text-danger" id="status_error"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-rectangle-o mr-1"></i>Tutup [Esc]</button>
                    <button type="submit" class="btn btn-primary ladda-button ladda-button-demo" data-style="zoom-in" id="submit" tabindex="8"><i class="fa fa-check-square mr-1"></i>Simpan [Enter]</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        //BASE
        let ladda = $('.ladda-button-demo').ladda();

        function LaddaStart() {
            ladda.ladda('start');
        }

        function LaddaAndDrawTable() {
            ladda.ladda('stop');
            serverSideTable.draw()
        }

        function sweetalert(title, msg, type, timer = 60000, confirmButton = true) {
            swal({
                title: title,
                text: msg,
                type: type,
                timer: timer,
                showConfirmButton: confirmButton
            });
        }

        $("#formAddEdit").validate({
            messages: {
                nomor_agenda: "Nomor agenda tidak boleh kosong",
                pemohon_id: "Nama pemohon tidak boleh kosong",
                tanggal_surat: "Tanggal surat tidak boleh kosong",
                nomor_surat: "Nomor surat tidak boleh kosong",
                asal_surat: "Asal surat tidak boleh kosong",
                hal: "Hal tidak boleh kosong",
                tanggal_diterima: "Tanggal diterima tidak boleh kosong",
                untuk: "Untuk tidak boleh kosong",
                disertai_dana: "Jenis surat tidak boleh kosong",
            },
            success: function(messages) {
                $(messages).remove();
            },
            errorPlacement: function(error, element) {
                let name = element.attr("name");
                $("#" + name + "_error").text(error.text());
            },
            submitHandler: function(form) {
                LaddaStart()
                $.ajax({
                    url: $(form).attr('action'),
                    type: $(form).attr('method'),
                    data: new FormData(form),
                    contentType: false,
                    processData: false,
                    dataType: 'JSON',
                    success: function(res) {
                        $('#ModalAddEdit').modal('hide')
                        LaddaAndDrawTable()
                        sweetalert('Berhasil', res.msg, null, 500, false)
                    },
                    error: function(res) {
                        LaddaAndDrawTable()
                        sweetalert('Gagal', 'Terjadi kesalahan', 'error')
                    }
                })
            }
        });

        $("#formAddEditPemohon").validate({
            messages: {
                tanggal_surat: "Tanggal surat tidak boleh kosong",
                asal_surat: "Asal surat tidak boleh kosong",
                hal: "Hal tidak boleh kosong",
                disertai_dana: "Jenis surat tidak boleh kosong",
            },
            success: function(messages) {
                $(messages).remove();
            },
            errorPlacement: function(error, element) {
                let name = element.attr("name");
                $("#" + name + "_error").text(error.text());
            },
            submitHandler: function(form) {
                LaddaStart()
                $.ajax({
                    url: $(form).attr('action'),
                    type: $(form).attr('method'),
                    data: new FormData(form),
                    contentType: false,
                    processData: false,
                    dataType: 'JSON',
                    success: function(res) {
                        $('#ModalAddEdit').modal('hide')
                        LaddaAndDrawTable()
                        sweetalert('Berhasil', res.msg, null, 500, false)
                    },
                    error: function(res) {
                        LaddaAndDrawTable()
                        sweetalert('Gagal', 'Terjadi kesalah', 'error')
                    }
                })
            }
        });

        let columns = [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                searchable: false,
                orderable: false,
                className: 'text-center'
            },
            {
                data: 'kode',
                name: 'kode'
            },
            {
                data: 'pemohon.name',
                name: 'pemohon.name'
            },
            {
                data: 'tanggal_diterima',
                name: 'tanggal_diterima'
            },
            {
                data: 'nomor_surat',
                name: 'nomor_surat'
            },
            {
                data: 'asal_surat',
                name: 'asal_surat'
            },
            {
                data: 'hal',
                name: 'hal'
            },
            {
                data: 'untuk',
                name: 'untuk'
            },
            {
                data: 'disertai_dana',
                name: 'disertai_dana'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'file.original_name',
                name: 'file.original_name'
            },
            {
                data: 'action',
                name: 'action',
                searchable: false,
                orderable: false
            }
        ]

        if ("{{auth()->user()->role_id}}" == 2) {
            columns = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false,
                    className: 'text-center'
                },
                {
                    data: 'kode',
                    name: 'kode'
                },
                {
                    data: 'asal_surat',
                    name: 'asal_surat'
                },
                {
                    data: 'disertai_dana',
                    name: 'disertai_dana'
                },
                {
                    data: 'hal',
                    name: 'hal'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'file.original_name',
                    name: 'file.original_name'
                },
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    orderable: false
                }
            ]
        }
        let serverSideTable = $('.dataTables').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [1, 'desc']
            ],
            ajax: {
                url: "{{ route('letter.data') }}",
            },
            columns: columns,
            search: {
                regex: true
            }
        });

        $('#applyFilter').on('click', function() {
            serverSideTable.ajax.reload();
        });

        $('#ModalAddEdit').on('shown.bs.modal', function(e) {
            let button = $(e.relatedTarget)
            let modal = $(this)
            if (button.data('mode') == 'edit') {
                let id = button.data('integrity')
                let closeTr = button.closest('tr')
                $('#formAddEdit').attr('action', '{{ route("letter.store") }}/' + id).attr('method', 'PUT')

                modal.find('#modal-title').text('Edit Pengajuan');
                $.get('{{ route("letter.store") }}/' + id, function(app) {
                    modal.find('#pemohon_id').val(app.pemohon_id)
                    modal.find('#nomor_agenda').val(app.nomor_agenda)
                    modal.find('#tanggal_surat').val(app.tanggal_surat)
                    modal.find('#nomor_surat').val(app.nomor_surat)
                    modal.find('#asal_surat').val(app.asal_surat)
                    modal.find('#hal').val(app.hal)
                    modal.find('#tanggal_diterima').val(app.tanggal_diterima)
                    modal.find('#untuk').val(app.untuk)
                    modal.find('#disertai_dana').val(app.disertai_dana ? "1" : "0")
                })
            } else {
                $('#formAddEdit').trigger('reset').attr('action', '{{ route("letter.store") }}').attr('method', 'POST')
                modal.find('#modal-title').text('Buat Pengajuan');
            }
        })

        $('#ModalAddEditPemohon').on('shown.bs.modal', function(e) {
            let button = $(e.relatedTarget)
            let modal = $(this)
            $('#formAddEditPemohon').trigger('reset').attr('action', '{{ route("letter.store") }}').attr('method', 'POST')
            modal.find('#modal-title').text('Buat Pengajuan');
        })

        $('#ModalDetail').on('shown.bs.modal', function(e) {
            let button = $(e.relatedTarget)
            let modal = $(this)
            let id = button.data('integrity')

            $.get('{{ route("letter.store") }}/' + id, function(app) {
                let dateOfLetter = ''
                if (app.tanggal_surat) {
                    const dateOfLetterObj = new Date(app.tanggal_surat);
                    dateOfLetter = `
                    ${dateOfLetterObj.getDate().toString().padStart(2, '0')}
                    ${dateOfLetterObj.toLocaleString('id-ID', {month: 'long'})}
                    ${dateOfLetterObj.getFullYear()}
                `;
                }

                let receivedDate = ''
                if (app.tanggal_diterima) {
                    const receivedDateObj = new Date(app.tanggal_diterima);
                    receivedDate = `
                        ${receivedDateObj.getDate().toString().padStart(2, '0')}
                        ${receivedDateObj.toLocaleString('id-ID', {month: 'long'})}
                        ${receivedDateObj.getFullYear()}
                    `;
                }

                let jenisSurat = 'Tanpa Pengajuan Dana'
                if (app.disertai_dana == true) {
                    jenisSurat = 'Disertai Pengajuan Dana'
                }

                modal.find('#detailInformation').find('#kode').text(app.kode);
                modal.find('#detailInformation').find('#nomor_agenda').text(app.nomor_agenda);
                modal.find('#detailInformation').find('#pemohon_id').text(`${app.pemohon.name} (${app.pemohon.no_identity})`);
                modal.find('#detailInformation').find('#tanggal_surat').text(dateOfLetter);
                modal.find('#detailInformation').find('#nomor_surat').text(app.nomor_surat);
                modal.find('#detailInformation').find('#asal_surat').text(app.asal_surat);
                modal.find('#detailInformation').find('#hal').text(app.hal);
                modal.find('#detailInformation').find('#tanggal_diterima').text(receivedDate);
                modal.find('#detailInformation').find('#untuk').text(app.untuk);
                modal.find('#detailInformation').find('#disertai_dana').text(jenisSurat);
                modal.find('#detailInformation').find('#alasan_penolakan').text(app.alasan_penolakan);
                modal.find('#detailInformation').find('#proposal_file').html(`<a href="#">${app.file.original_name}</a>`);
                modal.find('#detailInformation').find('#status').html(`<span class="badge badge-sm ${app.status === 'Selesai' ? 'badge-primary' : app.status === 'Ditolak' ? 'badge-danger' : 'badge-warning'}">${app.status}</span>`);

                // disposition detail
                modal.find('#detailDisposition').find('tbody').children().remove()

                for (let index = 0; index < app.dispositions.length; index++) {
                    const element = app.dispositions[index];

                    let recievedDate = ''
                    if (element.tanggal_diterima) {
                        const recievedDateObj = new Date(element.tanggal_diterima);
                        recievedDate = `
                        ${recievedDateObj.getDate().toString().padStart(2, '0')}
                        ${recievedDateObj.toLocaleString('id-ID', {month: 'long'})}
                        ${recievedDateObj.getFullYear()}
                    `;
                    }

                    let approvedDate = ''
                    if (element.tanggal_diproses) {
                        const approvedDateObj = new Date(element.tanggal_diproses);
                        approvedDate = `
                        ${approvedDateObj.getDate().toString().padStart(2, '0')}
                        ${approvedDateObj.toLocaleString('id-ID', {month: 'long'})}
                        ${approvedDateObj.getFullYear()}
                    `;
                    }

                    modal.find('#detailDisposition').find('tbody').append(`
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td>${element?.position?.name || '-'}</td>
                            <td><span class="badge badge-sm badge-${element.status === 'Diproses' || element.status === 'Menunggu Konfirmasi TU' ? 'warning' : element.status === 'Disposisi' ? 'primary' : element.status === 'Ditolak' ? 'danger' : 'info'}">${element.status}</span></td>
                            <td>${recievedDate || '-'}</td>
                            <td>${approvedDate || '-'}</td>
                            <td>${element?.verifikator?.name || '-'}</td>
                            <td>${element.keterangan || '-'}</td>
                        </tr>
                    `);
                }
            })
        })

        $('#ModalDisposition').on('shown.bs.modal', function(e) {
            let button = $(e.relatedTarget)
            let modal = $(this)
            let id = button.data('integrity')
            $('#formDisposition').attr('action', "{{ url('/letter/') }}/" + id + '/disposition').attr('method', 'PUT')
        })

        $('#ModalDisposition').find('#status').change(function() {
            const selectedValue = $(this).val();
            const keuanganRoleId = "{{ auth()->user()->role_id }}"

            if (selectedValue == "Disposisi" && keuanganRoleId != "5") {
                // Show the hidden row that contains the position_id select
                $('#ModalDisposition').find('#position_id').closest('.form-group').removeAttr('hidden');
            } else {
                // Optionally hide it again if value is not "Disposisi"
                $('#ModalDisposition').find('#position_id').closest('.form-group').attr('hidden', true);
            }
        });


        $("#formDisposition").validate({
            messages: {
                status: "Status tidak boleh kosong",
            },
            success: function(messages) {
                $(messages).remove();
            },
            errorPlacement: function(error, element) {
                let name = element.attr("name");
                $("#" + name + "_error").text(error.text());
            },
            submitHandler: function(form) {
                LaddaStart()
                $.ajax({
                    url: $(form).attr('action'),
                    type: $(form).attr('method'),
                    data: $(form).serialize(),
                    dataType: 'JSON',
                    success: function(res) {
                        $('#ModalDisposition').modal('hide')
                        LaddaAndDrawTable()
                        sweetalert('Berhasil', res.msg, null, 500, false)
                        $(form)[0].reset(); // Clear form after success
                    },
                    error: function(res) {
                        LaddaAndDrawTable()
                        sweetalert('Gagal', 'Terjadi kesalah', 'error')
                    }
                })
            }
        });

        $(document).on('click', '#delete', function(e) {
            let id = $(this).data('integrity')
            let name = $(this).closest('tr').find('td:eq(1)').text()
            swal({
                title: "Hapus?",
                text: `Data "${name}" akan terhapus!`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ya, hapus!",
                closeOnConfirm: false
            }, function() {
                swal.close()
                $.ajax({
                    url: "{{ route('letter.store') }}/" + id,
                    type: "DELETE",
                    dataType: 'json',
                    success: function(response) {
                        LaddaAndDrawTable()
                        sweetalert('Berhasil', `Data berhasil dihapus.`, null, 500, false)
                    },
                    error: function(response) {
                        LaddaAndDrawTable()
                        sweetalert('Tidak terhapus!', 'Terjadi kesalahan saat menghapus data.', 'error')
                    }
                })
            });
        })

        //Logout
        $(document).on('click', '#confirmation', function(e) {
            swal({
                title: "Selesai?",
                text: 'Click "Ya" untuk melanjutkan',
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ya, Logout!",
                closeOnConfirm: false
            }, function() {
                swal.close();
                $.ajax({
                    url: "{{ route('logout') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(response) {
                        window.location.href = "{{ route('login') }}"
                    },
                })
            });
        })

        $('#ModalConfirmation').on('shown.bs.modal', function(e) {
            let button = $(e.relatedTarget)
            let modal = $(this)
            let id = button.data('integrity')
            $('#formConfirmation').attr('action', "{{ url('/application/') }}/" + id + '/confirmation').attr('method', 'PUT')
        })

        $("#formConfirmation").validate({
            submitHandler: function(form) {
                LaddaStart()
                $.ajax({
                    url: $(form).attr('action'),
                    type: $(form).attr('method'),
                    data: $(form).serialize(),
                    dataType: 'JSON',
                    success: function(res) {
                        $('#ModalConfirmation').modal('hide')
                        LaddaAndDrawTable()
                        sweetalert('Berhasil', res.msg, null, 500, false)
                    },
                    error: function(res) {
                        LaddaAndDrawTable()
                        sweetalert('Gagal', 'Terjadi kesalah', 'error')
                    }
                })
            }
        });

    });
</script>
@endpush