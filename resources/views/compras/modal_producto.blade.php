<!-- Modal -->
<div class="modal fade" id="productSearchModal" tabindex="-1" role="dialog" aria-labelledby="productSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productSearchModalLabel">Buscar Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulario de búsqueda -->
                <input type="text" id="productSearchQuery" class="form-control" placeholder="Buscar...">
                <br>
                <!-- Aquí se mostrará la tabla con los resultados -->
                <div id="modalResults">
                    <!-- resultado de la búsqueda -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Cambiar el cursor a una mano cuando el mouse esté sobre una fila */
    #modalResults table tr:hover {
        cursor: pointer;
        background-color: #f5f5f5;
    }
</style>

<!-- SECCIÓN JAVASCRIPT -->
@push('page_scripts')
<script>
    // Evento para buscar productos al teclear en el input
    document.getElementById('productSearchQuery').addEventListener('keyup', function() {
        let query = this.value;
        fetch('{{ url('buscar-productos') }}?query=' + query + '&cod_suc=' + $("#cod_suc").val())
            .then(response => response.text())
            .then(html => {
                document.getElementById('modalResults').innerHTML = html;
            });
    });

    function seleccionarProducto(codigo, producto, precio) {
        let filas = document.getElementById('selectedProducts').getElementsByTagName('tr');

        // Verificar si el producto ya ha sido agregado
        for (let i = 0; i < filas.length; i++) {
            let codigoExistente = filas[i].querySelector('input[name="codigo[]"]').value;
            if (codigoExistente === codigo) {
                alert('El producto ya fue agregado.');
                return;
            }
        }

        // Crear una nueva fila con inputs
        let row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="codigo[]" class="form-control" value="${codigo}" readonly></td>
            <td><input type="text" name="producto[]" class="form-control" value="${producto}" readonly></td>
            <td><input type="number" name="cantidad[]" class="form-control text-center" value="1" min="1" oninput="calcularSubtotal(this)"></td>
            <td><input type="number" name="precio[]" class="form-control text-center" value="${precio}" min="0" step="any" oninput="calcularSubtotal(this)"></td>
            <td><input type="text" name="subtotal[]" class="form-control text-center" value="${formatMoney(precio, 0)}" readonly></td>
            <td><button type="button" class="btn btn-danger" onclick="borrar(this)"><i class="far fa-trash-alt"></i></button></td>
        `;
        document.getElementById('selectedProducts').appendChild(row);

        // Calcular el total general
        total();
        $('#productSearchModal').modal('hide');
    }

    function calcularSubtotal(element) {
        let row = element.closest('tr');
        let precio = parseFloat(row.querySelector('input[name="precio[]"]').value);
        let cantidad = parseFloat(row.querySelector('input[name="cantidad[]"]').value);
        let subtotal = precio * cantidad;
        row.querySelector('input[name="subtotal[]"]').value = formatMoney(subtotal);

        total();
    }

    function total() {
        let total = 0;
        let subtotals = document.querySelectorAll('input[name="subtotal[]"]');
        subtotals.forEach(function(subtotal) {
            total += parseFloat(subtotal.value.replace(/\./g, ''));
        });
        document.getElementById('com_total').value = formatMoney(total);
    }

    function borrar(button) {
        let row = button.closest('tr');
        row.remove();
        total();
    }

    function formatMoney(n, c, d, t) {
        let s, i, j;
        c = isNaN(c = Math.abs(c)) ? 0 : c;
        d = d === undefined ? "," : d;
        t = t === undefined ? "." : t;
        s = n < 0 ? "-" : "";
        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c)));
        j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) +
            (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    }
</script>
@endpush
