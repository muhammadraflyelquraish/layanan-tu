<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    @if (Auth::user()->avatar != null)
                    <img alt="image" class="rounded-circle" src="{{ Auth::user()->avatar }}" width="48" height="48" />
                    @else
                    <img alt="image" class="rounded-circle" src="{{ asset('build/assets') }}/img/default-profile.png" width="48" height="48" />
                    @endif
                    <div class="dropdown-toggle">
                        <span class="block m-t-xs font-bold text-white">Hai, {{ substr(Auth::user()->name, 0, 20) }}..</span>
                        <span class="text-muted text-xs block">{{ Auth::user()->role->name }}</span>
                    </div>
                </div>
                <div class="logo-element">
                    TU
                </div>
            </li>

            @php $permissions = \App\Models\RolePermission::where('role_id', Auth::user()->role_id)->pluck('is_permitted', 'menu') @endphp

            @if ($permissions['DASHBOARD'])
            <li class="{{( request()->routeIs('dashboard')) ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">Beranda</span></a>
            </li>
            @endif

            @if ($permissions['LETTER'])
            <li class="{{( request()->routeIs('letter.index')) ? 'active' : '' }}">
                <a href="{{ route('letter.index') }}"><i class="fa fa-book"></i> <span class="nav-label">Pengajuan</span></a>
            </li>
            @endif

            @if ($permissions['SPJ'])
            <li class="{{( 
                request()->routeIs('spj.index') OR
                request()->routeIs('spj.create') OR
                request()->routeIs('spj.edit') OR
                request()->routeIs('spj.show') OR
                request()->routeIs('spj.approval.view') OR
                request()->routeIs('spj.revisi') OR
                request()->routeIs('spj.rating')
                ) ? 'active' : '' }}">
                <a href="{{ route('spj.index') }}"><i class="fa fa-book"></i> <span class="nav-label">SPJ</span></a>
            </li>
            @endif

            @if ($permissions['DISPOSISI'])
            <li class="{{( 
                request()->routeIs('disposisi.index') OR
                request()->routeIs('disposisi.create') OR
                request()->routeIs('disposisi.edit') OR
                request()->routeIs('disposisi.show')
                ) ? 'active' : '' }}">
                <a href="{{ route('disposisi.index') }}"><i class="fa fa-sitemap"></i> <span class="nav-label">Disposisi</span></a>
            </li>
            @endif

            @if ($permissions['ARSIP'])
            <li class="{{( 
                request()->routeIs('arsip.index') OR
                request()->routeIs('arsip.create') OR
                request()->routeIs('arsip.edit') OR
                request()->routeIs('arsip.show')
                ) ? 'active' : '' }}">
                <a href="{{ route('arsip.index') }}"><i class="fa fa-folder-open"></i> <span class="nav-label">Arsip</span></a>
            </li>
            @endif

            @if ($permissions['USER'])
            <li class="{{( 
                request()->routeIs('user.index') OR
                request()->routeIs('user.create') OR
                request()->routeIs('user.edit') OR
                request()->routeIs('user.show')
                ) ? 'active' : '' }}">
                <a href="{{ route('user.index') }}"><i class="fa fa-user"></i> <span class="nav-label">Pengguna</span></a>
            </li>
            @endif

            @if ($permissions['ROLE'])
            <li class="{{( 
                request()->routeIs('role.index') OR
                request()->routeIs('role.create') OR
                request()->routeIs('role.edit') OR
                request()->routeIs('role.show')
                ) ? 'active' : '' }}">
                <a href="{{ route('role.index') }}"><i class="fa fa-key"></i> <span class="nav-label">Akses</span></a>
            </li>
            @endif

            <!-- @if ($permissions['USER'] OR $permissions['ROLE'])
            <li class="{{ (
                request()->routeIs('role.index') OR
                request()->routeIs('role.create') OR
                request()->routeIs('role.edit') OR
                request()->routeIs('user.index') OR
                request()->routeIs('user.create') OR
                request()->routeIs('user.edit')
                ) ? 'active' : '' }}">
                <a href="#"><i class="fa fa-edit"></i> <span class="nav-label">Managemen Akses</span></a>

                <ul class="nav nav-second-level collapse">
                    @if ($permissions['USER'])
                    <li class="{{( 
                    request()->routeIs('user.index') OR
                    request()->routeIs('user.create') OR
                    request()->routeIs('user.edit')
                    )? 'active' : '' }}"><a href="{{ route('user.index') }}">Pengguna</a></li>
                    @endif
                    @if ($permissions['ROLE'])
                    <li class="{{( 
                    request()->routeIs('role.index') OR
                    request()->routeIs('role.create') OR
                    request()->routeIs('role.edit') 
                    )? 'active' : '' }}"><a href="{{ route('role.index') }}">Role</a></li>
                    @endif
                </ul>
            </li>
            @endif -->

            <li class="special_link">
                <a href="javascript:void(0)" id="logout"><i class="fa fa-sign-out"></i> <span class="nav-label">Keluar</span></a>
            </li>
        </ul>

    </div>
</nav>