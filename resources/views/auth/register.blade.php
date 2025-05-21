<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Website Pelayanan - Sistem Monitoring SPJ</title>

    <link href="{{ asset('build/assets') }}/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('build/assets') }}/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="{{ asset('build/assets') }}/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="{{ asset('build/assets') }}/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="{{ asset('build/assets') }}/css/plugins/ladda/ladda-themeless.min.css" rel="stylesheet">

    <link href="{{ asset('build/assets') }}/css/animate.css" rel="stylesheet">
    <link href="{{ asset('build/assets') }}/css/style.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        * {
            font-family: 'Rubik', sans-serif;
        }
    </style>

</head>

<body class="gray-bg">

    <div class="passwordBox animated fadeInDown">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    <img src="{{ asset('build/assets') }}/img/logo-uin.png" width="160" height="120" alt="">
                    <h2 class="font-bold">Website Pelayanan <br> <small>Sistem Monitoring SPJ</small></h2>
                </div>
                <div class="ibox-content">
                    <p>Daftar sebagai anggota baru</p>
                    <form class="m-t" role="form" id="loginForm" method="POST">
                        @csrf

                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Nama lengkap" name="name" value="{{ old('name') }}" required autofocus>
                            <small class="text-danger" id="name_error">@if($errors->has('name')) {{ $errors->first('name') }} @endif</small>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="NIP/NIM/NIDN" name="no_identity" value="{{ old('no_identity') }}" required>
                            <small class="text-danger" id="no_identity_error">@if($errors->has('no_identity')) {{ $errors->first('no_identity') }} @endif</small>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}" required>
                            <small class="text-danger" id="email_error">@if($errors->has('email')) {{ $errors->first('email') }} @endif</small>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" autocomplete="off" placeholder="Kata sandi" name="password" required>
                            <small class="text-danger" id="password_error">@if($errors->has('password')) {{ $errors->first('password') }} @endif</small>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" autocomplete="off" placeholder="Ketik ulang kata sandi" name="password_confirmation" required>
                            <small class="text-danger" id="password_confirmation_error">@if($errors->has('password_confirmation')) {{ $errors->first('password_confirmation') }} @endif</small>
                        </div>

                        <button type="submit" class="btn btn-primary block full-width m-b ladda-button ladda-button-demo" data-style="zoom-in">Daftar</button>

                        <p class="m-t text-center"><a href="{{ route('login') }}">Saya telah menjadi anggota?</a></p>
                    </form>
                    <p class="m-t"></p>
                </div>
            </div>
        </div>
        <hr />
        <div class="row">
            <div class="col-md-12">
                &copy; <small>{{ date('Y') }} &bullet; Sistem Informasi Pelayanan Tata Usaha</small>
            </div>

        </div>
    </div>

    <script src="{{ asset('build/assets') }}/js/jquery-3.1.1.min.js"></script>
    <script src="{{ asset('build/assets') }}/js/plugins/iCheck/icheck.min.js"></script>
    <script src="{{ asset('build/assets') }}/js/plugins/validate/jquery.validate.min.js"></script>
    <script src="{{ asset('build/assets') }}/js/plugins/sweetalert/sweetalert.min.js"></script>

    <!-- Ladda -->
    <script src="{{ asset('build/assets') }}/js/plugins/ladda/spin.min.js"></script>
    <script src="{{ asset('build/assets') }}/js/plugins/ladda/ladda.min.js"></script>
    <script src="{{ asset('build/assets') }}/js/plugins/ladda/ladda.jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
            });

            let ladda = $('.ladda-button-demo').ladda();

            $("#loginForm").validate({
                rules: {
                    name: 'required',
                    no_identity: 'required',
                    email: 'required',
                    password: 'required',
                    password_confirmation: 'required',
                },
                messages: {
                    name: "Nama lengkap tidak boleh kosong",
                    no_identity: "NIP/NIM/NIDN tidak boleh kosong",
                    email: "Email tidak boleh kosong",
                    password: "Kata sandi tidak boleh kosong",
                    password_confirmation: "Ulangi kata sandi tidak boleh kosong",
                },
                success: function(messages) {
                    $(messages).remove();
                },
                errorPlacement: function(error, element) {
                    let name = element.attr("name")
                    $("#" + name + "_error").text(error.text())
                },
                submitHandler: function(form) {
                    $(form).submit()
                    // $.ajax({
                    //     url: "{{ route('register') }}",
                    //     type: "POST",
                    //     data: $(form).serialize(),
                    //     dataType: 'json',
                    //     success: function(res) {
                    //         window.location.href = "{{ route('dashboard') }}"
                    //     },
                    //     error: function(res) {
                    //         $('input[name="password"]').val(null)
                    //         $('input[name="password_confirmation"]').val(null)
                    //         console.log(res);

                    //         swal({
                    //             title: 'Login Gagal',
                    //             text: 'email atau password salah!',
                    //         });
                    //     }
                    // })
                }
            });
        });
    </script>
</body>

</html>