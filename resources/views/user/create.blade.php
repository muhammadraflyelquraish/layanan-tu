@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Tambah Pengguna</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <span>Managemen Akses</span>
            </li>
            <li class="breadcrumb-item">
                <span><a href="{{ route('user.index') }}"><u>Pengguna</u></a></span>
            </li>
            <li class="breadcrumb-item active">
                <strong>Tambah</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Data Pengguna</h5>
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

                    <form action="{{ route('user.store') }}" method="POST" id="formRole">
                        @csrf
                        @method('POST')

                        <h5>Informasi Pengguna</h5>

                        <div class="form-group">
                            <label>Nama lengkap</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                            <small class="text-danger" id="name_error">@if($errors->has('name')) {{ $errors->first('name') }} @endif</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NIM/NIP/NIDN</label>
                                    <input type="text" class="form-control" name="no_identity" value="{{ old('no_identity') }}" required>
                                    <small class="text-danger" id="no_identity_error">@if($errors->has('no_identity')) {{ $errors->first('no_identity') }} @endif</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                    <small class="text-danger" id="email_error">@if($errors->has('email')) {{ $errors->first('email') }} @endif</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" autocomplete="off" required minlength="6" maxlength="30">
                                        <div class="input-group-prepend">
                                            <a href="javascript:void(0)" id="hideShow" data-show="false" class="input-group-addon" style="border-left: none;"><i class="fa fa-eye"></i></a>
                                        </div>
                                    </div>
                                    <small class="text-danger" id="password_error">@if($errors->has('password')) {{ $errors->first('password') }} @endif</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ketik ulang kata sandi</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password_confirmation" autocomplete="off" required minlength="6" maxlength="30">
                                        <div class="input-group-prepend">
                                            <a href="javascript:void(0)" id="hideShow" data-show="false" class="input-group-addon" style="border-left: none;"><i class="fa fa-eye"></i></a>
                                        </div>
                                    </div>
                                    <small class="text-danger" id="password_confirmation_error">@if($errors->has('password_confirmation')) {{ $errors->first('password_confirmation') }} @endif</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status" required>
                                <option value="ACTIVE">Aktif</option>
                                <option value="INACTIVE">Non Aktif</option>
                            </select>
                            <small class="text-danger" id="status_error">@if($errors->has('status')) {{ $errors->first('status') }} @endif</small>
                        </div>

                        <hr>
                        <h5>Akses Pengguna</h5>
                        <table class="table table-bordered" width="100%" id="user-roles">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1%">No</th>
                                    <th width="49%">Role</th>
                                    <th width="49%">Prodi</th>
                                    <th class="text-right" width="1%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" id="iteration">1</td>
                                    <td>
                                        <select name="roles[]" id="roles" class="form-control select2-role" required>
                                            <option selected value="">Pilih Role..</option>
                                            @foreach(App\Models\Role::pluck('name', 'id') as $id => $name)
                                            <option value=" {{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="prodis[]" id="prodis" class="form-control" hidden>
                                            <option selected value="">Pilih Prodi..</option>
                                            @foreach(App\Models\Prodi::pluck('name', 'id') as $id => $name)
                                            <option value=" {{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><button class="btn btn-sm btn-outline-danger" id="#" disabled type="button"><i class="fa fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5"><button class="btn btn-default" id="add-role" type="button"><i class="fa fa-plus"></i> Tambah Akses</button></td>
                                </tr>
                            </tfoot>
                        </table>


                        <div class="form-group row">
                            <div class="col-sm-12 col-sm-offset-2">
                                <div class="btn-group pull-right">
                                    <a href="{{ route('user.index') }}" class="btn btn-default float-right"><i class="fa fa-arrow-left"></i> Kembali</a>
                                    <button class="btn btn-success float-right" type="submit"><i class="fa fa-save"></i> Simpan</button>
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

        $(document).on('click', '#hideShow', function() {
            let passwordInput = $(this).closest('.input-group').find('#password');
            if ($(this).attr('data-show') == 'false') {
                passwordInput.attr('type', 'text')
                $(this).attr('data-show', true).html('<i class="fa fa-eye-slash"></i>')
            } else {
                passwordInput.attr('type', 'password')
                $(this).attr('data-show', false).html('<i class="fa fa-eye"></i>')
            }
        })

        // $("#formRole").validate({
        //     rules: {
        //         name: 'required',
        //         no_identity: 'required',
        //         email: 'required',
        //         password: 'required',
        //         password_confirmation: 'required',
        //         role_id: 'required',
        //         status: 'required',
        //     },
        //     messages: {
        //         name: "Nama lengkap tidak boleh kosong",
        //         no_identity: "NIP/NIM/NIDN tidak boleh kosong",
        //         email: "Email tidak boleh kosong",
        //         password: "Kata sandi tidak boleh kosong",
        //         password_confirmation: "Ulangi kata sandi tidak boleh kosong",
        //         role_id: 'Role tidak boleh kosong',
        //         status: 'Status tidak boleh kosong',
        //     },
        //     success: function(messages) {
        //         $(messages).remove();
        //     },
        //     errorPlacement: function(error, element) {
        //         let name = element.attr("name");
        //         $("#" + name + "_error").text(error.text());
        //     },
        //     submitHandler: function(form) {
        //         // form.submit()
        //     }
        // });

        $("#formRole").on('submit', function(e) {
            e.preventDefault()
            let isValid = true
            const requireProdi = [2, 6, 7, 8];

            $('select[id^="roles"]').each(function(i, e) {
                let roleId = $(this).closest('tr').find('#roles').val();
                let prodiId = $(this).closest('tr').find('#prodis').val();

                if (!roleId) {
                    sweetalert('Data tidak valid', `Mohon isi role pada row ke-${i+1}.`, 'info');
                    isValid = false
                    return false;
                }

                if (requireProdi.includes(Number(roleId)) && !prodiId) {
                    sweetalert('Data tidak valid', `Mohon isi prodi pada row ke-${i+1}.`, 'info');
                    isValid = false
                    return false;
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
                }, (isConfirm) => {
                    if (isConfirm) {
                        LaddaStart();
                        this.submit();
                    }
                });
            }
        });


        $('.select2-role').select2({
            placeholder: "Pilih Role..",
            allowClear: true
        });

        $('.select2-prodi').select2({
            placeholder: "Pilih Role..",
            allowClear: true
        });

        $(document).on('click', '#add-role', function(e) {
            let row = `
                <tr>
                    <td class="text-center" id="iteration">1</td>
                    <td>
                        <select name="roles[]" id="roles" class="form-control select2-role" required>
                            <option selected value="">Pilih Role..</option>
                            @foreach(App\Models\Role::pluck('name', 'id') as $id => $name)
                            <option value=" {{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="prodis[]" id="prodis" class="form-control" hidden>
                            <option selected value="">Pilih Prodi..</option>
                            @foreach(App\Models\Prodi::pluck('name', 'id') as $id => $name)
                            <option value=" {{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><button class="btn btn-sm btn-outline-danger" id="remove-role" type="button"><i class="fa fa-trash"></i></button></td>
                </tr>
            `
            $('#user-roles').find('tbody').append(row)
            iteration()

            // Reapply select2-role style after adding new row
            $('.select2-role').select2({
                placeholder: "Pilih Role..",
                allowClear: true
            });

            // Reapply select2-role style after adding new row
            $('.select2-prodi').select2({
                placeholder: "Pilih Prodi..",
                allowClear: true
            });
        })

        $(document).on('click', '#remove-role', function(e) {
            const row = $(this).closest('tr').remove()
            iteration()
        })

        $(document).on('change', '.select2-role', function(e) {
            const value = Number($(this).val());
            const requireProdi = [2, 6, 7, 8];
            const prodis = $(this).closest('tr').find('#prodis');

            if (requireProdi.includes(value)) {
                prodis.removeAttr('hidden');
                // prodis.addClass('select2-prodi')
            } else {
                prodis.attr('hidden', true);
                prodis.val('').trigger('change');
            }

            // $('.select2-prodi').select2({
            //     placeholder: "Pilih Prodi..",
            //     allowClear: true
            // });
        });

        function iteration() {
            $('#user-roles').find('tbody tr').each(function(index) {
                $(this).find('#iteration').text(index + 1);
            });
        }

    })
</script>
@endpush