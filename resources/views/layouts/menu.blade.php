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
        <i class="fa fa-cart-plus" style="color: #76fc74;"></i>
        <p>Ventas</p>
    </a>
</li>

@can('ciudades index')
    <li class="nav-item">
        <a href="{{ route('ciudades.index') }}" class="nav-link {{ Request::is('ciudades.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-blog"></i>
            <p>Ciudades</p>
        </a>
    </li>
@endcan

    <li class="nav-item">
        <a href="{{ route('clientes.index') }}" class="nav-link {{ Request::is('clientes.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user"></i>
            <p>Clientes</p>
        </a>
    </li>


<li class="nav-item">
    <a href="{{ route('entidad_emisora.index') }}"
        class="nav-link {{ Request::is('entidad_emisora.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-building"></i>
        <p>Entidad Emisora</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('articulos.index') }}" class="nav-link
        {{ Request::is('articulos*') ? 'active' : '' }}">
        <i class="fab fa-amazon"></i>
        <p> Articulos</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('compras.index') }}" class="nav-link
        {{ Request::is('compras*') ? 'active' : '' }}">
        <i class="fa fa-cart-plus" style="color: #74a1fc;"></i>
                <p> Compras</p>
    </a>
</li>


<li
    class="nav-item {{ Request::is('reportes/rpt_clientes*')
    || Request::is('reportes/rpt_ventas*')
     ? 'menu-is-opening menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::is('reportes/rpt_clientes*') ? 'active' : '' }}">
        <i class="far fa-chart-bar"></i>
        <p>
            Reporteria
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>

    <ul class="nav nav-treeview"
        style="display:
        {{ Request::is('reportes/rpt_clientes*')
        || Request::is('reportes/rpt_ventas*')
        ? 'block;' : 'none;' }};">

        <li class="nav-item">
            <a href="{{ url('reportes/rpt_clientes') }}"
            class="nav-link {{ Request::is('reportes/rpt_clientes*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Reporte Clientes</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('reportes/rpt_ventas') }}"
                class="nav-link {{ Request::is('reportes/rpt_ventas*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Reporte Ventas</p>
            </a>
        </li>


        <li class="nav-item">
            <a href="{{ url('reportes/rpt_compras') }}"
                class="nav-link {{ Request::is('reportes/rpt_compras*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Reporte Compra</p>
            </a>
        </li>

        
    </ul>
</li>

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

        <li class="nav-item">
            <a href="{{ route('usuarios.index') }}" class="nav-link {{ Request::is('usuarios*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Usuarios</p>
            </a>
        </li>

       
        <li class="nav-item">
            <a href="{{ route('permisos.index') }}"
                class="nav-link {{ Request::is('permissions*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Permisos</p>
            </a>
        </li>
       

       
        <li class="nav-item">
            <a href="{{ route('roles.index') }}" class="nav-link {{ Request::is('roles*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Roles</p>
            </a>
        </li>
     

    </ul>
</li>
