@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Edit Role</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <span>Managemen Akses</span>
            </li>
            <li class="breadcrumb-item">
                <span><a href="{{ route('role.index') }}"><u>Role</u></a></span>
            </li>
            <li class="breadcrumb-item active">
                <strong>Edit</strong>
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
                    <form action="{{ route('role.update', $role->id) }}" method="POST" id="formRole">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <label>Nama role</label>
                            <input type="text" name="name" class="form-control" required value="{{ $role->name }}" autofocus>
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
                                    @php
                                    $permissions = [
                                    'DASHBOARD' => 'Beranda',
                                    'LETTER' => 'Proposal',
                                    'SPJ' => 'SPJ',
                                    'DISPOSISI' => 'Disposisi',
                                    'ARSIP' => 'Arsip',
                                    'USER' => 'Pengguna',
                                    'ROLE' => 'Akses',
                                    ]
                                    @endphp

                                    @foreach($role->permissions as $permission)
                                    <tr>
                                        <td>{{ $permissions[$permission->menu] }}</td>
                                        <td class="text-center">
                                            <div class="i-checks"><input type="checkbox" name="{{ strtolower($permission->menu).'_permitted' }}" @if($permission->is_permitted) checked @endif name="permissions[{{ $permission->menu }}]"></div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group row">
                            <div class="col-sm-12 col-sm-offset-2">
                                <button class="btn btn-primary float-right" type="submit"><i class="fa fa-save"></i> Ubah</button>
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