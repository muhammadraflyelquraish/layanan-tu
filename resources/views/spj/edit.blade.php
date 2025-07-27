@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Edit Surat Pertanggungjawaban</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <span><a href="{{ route('spj.index') }}"><u>SPJ</u></a></span>
            </li>
            <li class="breadcrumb-item active">
                <strong>Edit</strong>
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

                    <form action="{{ route('spj.revisi', $spj->id) }}" method="post" id="formSpj" enctype="multipart/form-data">
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

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>Judul</label>
                            <textarea class="form-control" name="jenis" required autofocus placeholder="Masukan judul...">{{ $spj->jenis }}</textarea>
                            <small class="text-danger" id="jenis_error">@if($errors->has('jenis')) {{ $errors->first('jenis') }} @endif</small>
                        </div>

                        <h5>Lampiran</h5>

                        <table class="table table-bordered" width="100%" id="spj-documents">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1%">No</th>
                                    <th width="32%">Label</th>
                                    <th width="32%">File <small>(Maksimal: 5MB)</small></th>
                                    <th width="32%">Tautan</th>
                                    <th class="text-right" width="1px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($spj->documents as $document)
                                <tr>
                                    <td class="text-center" id="iteration">{{ $loop->iteration }}</td>
                                    <td>
                                        <select name="categories[]" id="categories" class="form-control select2" required>
                                            <option selected value="">Pilih Lampiran..</option>
                                            @foreach(App\Models\SPJCategory::pluck('nama', 'id') as $id => $nama)
                                            <option value="{{ $id }}" @if($document->spj_category_id == $id) selected @endif >{{ $nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" name="document_ids[]" value="{{ $document->id }}">
                                        <input type="file" name="files[]" id="files" class="form-control" accept=".pdf, .docx, .png, .jpg, .jpeg">
                                        <input type="hidden" id="file_id" value="{{ $document->file_id }}">
                                        <!-- {!! $document->file ? '<a href="' . ($document->file ? $document->file->file_url : '#') . '" target="_blank"><i class="fa fa-file-pdf-o"></i> Dokumen ' . $document->category->nama . '</a>' : '-' !!} -->
                                        {!! $document->file ? '<a href="' . ($document->file ? $document->file->file_url : '#') . '" target="_blank"><i class="fa fa-file-pdf-o"></i> Dokumen</a>' : '-' !!}
                                    </td>
                                    <td>
                                        <input type="text" name="links[]" id="links" value="{{ $document->link }}" class="form-control">
                                        <!-- {!! $document->link ? '<a href="' . $document->link . '" target="_blank"><i class="fa fa-link"></i> Tautan ' . $document->category->nama . '</a>' : '-' !!} -->
                                        {!! $document->link ? '<a href="' . $document->link . '" target="_blank"><i class="fa fa-link"></i> Tautan</a>' : '-' !!}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-danger" id="remove-document" type="button"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5"><button class="btn btn-default" id="add-document" type="button"><i class="fa fa-plus"></i> Tambah Dokumen</button></td>
                                </tr>
                            </tfoot>
                        </table>

                        </table>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group row">
                            <div class="col-sm-12 col-sm-offset-2">
                                <button class="btn btn-success float-right ladda-button ladda-button-demo" type="submit"><i class="fa fa-save"></i> Revisi</button>
                                <a href="{{ route('spj.index') }}" class="btn btn-default float-right"><i class="fa fa-arrow-left"></i> Kembali</a>
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

        $("#formSpj").on('submit', function(e) {
            e.preventDefault()
            let isValid = true

            if ($('select[id^="categories"]').length < 1) {
                sweetalert('Data tidak valid', `Mohon isi lampiran.`, 'info');
                isValid = false
                return
            }

            $('select[id^="categories"]').each(function(i, e) {
                let file = $(this).closest('tr').find('#files').val();
                let fileId = $(this).closest('tr').find('#file_id').val();
                let link = $(this).closest('tr').find('#links').val();

                if (!link && !(file || fileId)) {
                    sweetalert('Data tidak valid', `Mohon isi file atau tautan pada lampiran ke-${i+1}.`, 'info');
                    isValid = false
                    return
                }
            });

            if (isValid) {
                swal({
                    title: `Simpan Perubahan?`,
                    text: 'Click "Ya" untuk melanjutkan',
                    showCancelButton: true,
                    confirmButtonColor: "#007bff",
                    confirmButtonText: `Ya, Simpan`,
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function() {
                    LaddaStart()
                    let form = $("#formSpj")
                    $.ajax({
                        url: $(form).attr('action'),
                        type: "POST",
                        data: new FormData(form[0]),
                        contentType: false,
                        processData: false,
                        dataType: 'JSON',
                        success: function(response) {
                            LaddaAndDrawTable()
                            sweetalert('Berhasil', response.msg, null, 500, false)
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
                    })
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