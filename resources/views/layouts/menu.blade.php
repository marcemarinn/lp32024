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
