@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Disposisi</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <strong>Disposisi</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5><button class="btn btn-primary btn-sm" data-toggle="modal" data-mode="add" data-target="#ModalAddEdit"><i class="fa fa-plus-square mr-1"></i> Buat Disposisi</button></h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1px">No</th>
                                    <th>Nama</th>
                                    <th>Approver</th>
                                    <th>Urutan</th>
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

<div class="modal fade" id="ModalAddEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formAddEdit" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Nama</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="name" name="name" tabindex="2" required>
                            <small class="text-danger" id="name_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Approver</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="approver_id" id="approver_id" required>
                                <option value="" selected disabled>Pilih approver</option>
                                @foreach($approver as $pm)
                                @php
                                $approverId = $pm->id;
                                @endphp
                                <option value="{{ $approverId }}" @selected(old('approver_id')==$approverId)>{{ $pm->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="approver_id_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Urutan</label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" id="urutan" name="urutan" tabindex="2" required>
                            <small class="text-danger" id="urutan_error"></small>
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
                name: "Nama tidak boleh kosong",
                approver_id: "Approver tidak boleh kosong",
                urutan: "Urutan tidak boleh kosong"
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
                        sweetalert('Gagal', 'Terjadi kesalahan', 'error')
                    }
                })
            }
        });


        let serverSideTable = $('.dataTables').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [3, 'asc']
            ],
            ajax: {
                url: "{{ route('disposisi.data') }}",
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'approver.name',
                    name: 'approver.name'
                },
                {
                    data: 'urutan',
                    name: 'urutan'
                },
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    orderable: false
                }
            ],
            search: {
                regex: true
            }
        });

        $('#ModalAddEdit').on('shown.bs.modal', function(e) {
            let button = $(e.relatedTarget)
            let modal = $(this)
            if (button.data('mode') == 'edit') {
                let id = button.data('integrity')
                let closeTr = button.closest('tr')
                $('#formAddEdit').attr('action', '{{ route("disposisi.store") }}/' + id).attr('method', 'PUT')

                modal.find('#modal-title').text('Edit Disposisi');
                $.get('{{ route("disposisi.store") }}/' + id, function(app) {
                    modal.find('#name').val(app.name)
                    modal.find('#approver_id').val(app.approver.id)
                    modal.find('#urutan').val(app.urutan)
                    modal.find('#approver_id').select2()
                })
            } else {
                $('#formAddEdit').trigger('reset').attr('action', '{{ route("disposisi.store") }}').attr('method', 'POST')
                modal.find('#modal-title').text('Buat Disposisi');
                modal.find('#approver_id').select2()
            }
        })

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
                    url: "{{ route('disposisi.store') }}/" + id,
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

    });
</script>
@endpush