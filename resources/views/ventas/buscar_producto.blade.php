<table class="table">
    <thead>
        <tr>
            <th>Código de Producto</th>
            <th>Producto</th>
            <th>Precio</th>
        </tr>
    </thead>
    <tbody>
        @forelse($productos as $product)
            <tr>
                <td>{{ $product->id_articulo }}</td>
                <td>{{ $product->art_descripcion }}</td>
                <td>{{ $product->art_precio }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3">No se encontraron productos.</td>
            </tr>
        @endforelse
    </tbody>
</table>