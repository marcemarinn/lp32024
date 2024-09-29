@push('styles')
<link href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" rel="stylesheet">
<style>
    .form-group {
        margin-bottom: 25px;
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        margin-bottom: 8px;
        font-weight: bold;
    }

    .form-control {
        width: 100%;
        padding: 12px; /* Aumentado el padding para más área clicable */
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    /* Aumenta el ancho máximo del formulario */
    form {
        max-width: 800px;
        margin: 0 auto;
    }

    .form-section {
        background-color: #f8f9fa;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 40px;
    }

    .form-section h4 {
        margin-bottom: 20px;
        color: #495057;
        font-size: 1.25rem;
    }

    /* Ajuste en la tabla de detalles */
    #detalle_compras_table {
        width: 100%;
        margin-top: 30px;
        margin-bottom: 20px;
        border-collapse: separate;
        border-spacing: 0 15px;
    }

    #detalle_compras_table th, #detalle_compras_table td {
        padding: 15px;
        text-align: center;
        vertical-align: middle;
    }

    .btn-sm {
        margin-top: 25px; /* Aumentado el espacio para los botones */
        padding: 10px 20px;
    }

    .text-danger {
        color: #dc3545;
        margin-top: 5px;
    }

    .detalle-actions {
        text-align: center;
    }
</style>
@endpush

