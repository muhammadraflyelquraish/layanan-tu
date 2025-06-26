<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Layanan Tata Usaha - Portal Surat FST</title>

    <link href="{{ asset('build/assets') }}/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('build/assets') }}/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="{{ asset('build/assets') }}/css/plugins/iCheck/custom.css" rel="stylesheet">

    <link href="{{ asset('build/assets') }}/css/dataTables.min.css" rel="stylesheet">
    <link href="{{ asset('build/assets') }}/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="{{ asset('build/assets') }}/css/plugins/ladda/ladda-themeless.min.css" rel="stylesheet">

    <link href="{{ asset('build/assets') }}/css/animate.css" rel="stylesheet">
    <link href="{{ asset('build/assets') }}/css/style.css" rel="stylesheet">

    <link href="{{ asset('build/assets') }}/css/plugins/select2/select2.min.css" rel="stylesheet">
    <link href="{{ asset('build/assets') }}/css/plugins/chosen/bootstrap-chosen.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        * {
            font-family: 'Rubik', sans-serif;
        }
    </style>

</head>

<body class="gray-bg">
    <div id="wrapper">
        <div class="wrapper wrapper-content">
            <div class="row">
                <div class="col-lg-10" style="margin: auto">
                    <div class="form-group">
                        <a href="{{ route('letter.index') }}" class="btn btn-success"><i class="fa fa-arrow-circle-right"></i> Masuk Aplikasi</a>
                    </div>
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Tracking Surat</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="table-responsive">
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
                                            <th>SK</th>
                                            <th class="text-right" width="1px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
                                    <th class="text-right">Tanggal Diterima</th>
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
                                    <th>Perlu SK</th>
                                    <td class="text-center">:</td>
                                    <td id="perlu_sk"></td>
                                    <th class="text-right">Pihak Pembuat SK</th>
                                    <td class="text-center">:</td>
                                    <td id="pihak_pembuat_sk_id"></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Selesai</th>
                                    <td class="text-center">:</td>
                                    <td id="tanggal_selesai"></td>
                                    <th class="text-right">Selesai Dalam</th>
                                    <td class="text-center">:</td>
                                    <td id="selesai_dalam_waktu"></td>
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

    </div>

    <!-- Mainly scripts -->
    <script src="{{ asset('build/assets') }}/js/jquery-3.1.1.min.js"></script>
    <script src="{{ asset('build/assets') }}/js/popper.min.js"></script>
    <script src="{{ asset('build/assets') }}/js/bootstrap.js"></script>
    <script src="{{ asset('build/assets') }}/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="{{ asset('build/assets') }}/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <script src="{{ asset('build/assets') }}/js/dataTables.min.js"></script>
    <script src="{{ asset('build/assets') }}/js/jquery.form-validator.min.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="{{ asset('build/assets') }}/js/inspinia.js"></script>
    <script src="{{ asset('build/assets') }}/js/plugins/pace/pace.min.js"></script>

    <!-- Sweet alert -->
    <script src="{{ asset('build/assets') }}/js/plugins/sweetalert/sweetalert.min.js"></script>

    <!-- Ladda -->
    <script src="{{ asset('build/assets') }}/js/plugins/ladda/spin.min.js"></script>
    <script src="{{ asset('build/assets') }}/js/plugins/ladda/ladda.min.js"></script>
    <script src="{{ asset('build/assets') }}/js/plugins/ladda/ladda.jquery.min.js"></script>

    <script src="{{ asset('build/assets') }}/js/plugins/iCheck/icheck.min.js"></script>

    <script src="{{ asset('build/assets') }}/js/plugins/chosen/chosen.jquery.js"></script>

    <script src="{{ asset('build/assets') }}/js/plugins/select2/select2.full.min.js"></script>
    <script>
        $(document).ready(function() {

            let serverSideTable = $('.dataTables').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('tracking.data') }}",
                    type: "GET",
                },
                columns: [{
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
                        data: 'sk.original_name',
                        name: 'sk.original_name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    },
                ],
                search: {
                    regex: true
                }
            });

            $('#ModalDetail').on('shown.bs.modal', function(e) {
                let button = $(e.relatedTarget)
                let modal = $(this)
                let id = button.data('integrity')

                $.get('{{ route("letter.store") }}/' + id, function(app) {
                    let dateOfLetter = '-'
                    if (app.letter.tanggal_surat) {
                        const dateOfLetterObj = new Date(app.letter.tanggal_surat);
                        dateOfLetter = `
                    ${dateOfLetterObj.getDate().toString().padStart(2, '0')}
                    ${dateOfLetterObj.toLocaleString('id-ID', {month: 'long'})}
                    ${dateOfLetterObj.getFullYear()}
                `;
                    }

                    let receivedDate = '-'
                    if (app.letter.tanggal_diterima) {
                        const receivedDateObj = new Date(app.letter.tanggal_diterima);
                        receivedDate = `
                        ${receivedDateObj.getDate().toString().padStart(2, '0')}
                        ${receivedDateObj.toLocaleString('id-ID', {month: 'long'})}
                        ${receivedDateObj.getFullYear()}
                        ${receivedDateObj.getHours().toString().padStart(2, '0')}:${receivedDateObj.getMinutes().toString().padStart(2, '0')}
                    `;
                    }

                    let tanggalSelesai = '-'
                    if (app.letter.tanggal_selesai) {
                        const tanggalSelesaiObj = new Date(app.letter.tanggal_selesai);
                        tanggalSelesai = `
                        ${tanggalSelesaiObj.getDate().toString().padStart(2, '0')}
                        ${tanggalSelesaiObj.toLocaleString('id-ID', {month: 'long'})}
                        ${tanggalSelesaiObj.getFullYear()}
                        ${tanggalSelesaiObj.getHours().toString().padStart(2, '0')}:${tanggalSelesaiObj.getMinutes().toString().padStart(2, '0')}
                    `;
                    }

                    let jenisSurat = 'Surat Masuk'
                    if (app.letter.disertai_dana == true) {
                        jenisSurat = 'Surat Pembayaran'
                    }

                    modal.find('#detailInformation').find('#kode').html(`${app.letter.kode} <br> <small>Nomor Agenda: ${app.letter.nomor_agenda}</small>`);
                    modal.find('#detailInformation').find('#nomor_agenda').text(app.letter.nomor_agenda);
                    modal.find('#detailInformation').find('#pemohon_id').html(`${app.letter.pemohon.name} <br> <small>${app.letter.pemohon.email}</small>`);
                    modal.find('#detailInformation').find('#tanggal_surat').text(dateOfLetter);
                    modal.find('#detailInformation').find('#nomor_surat').text(app.letter.nomor_surat);
                    modal.find('#detailInformation').find('#asal_surat').text(app.letter.asal_surat);
                    modal.find('#detailInformation').find('#hal').text(app.letter.hal);
                    modal.find('#detailInformation').find('#tanggal_diterima').text(receivedDate);
                    modal.find('#detailInformation').find('#untuk').text(app.letter.untuk);
                    modal.find('#detailInformation').find('#disertai_dana').text(jenisSurat);
                    modal.find('#detailInformation').find('#alasan_penolakan').text(app.letter.alasan_penolakan ?? '-');
                    modal.find('#detailInformation').find('#proposal_file').html(`<a href="${app.letter.file.file_url}" target="_blank"><i class="fa fa-file-pdf-o"></i> Dok Proposal</a>`);
                    modal.find('#detailInformation').find('#sk').html(app.letter?.sk ? `<a href="${app.letter?.sk.file_url}" target="_blank"><i class="fa fa-file-pdf-o"></i> Dok SK</a>` : '-');
                    modal.find('#detailInformation').find('#status').html(`<span class="badge badge-sm ${app.letter.status === 'Selesai' ? 'badge-primary' : app.letter.status === 'Ditolak' ? 'badge-danger' : 'badge-warning'}">${app.letter.status}</span>`);
                    // modal.find('#detailInformation').find('#perlu_sk').text(app.letter.perlu_sk ? "Ya" : "Tidak")
                    modal.find('#detailInformation').find('#pihak_pembuat_sk_id').text(app?.letter?.pihak_pembuat_sk?.name ?? '-')
                    modal.find('#detailInformation').find('#tanggal_selesai').text(tanggalSelesai);
                    modal.find('#detailInformation').find('#selesai_dalam_waktu').text(app?.selesai_dalam);

                    // disposition detail
                    modal.find('#detailDisposition').find('tbody').children().remove()

                    for (let index = 0; index < app.letter.dispositions.length; index++) {
                        const element = app.letter.dispositions[index];

                        let recievedDate = ''
                        if (element.tanggal_diterima) {
                            const recievedDateObj = new Date(element.tanggal_diterima);
                            recievedDate = `
                        ${recievedDateObj.getDate().toString().padStart(2, '0')}
                        ${recievedDateObj.toLocaleString('id-ID', {month: 'long'})}
                        ${recievedDateObj.getFullYear()}
                        ${recievedDateObj.getHours().toString().padStart(2, '0')}:${recievedDateObj.getMinutes().toString().padStart(2, '0')}
                    `;
                        }

                        let approvedDate = ''
                        if (element.tanggal_diproses) {
                            const approvedDateObj = new Date(element.tanggal_diproses);
                            approvedDate = `
                        ${approvedDateObj.getDate().toString().padStart(2, '0')}
                        ${approvedDateObj.toLocaleString('id-ID', {month: 'long'})}
                        ${approvedDateObj.getFullYear()}
                        ${approvedDateObj.getHours().toString().padStart(2, '0')}:${approvedDateObj.getMinutes().toString().padStart(2, '0')}
                    `;
                        }

                        let disposisi = element?.disposition?.name || 'TU'
                        let status = element.status ? `<span class="badge badge-sm badge-${element.status === 'Diproses' || element.status === 'Menunggu Konfirmasi TU' ? 'warning' : element.status === 'Disposisi' ? 'primary' : element.status === 'Ditolak' ? 'danger' : 'info'}">${element.status}</span>` : '-'

                        modal.find('#detailDisposition').find('tbody').append(`
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td>${disposisi}</td>
                            <td>${status}</td>
                            <td>${recievedDate || '-'}</td>
                            <td>${approvedDate || '-'}</td>
                            <td>${element?.verifikator?.name || '-'}</td>
                            <td>${element.keterangan || '-'}</td>
                        </tr>
                    `);
                    }
                })
            })
        })
    </script>
</body>

</html>