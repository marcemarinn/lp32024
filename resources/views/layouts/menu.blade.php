<!-- need to remove -->
<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Home</p>
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
        <i class="nav-icon fas fa-city"></i>       
        <p>Entidad Emisora</p>
    </a>
</li>
