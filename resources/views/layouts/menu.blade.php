<!-- need to remove -->
<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Home</p>
    </a>
</li>


<li class="nav-item">
    <a href="{{ route('ventas.index') }}" class="nav-link
        {{ Request::is('ventas.index') ? 'active' : '' }}">
        <i class="fa fa-cart-plus" style="color: #63E6BE;"></i>    
        <p>Ventas</p>
    </a>
</li>


    <li class="nav-item">
        <a href="{{ route('ciudades.index') }}" class="nav-link {{ Request::is('ciudades.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-blog"></i>
            <p>Ciudades</p>
        </a>
    </li>


    <li class="nav-item">
        <a href="{{ route('clientes.index') }}" class="nav-link {{ Request::is('clientes.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user"></i>
            <p>Clientes</p>
        </a>
    </li>

    
    <li class="nav-item">
        <a href="{{ route('compras.index') }}" class="nav-link {{ Request::is('compras.index') ? 'active' : '' }}">
            <i class="fa fa-cart-plus" style="color: #74C0FC;"></i> 
            
            <p>Compras</p>
        </a>
    </li>

@can('configuraciones list')
<li class="nav-item">
    <a href="{{ route('entidad_emisora.index') }}"
        class="nav-link {{ Request::is('entidad_emisora.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user"></i>
        <p>Entidad Emisora</p>
    </a>
</li>
@endcan

@can('configuraciones list')
<li class="nav-item">
    <a href="{{ route('articulos.index') }}" class="nav-link
        {{ Request::is('articulos*') ? 'active' : '' }}">
        <i class="fab fa-amazon"></i>
        <p> Articulos</p>
    </a>
</li>
@endcan

<li
    class="nav-item {{ Request::is('reportes/rpt_clientes*') || Request::is('reportes/rpt_ventas*') ? 'menu-is-opening menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::is('reportes/rpt_clientes*') ? 'active' : '' }}">
        <i class="far fa-chart-bar"></i>
        <p>
            Reporteria
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>

    <ul class="nav nav-treeview"
        style="display: {{ Request::is('reportes/rpt_clientes*') || Request::is('reportes/rpt_ventas*') ? 'block;' : 'none;' }};">

        <li class="nav-item">
            <a href="{{ url('reportes/rpt_clientes') }}" class="nav-link {{ Request::is('reportes/rpt_clientes*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Reporte Clientes</p>
            </a>
        </li>
    </ul>
</li>

@can('configuraciones list')
<li
    class="nav-item {{ Request::is('permissions*') || Request::is('roles*') || Request::is('usuarios*') ? 'menu-is-opening menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::is('usuarios*') ? 'active' : '' }}">
        <i class="nav-icon fa fa-cogs"></i>
        <p>
            Configuraciones
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>

    <ul class="nav nav-treeview"
        style="display: {{ Request::is('permissions*') || Request::is('roles*') || Request::is('usuarios*') ? 'block;' : 'none;' }};">

        @can('usuarios index')
        <li class="nav-item">
            <a href="{{ route('usuarios.index') }}" class="nav-link {{ Request::is('usuarios*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Usuarios</p>
            </a>
        </li>
        @endcan

        @can('permissions index')
        <li class="nav-item">
            <a href="{{ route('permissions.index') }}"
                class="nav-link {{ Request::is('permissions*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Permisos</p>
            </a>
        </li>
        @endcan

        @can('roles index')
        <li class="nav-item">
            <a href="{{ route('roles.index') }}" class="nav-link {{ Request::is('roles*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Roles</p>
            </a>
        </li>
        @endcan

    </ul>
</li>
@endcan
