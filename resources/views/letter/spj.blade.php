@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Lampiran Surat Pertanggungjawaban</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <span><a href="{{ route('letter.index') }}"><u>Proposal</u></a></span>
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
                    <form action="{{ route('spj.store') }}" method="post" id="formRole" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <input type="hidden" name="letter_id" id="letter_id" value="{{ $letter->id }}" class="form-control" required>

                        <div class="form-group">
                            <label>Kategori SPJ</label>
                            <textarea class="form-control" name="jenis" required autofocus placeholder="Masukan kategori...">{{ old('jenis') }}</textarea>
                            <small class="text-danger" id="jenis_error">@if($errors->has('jenis')) {{ $errors->first('jenis') }} @endif</small>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <h4>Lampiran Dokumen</h4>

                        <div class="form-group">
                            <button class="btn btn-default" type="button" data-toggle="modal" data-target="#ModalSpjCategory"><i class="fa fa-plus"></i> Tambah Jenis Dokumen</button>
                        </div>

                        <table class="table table-bordered" width="100%" id="spj-documents">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1px">No</th>
                                    <th>Jenis Dokumen</th>
                                    <th>File</th>
                                    <th class="text-right" width="1px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" id="iteration">1</td>
                                    <td>
                                        <select name="categories[]" id="categories" class="form-control" required>
                                            <option value="" selected disabled>--Pilih Jenis Dokumen--</option>
                                            @foreach(App\Models\SPJCategory::pluck('nama', 'id') as $id => $nama)
                                            <option value="{{ $id }}">{{ $nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="file" name="files[]" id="files" class="form-control" accept="application/pdf" required>
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4"><button class="btn btn-default" id="add-document" type="button"><i class="fa fa-plus"></i> Tambah Dokumen</button></td>
                                </tr>
                            </tfoot>
                        </table>

                        </table>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group row">
                            <div class="col-sm-12 col-sm-offset-2">
                                <button class="btn btn-primary float-right" type="submit"><i class="fa fa-save"></i> Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalSpjCategory" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('spj.category.store') }}" method="POST" id="formSpjDokumen">
                @csrf
                @method('POST')

                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Tambah Kategori Dokumen SPJ</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <div>
                            <input type="text" class="form-control" name="nama" id="nama" required>
                            <small class="text-danger" id="nama_error"></small>
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
    $(function() {

        $("#formRole").validate({
            rules: {
                jenis: 'required',
            },
            messages: {
                jenis: "Kategori tidak boleh kosong",
            },
            success: function(messages) {
                $(messages).remove();
            },
            errorPlacement: function(error, element) {
                let name = element.attr("name");
                $("#" + name + "_error").text(error.text());
            },
            submitHandler: function(form) {
                form.submit()
            }
        });

        $("#formSpjDokumen").validate({
            rules: {
                nama: 'required',
            },
            messages: {
                nama: "Nama kategori tidak boleh kosong",
            },
            success: function(messages) {
                $(messages).remove();
            },
            errorPlacement: function(error, element) {
                let name = element.attr("name");
                $("#" + name + "_error").text(error.text());
            },
            submitHandler: function(form) {
                form.submit()
            }
        });

        $(document).on('click', '#add-document', function(e) {
            let row = `
                <tr>
                    <td class="text-center" id="iteration">1</td>
                    <td>
                        <select name="categories[]" id="categories" class="form-control" required>
                            <option value="" selected disabled>--Pilih Jenis Dokumen--</option>
                            @foreach(App\Models\SPJCategory::pluck('nama', 'id') as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="file" name="files[]" id="files" accept="application/pdf" class="form-control" required>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-danger" id="remove-document" type="button"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            `
            $('#spj-documents').find('tbody').append(row)
            iteration()
        })

        $(document).on('click', '#remove-document', function(e) {
            const row = $(this).closest('tr').remove()
            iteration()
        })

        function iteration() {
            $('#spj-documents').find('tbody tr').each(function(index) {
                $(this).find('#iteration').text(index + 1);
            });
        }


        $("#formRole").validate({
            rules: {
                kategori: 'required',
            },
            messages: {
                kategori: "Kategori tidak boleh kosong",
            },
            success: function(messages) {
                $(messages).remove();
            },
            errorPlacement: function(error, element) {
                let name = element.attr("name");
                $("#" + name + "_error").text(error.text());
            },
            submitHandler: function(form) {
                form.submit()
            }
        });
    })
</script>
@endpush