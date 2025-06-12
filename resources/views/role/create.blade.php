@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Tambah Role</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <span>Managemen Akses</span>
            </li>
            <li class="breadcrumb-item">
                <span><a href="{{ route('role.index') }}"><u>Role</u></a></span>
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
                    <h5>Data Role</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('role.store') }}" method="post" id="formRole">
                        @csrf
                        @method('POST')

                        <div class="form-group">
                            <label>Nama role</label>
                            <input type="text" name="name" class="form-control" required autofocus>
                            <small class="text-danger" id="name_error">@if($errors->has('name')) {{ $errors->first('name') }} @endif</small>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <h5>Role Akses</h5>

                        <div class="form-group">
                            <table class="table table-bordered">
                                <thead>
                                    <th>Menu</th>
                                    <th>Akses</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Beranda</td>
                                        <td class="text-center">
                                            <div class="i-checks"><input type="checkbox" name="dashboard_permitted"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Proposal</td>
                                        <td class="text-center">
                                            <div class="i-checks"><input type="checkbox" name="letter_permitted"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>SPJ</td>
                                        <td class="text-center">
                                            <div class="i-checks"><input type="checkbox" name="spj_permitted"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Disposisi</td>
                                        <td class="text-center">
                                            <div class="i-checks"><input type="checkbox" name="disposisi_permitted"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Arsip</td>
                                        <td class="text-center">
                                            <div class="i-checks"><input type="checkbox" name="arsip_permitted"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Pengguna</td>
                                        <td class="text-center">
                                            <div class="i-checks"><input type="checkbox" name="user_permitted"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Akses</td>
                                        <td class="text-center">
                                            <div class="i-checks"><input type="checkbox" name="role_permitted"></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group row">
                            <div class="col-sm-12 col-sm-offset-2">
                                <button class="btn btn-primary float-right" type="submit"><i class="fa fa-save"></i> Simpan</button>
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
            messages: {
                name: "Nama role tidak boleh kosong",
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