@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Approval Surat Pertanggungjawaban</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <span><a href="{{ route('spj.index') }}"><u>SPJ</u></a></span>
            </li>
            <li class="breadcrumb-item active">
                <strong>Approval</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-9">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Data Surat Pertanggungjawaban</h5>
                </div>
                <div class="ibox-content">

                    <h5>Informasi</h5>
                    <table class="table" id="detailInformation">
                        <tr>
                            <th>Judul</th>
                            <td class="text-center">:</td>
                            <td colspan="4">{{ $spj->jenis }}</td>
                        </tr>
                        <tr>
                            <th>Pemohon</th>
                            <td class="text-center">:</td>
                            <td>
                                {{ $spj->user->name }} <br>
                                <small>{{ $spj->user->email }}</small>
                            </td>
                            <th class="text-right">Pengajuan</th>
                            <td class="text-center">:</td>
                            <td>
                                {{ $spj->letter->kode }} <br>
                                <small>Nomor Agenda: {{ $spj->letter->nomor_agenda }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Diproses</th>
                            <td class="text-center">:</td>
                            <td>{{ $spj->tanggal_proses ? date('d M Y - H:i', strtotime($spj->tanggal_proses)) : '-' }}</td>
                            <th class="text-right">Status</th>
                            <td class="text-center">:</td>
                            <td>
                                @if ($spj->status == 'Disetujui')
                                <span class="label label-success">{{ $spj->status }}</span>
                                @elseif ($spj->status == 'Ditolak')
                                <span class="label label-danger">{{ $spj->status }}</span>
                                @else
                                <span class="label label-warning">{{ $spj->status }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Catatan</th>
                            <td class="text-center">:</td>
                            <td colspan="4">{{ $spj->catatan ?? '-' }}</td>
                        </tr>
                    </table>

                    <form action="{{ route('spj.update', $spj->id) }}" method="POST" id="formRole">
                        @csrf
                        @method('PUT')

                        <div class="hr-line-dashed"></div>

                        <h5>Lampiran Dokumen</h5>

                        <table class="table table-bordered" width="100%" id="spj-documents">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1%">No</th>
                                    <th width="32%">Label</th>
                                    <th width="32%">File <small>(Maksimal: 5MB)</small></th>
                                    <th width="32%">Tautan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($spj->documents as $document)
                                <tr>
                                    <td class="text-center" id="iteration">{{ $loop->iteration }}</td>
                                    <td>{{$document->category->nama}}</td>
                                    <!-- <td>{!! $document->file ? '<a href="' . ($document->file ? $document->file->file_url : '#') . '" target="_blank"><i class="fa fa-file-pdf-o"></i> Dokumen ' . $document->category->nama . '</a>' : '-' !!}</td> -->
                                    <!-- <td>{!! $document->link ? '<a href="' . $document->link . '" target="_blank"><i class="fa fa-link"></i> Tautan ' . $document->category->nama . '</a>' : '-' !!}</td> -->
                                    <td>{!! $document->file ? '<a href="' . ($document->file ? $document->file->file_url : '#') . '" target="_blank"><i class="fa fa-file-pdf-o"></i> Dokumen</a>' : '-' !!}</td>
                                    <td>{!! $document->link ? '<a href="' . $document->link . '" target="_blank"><i class="fa fa-link"></i> Tautan</a>' : '-' !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea class="form-control" cols="1" rows="2" name="catatan" id="catatan"></textarea>
                            <small class="text-danger" id="catatan_error"></small>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <input type="hidden" name="type" id="type">

                        <div class="form-group row">
                            <div class="col-sm-12 col-sm-offset-2">
                                <div class="btn-group pull-right">
                                    <a href="{{ route('spj.index') }}" class="btn btn-default float-right"><i class="fa fa-arrow-left"></i> Kembali</a>
                                    <button class="btn btn-warning float-right ladda-button ladda-button-demo" type="button" id="btn-revisi"><i class="fa fa-times"></i> Revisi</button>
                                    <button class="btn btn-success float-right ladda-button ladda-button-demo" type="button" id="btn-terima"><i class="fa fa-check"></i> Setujui</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>History Approval</h5>
                </div>
                <div class="ibox-content">

                    @foreach($spj->histories as $i => $history)

                    <div>
                        <small>{{ date('d M Y - H:i', strtotime($history->created_at)) }}</small>

                        <h4>
                            <i class="fa fa-user"></i> {{ $history->user->name }} ({{ $history->user->role->name }})
                            @switch($history->status)
                            @case("Diproses")
                            <span class="badge badge-warning">{{ $history->status }}</span>
                            @break
                            @case("Revisi")
                            <span class="badge badge-warning">{{ $history->status }}</span>
                            @break
                            @default
                            <span class="badge badge-success">{{ $history->status }}</span>
                            @endswitch
                        </h4>

                        <p>Catatan: <i>{{ $history->catatan }}</i></p>

                        <hr>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(function() {
        //BASE
        let ladda = $('.ladda-button-demo').ladda();

        function LaddaStart() {
            ladda.ladda('start');
        }

        function LaddaAndDrawTable() {
            ladda.ladda('stop');
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

        $(document).on('click', '#btn-revisi, #btn-terima', function(e) {
            e.preventDefault();

            let catatan = $('#catatan');
            let catatanError = $('#catatan_error');
            let type = $('#type');
            let form = $('#formRole');
            let message = "";

            if ($(this).attr('id') === 'btn-revisi') {
                type.val('revisi')
                message = "Revisi"
                if (!catatan.val().trim()) {
                    catatanError.text("Catatan tidak boleh kosong")
                    catatan.focus();
                    return;
                }
            } else {
                type.val('setuju')
                message = "Setujui"
            }

            swal({
                title: `${message}?`,
                text: 'Click "Ya" untuk melanjutkan',
                showCancelButton: true,
                confirmButtonColor: "#007bff",
                confirmButtonText: `Ya, ${message}`,
                closeOnConfirm: true,
                closeOnCancel: true
            }, function() {
                LaddaStart()
                swal.close();
                form.submit()
                LaddaAndDrawTable()
            });
        });

    })
</script>
@endpush