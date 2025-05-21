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
        <div class="col-lg-6">
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
                            <input type="text" class="form-control" value="{{ $spj->jenis }}" disabled>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <h4>Lampiran Dokumen</h4>

                        <table class="table table-bordered" width="100%" id="spj-documents">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1px">No</th>
                                    <th>Jenis Dokumen</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($spj->documents as $document)
                                <tr>
                                    <td class="text-center" id="iteration">{{ $loop->iteration }}</td>
                                    <td>{{$document->category->nama}}</td>
                                    <td><a href="#">{{$document->file->name}}</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea class="form-control" name="catatan" id="catatan">{{ $spj->catatan }}</textarea>
                            <small class="text-danger" id="catatan_error"></small>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <input type="hidden" name="type" id="type">

                        <div class="btn-group">
                            <a href="{{ route('spj.index') }}" class="btn btn-default" type="button"><i class="fa fa-arrow-left"></i> Kembali</a>
                            <button class="btn btn-warning" type="button" id="btn-revisi"><i class="fa fa-times"></i> Revisi</button>
                            <button class="btn btn-primary" type="button" id="btn-terima"><i class="fa fa-check"></i> Terima</button>
                        </div>

                    </form>
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