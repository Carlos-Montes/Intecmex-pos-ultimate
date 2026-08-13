<div class="sidebar" id="sidebar">
    <div class="logo">
        <img src="{{ url('/static/web/images/logo.png') }}">
        <a href="#" id="trigger_sidebar_close" class="hide-in-desktop"><i class="bi bi-x"></i></a>
    </div>
    <ul>
        <li>
            <a href="{{ url('/') }}">
                <i class="bi bi-arrow-bar-right"></i>
                Pagina Principal
            </a>
        </li>
        @if(getPermissions(Auth::user()->permissions, 'dashboard'))
        <li>
            <a href="{{ url('/') }}" class="lk-dashboard">
                <i class="bi bi-house"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @endif

        {{-- @if(getPermissions(Auth::user()->permissions, 'users_list'))
        <li>
            <a href="#" class="lk-users_list lk-users_view lk-users_edit lk-users_add lk-users_permissions sidebar_accordion" data-target="accordion_users">
                <i class="bi bi-people"></i> 
                <span>Usuarios</span>
                <span class="row-icon" id="row_icon_accordion_users"><i class="bi bi-caret-right-fill"></i></span>
            </a>
            <ul id="accordion_users" class="accordin_ul">
                <li>
                    <a href="{{ url('/users/list/all') }}" id="side_lk_users_all">
                        <i class="bi bi-circle"></i>
                        Todos
                    </a>
                </li>
                @foreach (user_role() as $key => $role)
                <li>
                    <a href="{{ url('/users/list/'.$key) }}" id="side_lk_users_{{ $key }}">
                        <i class="bi bi-circle"></i>
                        {{ $role }}
                    </a>
                </li>
                @endforeach
            </ul>
        </li>
        @endif

        @if(getPermissions(Auth::user()->permissions, 'citas_list'))
        <li>
            <a href="#" class="lk-citas_list lk-citas_view lk-citas_edit lk-citas_add lk-citas_details sidebar_accordion" data-target="accordion_citas">
                <i class="bi bi-list-check"></i> 
                <span>Citas</span>
                <span class="row-icon" id="row_icon_accordion_citas"><i class="bi bi-caret-right-fill"></i></span>
            </a>
            <ul id="accordion_citas" class="accordin_ul">
                <li>
                    <a href="{{ url('/citas/list/all') }}" id="side_lk_citas_all">
                        <i class="bi bi-circle"></i>
                        Todos
                    </a>
                </li>
                @if(Auth::user()->role == 1 || Auth::user()->role == 3)
                    @foreach (user_citas() as $key => $role)
                    <li>
                        <a href="{{ url('/citas/list/'.$key) }}" id="side_lk_citas_{{ $key }}">
                            <i class="bi bi-circle"></i>
                            {{ $role }}
                        </a>
                    </li>
                    @endforeach
                @endif
                @if(getPermissions(Auth::user()->permissions, 'citas_add'))
                <li>
                    <a href="{{ url('citas/add/cita') }}" id="side_lk_citas_new">
                        <i class="bi bi-circle"></i>
                        Agregar nueva cita
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        @if(getPermissions(Auth::user()->permissions, 'transports_list'))
        <li>
            <a href="{{ url('/transports/list') }}" class="lk-transports_list">
                <i class="bi bi-car-front"></i>
                <span>Transportes</span>
            </a>
        </li>
        @endif

        @if(getPermissions(Auth::user()->permissions, 'documents_list'))
        <li>
            <a href="{{ url('/documents/list') }}" class="lk-documents_list">
                <i class="bi bi-file-earmark-break-fill"></i>
                <span>Documentos</span>
            </a>
        </li>
        @endif

        @if(getPermissions(Auth::user()->permissions, 'platform_settings'))
        <li>
            <a href="{{ url('/settings') }}" class="lk-platform_settings">
                <i class="bi bi-life-preserver"></i>
                <span>Configuración</span>
            </a>
        </li>
        @endif --}}
    </ul>
</div>