<form method="POST" action="{{ route('compras.store') }}">
    @csrf

    <div class="form-section">
        
        <div class="form-group">
            <label for="prov_id">Proveedor</label>
            <select name="prov_id" class="form-control">
                <option value="">Seleccione un Proveedor</option>
                @foreach($proveedores as $prov_id => $prov_nombre)
                    <option value="{{ $prov_id }}">{{ $prov_nombre }}</option>
                @endforeach
            </select>
            @error('prov_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="cod_suc">Sucursal</label>
            <select name="cod_suc" class="form-control">
                <option value="">Seleccione una Sucursal</option>
                @foreach($sucursales as $cod_suc => $suc_descri)
                    <option value="{{ $cod_suc }}">{{ $suc_descri }}</option>
                @endforeach
            </select>
            @error('cod_suc')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="com_fecha">Fecha de la Compra</label>
            <input type="date" name="com_fecha" id="com_fecha" class="form-control" value="{{ old('com_fecha') }}">
            @error('com_fecha')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="com_user_id">Usuario</label>
            <input type="text" name="com_user_id" class="form-control" value="{{ auth()->user()->name }}" readonly>
        </div>
    </div>

    
    <div class="form-section">
        
        <div class="form-group">
            <label for="com_condicion">Condición de Compra</label>
            <select name="com_condicion" id="com_condicion" class="form-control" onchange="toggleCuotas()">
                <option value="">Seleccione una Condición</option>
                <option value="CONTADO">CONTADO</option>
                <option value="CREDITO">CRÉDITO</option>
            </select>
            @error('com_condicion')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="com_cat_cuo">Cantidad de Cuotas</label>
            <input type="number" name="com_cat_cuo" id="com_cat_cuo" class="form-control" value="{{ old('com_cat_cuo') }}" disabled>
            @error('com_cat_cuo')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="com_plazo">Plazo de Pago (en días)</label>
            <input type="number" name="com_plazo" id="com_plazo" class="form-control" value="{{ old('com_plazo') }}" disabled>
            @error('com_plazo')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="form-group">
        <label for="com_descripcion">Descripción de la Compra</label>
        <textarea name="com_descripcion" id="com_descripcion" class="form-control" rows="3">{{ old('com_descripcion') }}</textarea>
        <small class="form-text text-muted">Ingrese una descripción detallada de la compra.</small>
        @error('com_descripcion')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="form-section">
        <h4>Detalles de la Compra</h4>
        
        <table id="detalle_compras_table" class="table table-bordered">
            <thead>
                <tr>
                    <th>Artículo</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="detalle_compras_body">
                <tr class="detalle_item">
                    <td>
                        <select name="detalle_compras[0][id_articulo]" class="form-control">
                            <option value="">Seleccione un Artículo</option>
                            @foreach($articulos as $articulo)
                                <option value="{{ $articulo->id_articulo }}">{{ $articulo->art_descripcion }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="detalle_compras[0][cantidad]" class="form-control" placeholder="Cantidad" oninput="calculateTotal()"></td>
                    <td><input type="number" name="detalle_compras[0][precio_unit]" class="form-control" placeholder="Precio Unitario" oninput="calculateTotal()"></td>
                    <td class="detalle-actions">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeDetalle(this)">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="button" class="btn btn-primary btn-sm" onclick="addDetalle()">Agregar Detalle</button>

        <div class="form-group" style="margin-top: 20px;">
            <label for="com_total">Total:</label>
            <input type="number" name="com_total" id="com_total" class="form-control" readonly>
        </div>
    </div>

    <button type="submit" class="btn btn-success btn-lg" style="width: 100%;">Guardar Compra</button>
</form>

</script>
    @csrf

    <!-- Campos del formulario -->

    {{-- <div class="form-group">
        <label for="prov_id">Proveedor</label>
        <select name="prov_id" class="form-control">
            <option value="">Seleccione un Proveedor</option>
            @foreach($proveedores as $prov_id => $prov_nombre)
                <option value="{{ $prov_id }}">{{ $prov_nombre }}</option>
            @endforeach
        </select>
        @error('prov_id')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="cod_suc">Sucursal</label>
        <select name="cod_suc" class="form-control">
            <option value="">Seleccione una Sucursal</option>
            @foreach($sucursales as $cod_suc => $suc_descri)
                <option value="{{ $cod_suc }}">{{ $suc_descri }}</option>
            @endforeach
        </select>
        @error('cod_suc')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="com_fecha">Fecha de la Compra</label>
        <input type="date" name="com_fecha" id="com_fecha" class="form-control" value="{{ old('com_fecha') }}">
        @error('com_fecha')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="com_user_id">Usuario</label>
        <input type="text" name="com_user_id" class="form-control" value="{{ auth()->user()->name }}" readonly>
    </div>

    <div class="form-group">
        <label for="com_condicion">Condición de Compra</label>
        <select name="com_condicion" id="com_condicion" class="form-control" onchange="toggleCuotas()">
            <option value="">Seleccione una Condición</option>
            <option value="CONTADO">CONTADO</option>
            <option value="CREDITO">CRÉDITO</option>
        </select>
        @error('com_condicion')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="com_cat_cuo">Cantidad de Cuotas</label>
        <input type="number" name="com_cat_cuo" id="com_cat_cuo" class="form-control" value="{{ old('com_cat_cuo') }}" disabled>
        @error('com_cat_cuo')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="com_plazo">Plazo de Pago</label>
        <input type="number" name="com_plazo" id="com_plazo" class="form-control" value="{{ old('com_plazo') }}" disabled>
        @error('com_plazo')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="com_total">Total:</label>
        <input type="number" name="com_total" id="com_total" class="form-control" readonly>
    </div>

    <!-- Tabla de detalles de compras -->
    <table id="detalle_compras_table" class="table table-bordered">
        <thead>
            <tr>
                <th>Artículo</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="detalle_compras_body">
            <tr class="detalle_item">
                <td>
                    <select name="detalle_compras[0][id_articulo]" class="form-control">
                        <option value="">Seleccione un Artículo</option>
                        @foreach($articulos as $articulo)
                            <option value="{{ $articulo->id_articulo }}">{{ $articulo->art_descripcion }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="detalle_compras[0][cantidad]" class="form-control" placeholder="Cantidad" oninput="calculateTotal()"></td>
                <td><input type="number" name="detalle_compras[0][precio_unit]" class="form-control" placeholder="Precio Unitario" oninput="calculateTotal()"></td>
                <td class="detalle-actions">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeDetalle(this)">Eliminar</button>
                </td>
            </tr>
        </tbody>
    </table>

    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="addDetalle()">Agregar Detalle</button>

    <!-- Botón de guardar al final de ambas secciones -->
    <button type="submit" class="btn btn-success btn-sm mt-3">Guardar Compra</button>
</form> --}}

<script>
    let detalleIndex = 1;

    function addDetalle() {
        const detalleComprasBody = document.getElementById('detalle_compras_body');
        const newRow = document.createElement('tr');
        newRow.classList.add('detalle_item');
        newRow.innerHTML = `
            <td>
                <select name="detalle_compras[${detalleIndex}][id_articulo]" class="form-control">
                    <option value="">Seleccione un Artículo</option>
                    @foreach($articulos as $articulo)
                        <option value="{{ $articulo->id_articulo }}">{{ $articulo->art_descripcion }} - ${{ $articulo->art_precio }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="detalle_compras[${detalleIndex}][cantidad]" class="form-control" placeholder="Cantidad" oninput="calculateTotal()"></td>
            <td><input type="number" name="detalle_compras[${detalleIndex}][precio_unit]" class="form-control" placeholder="Precio Unitario" oninput="calculateTotal()"></td>
            <td class="detalle-actions">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeDetalle(this)">Eliminar</button>
            </td>
        `;
        detalleComprasBody.appendChild(newRow);
        detalleIndex++;
    }

    function removeDetalle(element) {
        const row = element.closest('tr');
        row.remove();
        calculateTotal(); // Recalcular total al eliminar
    }

    function toggleCuotas() {
        const condicion = document.getElementById('com_condicion').value;
        document.getElementById('com_cat_cuo').disabled = condicion !== 'CREDITO';
        document.getElementById('com_plazo').disabled = condicion !== 'CREDITO';
    }

    function calculateTotal() {
        let total = 0;
        const detalles = document.querySelectorAll('.detalle_item');

        detalles.forEach(detalle => {
            const cantidad = detalle.querySelector('input[name*="[cantidad]"]').value;
            const precioUnit = detalle.querySelector('input[name*="[precio_unit]"]').value;
            if (cantidad && precioUnit) {
                total += parseFloat(cantidad) * parseFloat(precioUnit);
            }
        });

        document.getElementById('com_total').value = total.toFixed(2);
    }
</script> 