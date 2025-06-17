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
                <strong>Daftar Surat Pertanggungjawaban</strong>
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
                                <input name="search" id="search" class="form-control" placeholder="Pengajuan/Pemohon/Surat...">
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
                                    <option value="Revisi">Revisi</option>
                                    <option value="Disetujui">Disetujui</option>
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
                    <h4>Daftar SPJ</h4>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables" width="100%">
                            <thead>
                                @if(auth()->user()->role_id != 2)
                                <tr>
                                    <th class="text-center" width="1px">No</th>
                                    <th>Proposal</th>
                                    <th>Pemohon</th>
                                    <th>Kategori</th>
                                    <th>Tanggal Proses</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Catatan</th>
                                    <th>Status</th>
                                    <th>Rating</th>
                                    <th class="text-right" width="1px">Aksi</th>
                                </tr>
                                @else
                                <tr>
                                    <th class="text-center" width="1px">No</th>
                                    <th>Proposal</th>
                                    <th>Kategori</th>
                                    <th>Tanggal Proses</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Catatan</th>
                                    <th>Status</th>
                                    <th>Rating</th>
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
            <form action="{{ route('spj.rating') }}" method="POST">
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
                    <button type="submit" class="btn btn-lg btn-primary btn-block" data-style="zoom-in" id="submit" tabindex="8">Rating</button>
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
                disertai_dana: "Jenis surat tidak boleh kosong"
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
            {
                data: 'rating',
                name: 'rating'
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
                {
                    data: 'rating',
                    name: 'rating'
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
                url: "{{ route('spj.data') }}",
                type: "GET",
                data: function(d) {
                    d.search = $('input[name="search"]').val()
                    d.pemohon_id = $('select[name="pemohon_id"]').val()
                    d.status = $('select[name="status"]').val()
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

        $('#ModalAddEdit').on('shown.bs.modal', function(e) {
            $('#aplicant_name').focus();
            let button = $(e.relatedTarget)
            let modal = $(this)
            if (button.data('mode') == 'edit') {
                let id = button.data('integrity')
                let closeTr = button.closest('tr')
                $('#formAddEdit').attr('action', '{{ route("letter.store") }}/' + id).attr('method', 'PATCH')

                modal.find('#modal-title').text('Edit Proposal');
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
                modal.find('#modal-title').text('Buat Proposal');
            }
        })

        $('#ModalDetail').on('shown.bs.modal', function(e) {
            let button = $(e.relatedTarget)
            let modal = $(this)
            let id = button.data('integrity')

            $.get('{{ route("letter.store") }}/' + id, function(app) {
                const dateOfLetterObj = new Date(app.tanggal_surat);
                const dateOfLetter = `
                    ${dateOfLetterObj.getDate().toString().padStart(2, '0')}
                    ${dateOfLetterObj.toLocaleString('id-ID', {month: 'long'})}
                    ${dateOfLetterObj.getFullYear()}
                `;

                const receivedDateObj = new Date(app.tanggal_diterima);
                const receivedDate = `
                    ${receivedDateObj.getDate().toString().padStart(2, '0')}
                    ${receivedDateObj.toLocaleString('id-ID', {month: 'long'})}
                    ${receivedDateObj.getFullYear()}
                `;

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
                modal.find('#detailInformation').find('#status').html(`<span class="badge badge-sm ${app.status === 'Selesai' ? 'badge-primary' : app.status === 'Ditolak' ? 'badge-danger' : 'badge-warning'}">${app.status}</span>`);

                // disposition detail
                modal.find('#detailDisposition').find('tbody').children().remove()

                for (let index = 0; index < app.dispositions.length; index++) {
                    const element = app.dispositions[index];

                    const recievedDateObj = new Date(element.tanggal_diterima);
                    const recievedDate = `
                        ${recievedDateObj.getDate().toString().padStart(2, '0')}
                        ${recievedDateObj.toLocaleString('id-ID', {month: 'long'})}
                        ${recievedDateObj.getFullYear()}
                    `;

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
                            <td>${recievedDate}</td>
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
            modal.find('#spj_id').val(button.data('id'));
        })

    });
</script>
@endpush