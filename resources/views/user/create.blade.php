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
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Data Pengguna</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('user.store') }}" method="post" id="formRole">
                        @csrf
                        @method('POST')

                        <div class="form-group">
                            <label>Nama lengkap</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                            <small class="text-danger" id="name_error">@if($errors->has('name')) {{ $errors->first('name') }} @endif</small>
                        </div>
                        <div class="form-group">
                            <label>NIM/NIP/NIDN</label>
                            <input type="text" class="form-control" name="no_identity" value="{{ old('no_identity') }}" required>
                            <small class="text-danger" id="no_identity_error">@if($errors->has('no_identity')) {{ $errors->first('no_identity') }} @endif</small>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                            <small class="text-danger" id="email_error">@if($errors->has('email')) {{ $errors->first('email') }} @endif</small>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select class="form-control" name="role_id" required>
                                <option value="" selected disabled>Pilih Role</option>
                                @foreach($roles as $id => $role)
                                <option value="{{ $id }}" @selected(old('role_id')==$id)>{{ $role }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="role_id_error">@if($errors->has('password_confirmation')) {{ $errors->first('password_confirmation') }} @endif</small>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" autocomplete="off" name="password" required>
                            <small class="text-danger" id="password_error">@if($errors->has('password')) {{ $errors->first('password') }} @endif</small>
                        </div>
                        <div class="form-group">
                            <label>Ketik ulang kata sandi</label>
                            <input type="password" class="form-control" autocomplete="off" name="password_confirmation" required>
                            <small class="text-danger" id="password_confirmation_error">@if($errors->has('password_confirmation')) {{ $errors->first('password_confirmation') }} @endif</small>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status" required>
                                <option value="ACTIVE">Aktif</option>
                                <option value="INACTIVE">Non Aktif</option>
                            </select>
                            <small class="text-danger" id="status_error">@if($errors->has('status')) {{ $errors->first('status') }} @endif</small>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group row">
                            <div class="col-sm-12 col-sm-offset-2">
                                <button class="btn btn-success float-right" type="submit"><i class="fa fa-save"></i> Simpan</button>
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

        $("#formRole").validate({
            rules: {
                name: 'required',
                no_identity: 'required',
                email: 'required',
                password: 'required',
                password_confirmation: 'required',
                role_id: 'required',
                status: 'required',
            },
            messages: {
                name: "Nama lengkap tidak boleh kosong",
                no_identity: "NIP/NIM/NIDN tidak boleh kosong",
                email: "Email tidak boleh kosong",
                password: "Kata sandi tidak boleh kosong",
                password_confirmation: "Ulangi kata sandi tidak boleh kosong",
                role_id: 'Role tidak boleh kosong',
                status: 'Status tidak boleh kosong',
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