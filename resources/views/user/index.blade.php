@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Pengguna</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <span>Managemen Akes</span>
            </li>
            <li class="breadcrumb-item active">
                <strong>Pengguna</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5><a class="btn btn-primary btn-sm" href="{{ route('user.create') }}"><i class="fa fa-plus-square mr-1"></i> Tambah Pengguna</a></h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover usersTable" width="100%">
                            <thead>
                                <tr>
                                    <th width="1px">No</th>
                                    <th>Nama</th>
                                    <th>NIM/NIP/NIDN</th>
                                    <th>Email</th>
                                    <th>Role</th>
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
            <form id="formAddEdit" method="POST">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Nama</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="name" name="name" tabindex="1" required maxlength="200">
                            <small class="text-danger" id="name_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Username</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="username" name="username" tabindex="2" required maxlength="200">
                            <small class="text-danger" id="username_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="email" name="email" tabindex="3" required maxlength="200">
                            <small class="text-danger" id="email_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Password</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" autocomplete="off" tabindex="3" required minlength="6" maxlength="30">
                                <div class="input-group-prepend">
                                    <a href="javascript:void(0)" id="hideShow" data-show="false" class="input-group-addon" style="border-left: none;"><i class="fa fa-eye"></i></a>
                                </div>
                            </div>
                            <small class="text-danger" id="password_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">No Telp</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="phone_number" name="phone_number" tabindex="5" required maxlength="30">
                            <small class="text-danger" id="phone_number_error"></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Role</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="role" name="role" tabindex="4" required>
                                <option value="Admin">Admin</option>
                                <option value="Dokter">Dokter</option>
                            </select>
                            <small class="text-danger" id="role_error"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-rectangle-o mr-1"></i>Tutup [Esc]</button>
                    <button type="submit" class="btn btn-primary ladda-button ladda-button-demo" data-style="zoom-in" id="submit" tabindex="5"><i class="fa fa-check-square mr-1"></i>Simpan [Enter]</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {

        let serverSideTable = $('.usersTable').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [1, 'asc']
            ],
            ajax: {
                url: "{{ route('user.data') }}",
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                searchable: false,
                orderable: false,
                className: 'text-center'
            }, {
                data: 'name',
                name: 'name'
            }, {
                data: 'no_identity',
                name: 'no_identity',
            }, {
                data: 'email',
                name: 'email',
            }, {
                data: 'role.name',
                name: 'role.name',
            }, {
                data: 'action',
                name: 'action',
                searchable: false,
                orderable: false
            }],
            search: {
                "regex": true
            }
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

        $('#ModalAddEdit').on('shown.bs.modal', function(e) {
            $('#name').focus();
            let button = $(e.relatedTarget)
            let modal = $(this)
            if (button.data('mode') == 'edit') {
                let id = button.data('integrity')
                let closeTr = button.closest('tr')
                $('#formAddEdit').attr('action', '{{ route("user.store") }}/' + id).attr('method', 'PATCH')

                modal.find('#modal-title').text('Edit Akun');
                modal.find('#password').val(null).attr('disabled', true)

                modal.find('#name').val(closeTr.find('td:eq(1)').text())
                modal.find('#username').val(closeTr.find('td:eq(2)').text())
                modal.find('#email').val(closeTr.find('td:eq(3)').text())
                modal.find('#phone_number').val(closeTr.find('td:eq(4)').text())
                modal.find('#role').val(closeTr.find('td:eq(5)').text())

            } else {
                $('#formAddEdit').trigger('reset').attr('action', '{{ route("user.store") }}').attr('method', 'POST')
                modal.find('#modal-title').text('Tambah Akun');
                modal.find('#password').attr('disabled', false)
                modal.find('#role').val(null)
            }
        })


        $("#formAddEdit").validate({
            messages: {
                name: "Nama tidak boleh kosong",
                role: "Role tidak boleh kosong",
                username: {
                    required: "Username tidak boleh kosong",
                    minlength: "Username minimal 5 karakter"
                },
                email: "Email tidak boleh kosong",
                password: {
                    required: "Password tidak boleh kosong",
                    minlength: "Password minimal 6 karakter"
                },
                phone_number: "No Telp. tidak boleh kosong",
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


        $(document).on('click', '#hideShow', function() {
            if ($(this).attr('data-show') == 'false') {
                $('#password').attr('type', 'text')
                $(this).attr('data-show', true).html('<i class="fa fa-eye-slash"></i>')
            } else {
                $('#password').attr('type', 'password')
                $(this).attr('data-show', false).html('<i class="fa fa-eye"></i>')
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
                    url: "{{ route('user.store') }}/" + id,
                    type: "DELETE",
                    dataType: 'json',
                    success: function(response) {
                        LaddaAndDrawTable()
                        sweetalert("Terhapus!", `Data "${name}" berhasil dihapus.`, null, 500, false)
                    },
                    error: function(response) {
                        LaddaAndDrawTable()
                        sweetalert("Tidak terhapus!", `Terjadi kesalahan saat menghapus data.`, 'error')
                    }
                })
            });
        });


    });
</script>
@endpush