@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Lampiran Surat Pertanggungjawaban</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <span><a href="{{ route('letter.index') }}"><u>Pengajuan</u></a></span>
            </li>
            <li class="breadcrumb-item active">
                <strong>SPJ</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Data Surat Pertanggungjawaban</h5>
                </div>
                <div class="ibox-content">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('spj.store') }}" method="POST" id="formSpj" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <input type="hidden" name="letter_id" id="letter_id" value="{{ $letter->id }}" class="form-control" required>

                        <div class="form-group">
                            <label>Judul</label>
                            <textarea class="form-control" name="jenis" required autofocus placeholder="Masukan judul...">{{ old('jenis') }}</textarea>
                            <small class="text-danger" id="jenis_error">@if($errors->has('jenis')) {{ $errors->first('jenis') }} @endif</small>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <h4>Lampiran</h4>

                        <table class="table table-bordered" width="100%" id="spj-documents">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1%">No</th>
                                    <th width="32%">Label</th>
                                    <th width="32%">File <small>(Maksimal: 5MB)</small></th>
                                    <th width="32%">Tautan</th>
                                    <th class="text-right" width="1%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" id="iteration">1</td>
                                    <td>
                                        <select name="categories[]" id="categories" class="form-control select2" required>
                                            <option selected value="">Pilih Lampiran..</option>
                                            @foreach(App\Models\SPJCategory::pluck('nama', 'id') as $id => $nama)
                                            <option value=" {{ $id }}">{{ $nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="file" name="files[]" id="files" class="form-control" accept=".pdf, .docx, .png, .jpg, .jpeg">
                                    </td>
                                    <td>
                                        <input type="text" name="links[]" id="links" class="form-control"></input>
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5"><button class="btn btn-default" id="add-document" type="button"><i class="fa fa-plus"></i> Tambah Lampiran</button></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group row">
                            <div class="col-sm-12 col-sm-offset-2">
                                <div class="btn-group pull-right">
                                    <a href="{{ route('letter.index') }}" class="btn btn-default float-right"><i class="fa fa-arrow-left"></i> Kembali</a>
                                    <button type="submit" class="btn btn-success ladda-button ladda-button-demo" data-style="zoom-in" id="submit"><i class="fa fa-save"></i> Simpan</button>
                                </div>
                            </div>
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

        $("#formSpj").on('submit', function(e) {
            e.preventDefault()
            let isValid = true
            $('select[id^="categories"]').each(function(i, e) {
                let file = $(this).closest('tr').find('#files').val();
                let link = $(this).closest('tr').find('#links').val();

                if (!link && !file) {
                    sweetalert('Data tidak valid', `Mohon isi file atau tautan pada lampiran ke-${i+1}.`, 'info');
                    isValid = false
                    return
                }
            });
            if (isValid) {
                swal({
                    title: `Submit?`,
                    text: 'Click "Ya" untuk melanjutkan',
                    showCancelButton: true,
                    confirmButtonColor: "#007bff",
                    confirmButtonText: `Ya, Submit`,
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        LaddaStart();
                        let form = $("#formSpj");
                        $.ajax({
                            url: $(form).attr('action'),
                            type: $(form).attr('method'),
                            data: new FormData(form[0]),
                            contentType: false,
                            processData: false,
                            dataType: 'JSON',
                            success: function(response) {
                                LaddaAndDrawTable();
                                sweetalert('Berhasil', response.msg, null, 500, false);
                                $(form)[0].reset();
                                setTimeout(function() {
                                    window.location.href = "{{ route('spj.index') }}";
                                }, 500);
                            },
                            error: function(xhr, status, err) {
                                let errorMessage = 'Terjadi kesalahan';
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    errorMessage = xhr.responseJSON.error;
                                }

                                LaddaAndDrawTable();
                                sweetalert('Data tidak valid', errorMessage, 'info');
                            }
                        });
                    }
                    // If canceled, nothing else happens and modal already closed
                });
            }

        });

        $('.select2').select2({
            placeholder: "Pilih Lampiran..",
            allowClear: true
        });

        $(document).on('click', '#add-document', function(e) {
            let row = `
                <tr>
                    <td class="text-center" id="iteration">1</td>
                    <td>
                        <select name="categories[]" id="categories" class="form-control select2" required>
                            <option selected value="">Pilih Lampiran..</option>
                            @foreach(App\Models\SPJCategory::pluck('nama', 'id') as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="categories[]_error"></small>
                    </td>
                    <td>
                        <input type="file" name="files[]" id="files" class="form-control" accept=".pdf, .docx, .png, .jpg, .jpeg">
                    </td>
                    <td>
                        <input type="text" name="links[]" id="links" class="form-control"></input>
                    </td>
                    <td><button class="btn btn-sm btn-outline-danger" id="remove-document" type="button"><i class="fa fa-trash"></i></button></td>
                </tr>
            `
            $('#spj-documents').find('tbody').append(row)
            iteration()

            // Reapply select2 style after adding new row
            $('.select2').select2({
                placeholder: "Pilih Lampiran..",
                allowClear: true
            });
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
    })
</script>
@endpush