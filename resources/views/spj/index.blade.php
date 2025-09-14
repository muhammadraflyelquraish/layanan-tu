@extends('layouts.master')

@push('css')
<style>
    .rating {
        display: inline-flex;
        font-size: 3rem;
        cursor: pointer;
    }

    .star {
        color: #ddd;
        transition: color 0.3s;
    }

    .star.hovered,
    .star.selected {
        color: #f5b301;
    }
</style>
@endpush

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Surat Pertanggungjawaban</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <strong>SPJ</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    @if(auth()->user()->role_id != 2 && auth()->user()->role_id != 6 && auth()->user()->role_id != 7 && auth()->user()->role_id != 8)
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h4>Filter</h4>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Search</label>
                                <input name="search" id="search" class="form-control" placeholder="Cari Surat Pertanggungjawaban..">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Pemohon</label>
                                <select name="pemohon_id" id="pemohon_id" class="form-control select2-pemohon">
                                    <option value=""></option>
                                    @foreach($pemohon as $pm)
                                    <option value="{{ $pm->id }}">
                                        {{ $pm->name }} <br> <small>({{$pm->email}})</small>
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="status" class="form-control select2-status">
                                    <option value=""></option>
                                    <option value="Diproses">Diproses</option>
                                    <option value="Revisi">Revisi</option>
                                    <option value="Disetujui">Disetujui</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Prodi</label>
                                <select name="prodi_id" id="prodi_id" class="form-control select2-prodi">
                                    <option value=""></option>
                                    @foreach($prodi as $pr)
                                    <option value="{{ $pr->id }}">{{ $pr->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <!-- <button class="btn btn-success" style="margin-top: 26px;" id="applyFilter" type="button"><i class="fa fa-filter"></i> Filter</button> -->
                            <button class="btn btn-success" id="applyFilter" type="button"><i class="fa fa-filter"></i> Filter</button>
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
                    <h4>Daftar Surat Pertanggungjawaban</h4>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables" width="100%">
                            <thead>
                                @if(auth()->user()->role_id != 2)
                                <tr>
                                    <th class="text-center" width="1px">No</th>
                                    <th>Pengajuan</th>
                                    <th>Pemohon</th>
                                    <th>Judul</th>
                                    <th>Tanggal Proses</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Catatan</th>
                                    <th>Status</th>
                                    <!-- <th>Rating</th> -->
                                    <th class="text-right" width="1px">Aksi</th>
                                </tr>
                                @else
                                <tr>
                                    <th class="text-center" width="1px">No</th>
                                    <th>Pengajuan</th>
                                    <th>Judul</th>
                                    <th>Tanggal Proses</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Catatan</th>
                                    <th>Status</th>
                                    <!-- <th>Rating</th> -->
                                    <th class="text-right" width="1px">Aksi</th>
                                </tr>
                                @endif
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRating" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('spj.rating') }}" method="POST" id="formRating">
                <div class="modal-body">
                    @csrf
                    @method('POST')

                    <h4 class="text-center">Bagaimana pengalaman kamu menggunakan layanan ini?</h4>
                    <input type="hidden" name="spj_id" id="spj_id">
                    <div class="modal-body text-center">
                        <div id="starRating" class="rating" data-selected="0">
                            <span data-value="1" class="star">&#9733;</span>
                            <span data-value="2" class="star">&#9733;</span>
                            <span data-value="3" class="star">&#9733;</span>
                            <span data-value="4" class="star">&#9733;</span>
                            <span data-value="5" class="star">&#9733;</span>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="0" required>
                    </div>

                    <div class="form-group">
                        <textarea class="form-control" rows="3" cols="1" id="catatan" name="catatan" placeholder="Tulis pengalaman kamu disini yaa.."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-lg btn-success btn-block ladda-button ladda-button-demo" data-style="zoom-in" id="submitRating" tabindex="8">Rating</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        $('.select2-pemohon').select2({
            placeholder: "Filter Pemohon..",
            allowClear: true,
            width: '100%'
        });

        $('.select2-status').select2({
            placeholder: "Filter Status..",
            allowClear: true,
            width: '100%'
        });

        $('.select2-prodi').select2({
            placeholder: "Filter Prodi..",
            allowClear: true,
            width: '100%'
        });

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

        let columns = [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                searchable: false,
                orderable: false,
                className: 'text-center'
            },
            {
                data: 'letter',
                name: 'letter'
            },
            {
                data: 'user.name',
                name: 'user.name'
            },
            {
                data: 'jenis',
                name: 'jenis'
            },
            {
                data: 'tanggal_proses',
                name: 'tanggal_proses'
            },
            {
                data: 'tanggal_selesai',
                name: 'tanggal_selesai'
            },
            {
                data: 'catatan',
                name: 'catatan'
            },
            {
                data: 'status',
                name: 'status'
            },
            // {
            //     data: 'rating',
            //     name: 'rating'
            // },
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
                    data: 'letter',
                    name: 'letter'
                },
                {
                    data: 'jenis',
                    name: 'jenis'
                },
                {
                    data: 'tanggal_proses',
                    name: 'tanggal_proses'
                },
                {
                    data: 'tanggal_selesai',
                    name: 'tanggal_selesai'
                },
                {
                    data: 'catatan',
                    name: 'catatan'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                // {
                //     data: 'rating',
                //     name: 'rating'
                // },
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
                url: "{{ route('spj.data') }}",
                type: "GET",
                data: function(d) {
                    d.search = $('input[name="search"]').val()
                    d.pemohon_id = $('select[name="pemohon_id"]').val()
                    d.status = $('select[name="status"]').val()
                    d.prodi_id = $('select[name="prodi_id"]').val()
                }
            },
            columns: columns,
            search: {
                regex: true
            }
        });

        $('#applyFilter').on('click', function() {
            serverSideTable.ajax.reload();
        });

        $("#starRating .star").each(function() {
            $(this).on("mouseover", function() {
                const value = $(this).attr("data-value");
                $("#starRating .star").each(function() {
                    $(this).toggleClass("hovered", $(this).attr("data-value") <= value);
                });
            });

            $(this).on("mouseout", function() {
                $("#starRating .star").each(function() {
                    $(this).removeClass("hovered");
                });
            });

            $(this).on("click", function() {
                const value = $(this).attr("data-value");
                $("#ratingInput").val(value);
                $("#starRating .star").each(function() {
                    $(this).toggleClass("selected", $(this).attr("data-value") <= value);
                });
            });
        });

        $('#modalRating').on('shown.bs.modal', function(e) {
            let button = $(e.relatedTarget)
            let modal = $(this)
            let id = button.data('id')
            modal.find('#spj_id').val(id);

            $.get("{{ url('/spj/') }}/" + id + '/rating', function(app) {
                modal.find('#catatan').text(app.data.catatan)
                modal.find('#ratingInput').val(app.data.rating)
                $("#starRating .star").each(function() {
                    $(this).toggleClass("hovered", $(this).attr("data-value") <= app.data.rating);
                });
            })
        })

        $(document).on('click', '#submitRating', function(e) {
            let button = $(this)
            swal({
                title: `Simpan Rating?`,
                text: 'Click "Ya" untuk melanjutkan',
                showCancelButton: true,
                confirmButtonColor: "#007bff",
                confirmButtonText: `Ya, Simpan`,
                closeOnConfirm: false
            }, function() {
                LaddaStart()
                swal.close();
                button.closest('form#formRating').submit();
                LaddaAndDrawTable()
            });
        })

    });
</script>
@endpush