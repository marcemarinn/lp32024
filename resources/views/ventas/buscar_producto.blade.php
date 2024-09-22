<table id="selectedProducts" class="table">
    <thead>
        <tr>
            <th>Código de Producto</th>
            <th>Producto</th>
            <th>Precio</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        @forelse($productos as $product)
            <tr>
                <td>{{ $product->id_articulo }}</td>
                <td>{{ $product->art_descripcion }}</td>
                <td>{{ $product->art_precio }}</td>
                <td>
                    <button type="button" class="btn btn-primary" onclick="seleccionarProducto('{{ $product->id_articulo }}', '{{ $product->art_descripcion }}', '{{ $product->art_precio }}')">
                        Seleccionar
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">No se encontraron productos.</td>
            </tr>
        @endforelse
    </tbody>
</table>
