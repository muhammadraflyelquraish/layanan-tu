@extends('layouts.master')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-4">

            @if(session()->has('status'))
            <div class="alert alert-success alert-dismissible">
                {{ session('status') }}
                <button type="button" class="close" style="font-weight: 500; line-height: 0.75;" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Profile</h5>
                </div>

                <div class="ibox-content">
                    <form action="{{ route('profile.update', $user->id) }}" method="POST" id="formRole">
                        @csrf
                        @method('PATCH')

                        <h5>Informasi Pengguna</h5>
                        <div class="form-group">
                            <label>Nama lengkap</label>
                            <input type="text" class="form-control" name="name" value="{{ $user->name }}" required autofocus>
                            <small class="text-danger" id="name_error">@if($errors->has('name')) {{ $errors->first('name') }} @endif</small>
                        </div>
                        <div class="form-group">
                            <label>NIM/NIP/NIDN</label>
                            <input type="text" class="form-control" name="no_identity" value="{{ $user->no_identity }}" required>
                            <small class="text-danger" id="no_identity_error">@if($errors->has('no_identity')) {{ $errors->first('no_identity') }} @endif</small>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" readonly class="form-control" value="{{ $user->email }}" required>
                        </div>

                        <br>
                        <h5>Ubah Password <br> <small>(Kosongkan jika tidak ingin mengubah password)</small></h5>
                        <div class="form-group">
                            <label>Password Lama</label>
                            <input type="password" class="form-control" autocomplete="off" name="old_password">
                            <small class="text-danger" id="old_password_error">@if($errors->has('old_password')) {{ $errors->first('old_password') }} @endif</small>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" autocomplete="off" name="password">
                            <small class="text-danger" id="password_error">@if($errors->has('password')) {{ $errors->first('password') }} @endif</small>
                        </div>
                        <div class="form-group">
                            <label>Ketik ulang kata sandi</label>
                            <input type="password" class="form-control" autocomplete="off" name="password_confirmation">
                            <small class="text-danger" id="password_confirmation_error">@if($errors->has('password_confirmation')) {{ $errors->first('password_confirmation') }} @endif</small>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group row">
                            <div class="col-sm-12 col-sm-offset-2">
                                <button class="btn btn-success float-right ladda-button ladda-button-demo" type="submit"><i class="fa fa-edit"></i> Update</button>
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

        $("#formRole").validate({
            rules: {
                name: 'required',
                no_identity: 'required',
            },
            messages: {
                name: "Nama lengkap tidak boleh kosong",
                no_identity: "NIP/NIM/NIDN tidak boleh kosong",
            },
            success: function(messages) {
                $(messages).remove();
            },
            errorPlacement: function(error, element) {
                let name = element.attr("name");
                $("#" + name + "_error").text(error.text());
            },
            submitHandler: function(form) {
                swal({
                    title: `Update Profile?`,
                    text: 'Click "Ya" untuk melanjutkan',
                    showCancelButton: true,
                    confirmButtonColor: "#007bff",
                    confirmButtonText: `Ya, Update`,
                    closeOnConfirm: false
                }, function() {
                    LaddaStart()
                    swal.close();
                    form.submit()
                    LaddaAndDrawTable()
                });
            }
        });

    })
</script>
@endpush