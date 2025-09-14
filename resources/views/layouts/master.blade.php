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
    <link rel="icon" href="{{ asset('logo/uinxs.png') }}" type="image/png">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        * {
            font-family: 'Rubik', sans-serif;
        }
    </style>
    @stack('css')

</head>

<body>

    <div id="wrapper">

        @include('layouts.sidebar')

        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <button type="button" class="navbar-minimalize minimalize-styl-2 btn btn-secondary"><i class="fa fa-bars"></i></button>
                        <span class="nav minimalize-styl-2 text-muted"><b>Tanggal</b> :&nbsp;<span id="lifeTime"></span></span>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <span class="m-r-sm text-muted welcome-message">Layanan Tata Usaha - Fakultas Sains & Teknologi</span>
                        </li>
                        @if(count(auth()->user()->roles) > 1)
                        <li class="dropdown show">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="true">
                                <i class="fa fa-users"></i> <span class="label label-warning">{{ count(auth()->user()->roles) }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-messages">
                                <li>
                                    <div class="dropdown-messages-box">
                                        <div class="media-body">
                                            <strong>Login Sebagai:</strong><br>
                                        </div>
                                    </div>
                                </li>
                                <li class="dropdown-divider"></li>

                                @foreach(auth()->user()->roles as $key => $role)
                                <li>
                                    <a href="{{ route('profile.role', ['user_role_id' => $role->id]) }}">
                                        <div class="dropdown-messages-box">
                                            <div class="media-body">
                                                {{ $loop->iteration }}. {{ $role->role->name }}{{ isset($role->prodi) ? ' - '.$role->prodi->name : '' }} <strong>{{ auth()->user()->role_id == $role->role->id ? '(Aktif)' : '' }}</strong> <br>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                @endforeach
                            </ul>
                        </li>
                        @endif
                    </ul>
                </nav>
            </div>

            @yield('content')

            <div class="footer">
                <div class="float-right">
                    <?= date('Y') ?>
                </div>
                <div>
                    &copy; Layanan Tata Usaha - Fakultas Sains & Teknologi</a>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')

</body>

</html>