<div class="sidebar" id="sidebar">
    <div class="logo">
        <img src="{{ url('/static/images/general/Logo.png') }}">
        <a href="#" id="trigger_sidebar_close" class="hide-in-desktop"><i class="bi bi-x"></i></a>
    </div>
    <ul>
        <li>
            <a href="{{ url('/') }}" class="lk-dashboard">
                <i class="bi bi-house"></i>
                <span> Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/categories/list') }}" class="lk-categories lk-subcategories">
                <i class="bi bi-tags-fill"></i>
                <span> Categorías </span>
            </a>
        </li>
        <li>
            <a href="{{ url('/') }}" class="lk-dashboard">
                <i class="bi bi-boxes"></i>
                <span> Productos</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/pos') }}" class="lk-pos">
                <i class="bi bi-receipt"></i>
                <span> POS </span>
            </a>
        </li>
        <li>
            <a href="#" class="lk-finances sidebar_accordion" data-target="accordion_finances">
                <i class="bi bi-people"></i> 
                <span>Contabilidad</span>
                <span class="row-icon" id="row_icon_accordion_finances"><i class="bi bi-caret-right-fill"></i></span>
            </a>
            <ul id="accordion_finances" class="accordin_ul">
                @foreach (getFinances() as $key => $role)
                <li>
                    <a href="{{ url('/finances/list/'.$key) }}" id="side_lk_finances_{{ $key }}">
                        <i class="bi bi-circle"></i>
                        {{ $role }}
                    </a>
                </li>
                @endforeach
            </ul>
        </li>
        <li>
            <a href="{{ url('/inventory/initial') }}" class="lk-inventory">
                <i class="bi bi-card-checklist"></i>
                <span> Inventario</span>
            </a>
        </li>
        <li>
            <a href="#" class="lk-users sidebar_accordion" data-target="accordion_users">
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
                @foreach (getUserRole() as $key => $role)
                <li>
                    <a href="{{ url('/users/list/'.$key) }}" id="side_lk_users_{{ $key }}">
                        <i class="bi bi-circle"></i>
                        {{ $role }}
                    </a>
                </li>
                @endforeach
            </ul>
        </li>
        <li>
            <a href="{{ url('/reports') }}" class="lk-dashboard">
                <i class="bi bi-pie-chart-fill"></i>
                <span> Reportes</span>
            </a>
        </li>   
        <li>
            <a href="{{ url('/settings') }}" class="lk-settings">
                <i class="bi bi-gear-wide-connected"></i>
                <span> Configuración</span>
            </a>
        </li>
    </ul>
</div>
