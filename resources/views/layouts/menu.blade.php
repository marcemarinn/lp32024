<!-- need to remove -->
<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Home</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('departamento.index') }}" class="nav-link {{ Request::is('departamento.index') ? 'active' : '' }}">
    <i class="nav-icon fas fa-city"></i>
    <p>Departamentos</p>
    </a>
</li>

<!-- need to remove -->
<li class="nav-item">
    <a href="{{ route('ciudades.index') }}" class="nav-link {{ Request::is('ciudades.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-city"></i>       
        <p>Ciudades</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('entidad_emisora.index') }}" class="nav-link {{ Request::is('entidad_emisora.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-building"></i>       
        <p>Entidad Emisora</p>
    </a>
</li>


<li class="nav-item">
    <a href="{{ route('clientes.index') }}" class="nav-link {{ Request::is('clientes.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user"></i>       
        <p>Clientes</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('forma_pagos.index') }}" class="nav-link {{ Request::is('forma_pagos.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user"></i>       
        <p>Forma Pagos</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('articulos.index') }}" class="nav-link {{ Request::is('articulos.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user"></i>       
        <p>Articulos</p>
    </a>
</li>
