<!-- need to remove -->
<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Home</p>
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
    <a href="{{ route('entidad_emisora.index') }}"
        class="nav-link {{ Request::is('entidad_emisora.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user"></i>
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

<li class="nav-item {{ Request::is('usuarios*') ? 'menu-is-opening menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::is('usuarios*') ? 'active' : '' }}">
        <i class="nav-icon fa fa-cogs"></i>
        <p>
            Configuraciones
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>

    <ul class="nav nav-treeview"
        style="display: {{ Request::is('usuarios*') ? 'block;' : 'none;' }};">
        <li class="nav-item">
            <a href="{{ route('usuarios.index') }}"
            class="nav-link {{ Request::is('usuarios*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Usuarios</p>
            </a>
        </li>

        
    </ul>
</li>


<li class="nav-item">
    <a href="{{ route('ventas.index') }}" class="nav-link
        {{ Request::is('ventas*') ? 'active' : '' }}">
        <i class="fab fa-amazon"></i>
        <p> Ventas</p>
    </a>
</li>
