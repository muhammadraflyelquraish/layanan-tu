@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Lampiran Surat Pertanggungjawaban</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <span><a href="{{ route('spj.index') }}"><u>Proposal</u></a></span>
            </li>
            <li class="breadcrumb-item active">
                <strong>SPJ</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-7">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Data SPJ</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('spj.update', $spj->id) }}" method="POST" id="formRole">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="form-group">
                            <label>Kategori SPJ</label>
                            <textarea class="form-control" name="jenis" readonly>{{ $spj->jenis }}</textarea>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <h4>Lampiran Dokumen</h4>

                        <table class="table table-bordered" width="100%" id="spj-documents">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1px">No</th>
                                    <th>Jenis Dokumen</th>
                                    <th>Dokumen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($spj->documents as $document)
                                <tr>
                                    <td class="text-center" id="iteration">{{ $loop->iteration }}</td>
                                    <td>{{$document->category->nama}}</td>
                                    <td><a href="{{ $document->file->file_url }}" target="_blank">Dokumen {{$document->category->nama}}</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea class="form-control" cols="1" rows="3" name="catatan" id="catatan">{{ $spj->catatan }}</textarea>
                            <small class="text-danger" id="catatan_error"></small>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <input type="hidden" name="type" id="type">

                        <div class="form-group row">
                            <div class="col-sm-12 col-sm-offset-2">
                                <button class="btn btn-primary float-right" type="button" id="btn-terima"><i class="fa fa-check"></i> Terima</button>
                                <button class="btn btn-warning float-right" type="button" id="btn-revisi"><i class="fa fa-times"></i> Revisi</button>
                                <a href="{{ route('spj.index') }}" class="btn btn-default float-right"><i class="fa fa-arrow-left"></i> Kembali</a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
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

        $(document).on('click', '#btn-revisi, #btn-terima', function(e) {
            e.preventDefault();

            let catatan = $('#catatan');
            let catatanError = $('#catatan_error');
            let type = $('#type');
            let form = $('#formRole');

            if ($(this).attr('id') === 'btn-revisi') {
                type.val('revisi')
                if (!catatan.val().trim()) {
                    catatanError.text("Catatan tidak boleh kosong")
                    catatan.focus();
                    return;
                }
            } else {
                type.val('setuju')
            }

            form.submit()
        });

    })
</script>
@endpush