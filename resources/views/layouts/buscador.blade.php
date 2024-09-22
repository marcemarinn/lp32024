<form id="form-busqueda">
    <div class="input-group mb-3">
        <input type="text" class="form-control" name="buscar" id="buscar"
            value="{{ request()->get('buscar', null) }}"
            placeholder="Buscar..."
            aria-describedby="button-addon2">
        <button class="btn btn-outline-secondary"
        type="submit" id="button-addon2">Buscar</button>
    </div>
</form>
