@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Detail Surat Pertanggungjawaban</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <span><a href="{{ route('spj.index') }}"><u>SPJ</u></a></span>
            </li>
            <li class="breadcrumb-item active">
                <strong>Detail</strong>
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
                            <th>Tanggal Selesai</th>
                            <td class="text-center">:</td>
                            <td colspan="4">{{ $spj->tanggal_selesai ? date('d M Y - H:i', strtotime($spj->tanggal_selesai)) : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Selesai Dalam</th>
                            <td class="text-center">:</td>
                            <td colspan="4">
                                @if ($spj->tanggal_proses && $spj->tanggal_selesai)
                                {{ \Carbon\Carbon::parse($spj->tanggal_proses)->diff(\Carbon\Carbon::parse($spj->tanggal_selesai))->format('%d hari, %h jam, %i menit') }}
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Catatan</th>
                            <td class="text-center">:</td>
                            <td colspan="4">{{ $spj->catatan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Rating</th>
                            <td class="text-center">:</td>
                            <td colspan="4">
                                @if (isset($spj->ratings[0]))
                                @for ($i = 1; $i <= 5; $i++)
                                    <span data-value="{{ $i }}" class="star" style="{{ $i <= $spj->ratings[0]->rating ? 'color: #f5b301' : '' }}">&#9733;</span>
                                    @endfor
                                    <br> <small>Catatan: {{ $spj->ratings[0]->catatan ?? '-' }}</small>
                                    @else
                                    -
                                    @endif
                            </td>
                        </tr>
                    </table>

                    <form action="#" method="post" id="formRole" enctype="multipart/form-data">

                        <!-- <div class="form-group">
                            <label>Judul</label>
                            <textarea class="form-control" name="jenis" readonly>{{ $spj->jenis }}</textarea>
                        </div> -->

                        <div class="hr-line-dashed"></div>

                        <h5>Lampiran</h5>

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

                        <!-- <div class="hr-line-dashed"></div> -->
                        <!-- <div class="form-group">
                            <label>Catatan</label>
                            <textarea class="form-control" cols="1" rows="2" readonly>{{ $spj->catatan }}</textarea>
                            <small class="text-danger" id="catatan_error"></small>
                        </div> -->

                        <div class="hr-line-dashed"></div>

                        <div class="form-group row">
                            <div class="col-sm-12 col-sm-offset-2">
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
                    <button type="submit" class="btn btn-success ladda-button ladda-button-demo" data-style="zoom-in" id="submit" tabindex="8"><i class="fa fa-check-square mr-1"></i>Simpan [Enter]</button>
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