<!-- Modal -->
<div class="modal fade" id="productSearchModal" tabindex="-1" role="dialog" aria-labelledby="productSearchModalLabel"
    aria-hidden="true">
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
                        <!-- resultado de la busqueda del buscar_proudctos.blade.php -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Cambiar el cursor a una mano cuando el mouse esté sobre una fila */
    #modalResults table tr:hover {
        cursor: pointer;
        background-color: #f5f5f5; /* Cambia el color de fondo al pasar el mouse */
    }
</style>

<!-- Script para manejar la búsqueda -->
<!-- SECCION JAVASCRIPT -->
@push('page_scripts')
    <script>
        // Evento para buscar productos al teclear en el input y consultar la funcion buscar productos
        document.getElementById('productSearchQuery').addEventListener('keyup', function() {
            let query = this.value;
            fetch('{{ url('buscar-productos') }}?query=' + query + '&cod_suc=' + $("#cod_suc").val())
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalResults').innerHTML = html;
                });
        });

        function seleccionarProducto(codigo, producto, precio) {
            // Crear una nueva fila para el producto seleccionado con inputs
            let row = document.createElement('tr');

            // Agregar los inputs a la fila con los valores obtenidos de la función seleccionarProducto
            row.innerHTML = `
                <td><input type="text" name="codigo[]" class="form-control" value="${codigo}" readonly></td>
                <td><input type="text" name="producto[]" class="form-control" value="${producto}" readonly></td>
                <td><input type="number" name="cantidad[]" class="form-control text-center" value="1" min="1" oninput="calcularSubtotal(this)"></td>
                <td><input type="number" name="precio[]" class="form-control text-center" value="${formatMoney(precio)}" readonly></td>
                <td><input type="number" name="subtotal[]" class="form-control text-center" value="${formatMoney(precio)}" readonly></td>
                <td><button type="button" class="btn btn-danger" onclick="borrar(this)"><i class="far fa-trash-alt "></i></button></td>
            `;

            // Agregar la fila a la tabla de productos seleccionados
            document.getElementById('selectedProducts').appendChild(row);

            // Calcular el total general segun el detalle cargado
            total();

            // Cerrar el modal después de seleccionar el producto
            $('#productSearchModal').modal('hide');
        }

        function calcularSubtotal(element) {
            // Obtener la fila padre de este elemento (la fila del producto seleccionado)
            let row = element.closest('tr');
            // Obtener los valores de precio y cantidad y convertirlos a números
            let precio = parseFloat(row.querySelector('input[name="precio[]"]').value.replace(/\./g, ''));
            let cantidad = parseFloat(element.value);
            let subtotal = precio * cantidad;
            // Asignar el calculo al campo subtotal al nuestro detalle del producto
            row.querySelector('input[name="subtotal[]"]').value = formatMoney(subtotal);

            // Calcular el total general segun el detalle cargado al momento de aumentar la cantidad
            total();
        }

        // Funcion total para sumar todo los subtotales de lo que agrega el cliente
        function total() {
            let total = 0;
            let subtotals = document.querySelectorAll('input[name="subtotal[]"]');

            subtotals.forEach(function(subtotal) {
                // Convertir el subtotal a número y sumarlo al total general
                total += parseFloat(subtotal.value.replace(/\./g, ''));
            });

            /** actualizar mi elemento ven_total y dentro del campo imprimimos el valor calculado */
            document.getElementById('ven_total').value = formatMoney(total);
        }

        function borrar(button) {
            // Eliminar la fila del producto
            let row = button.closest('tr');
            row.remove();

            // Recalcular el total general después de eliminar un producto
            calcularSubtotal(button);
        }

        /** esta funcion nos ayudara a dar el formato a nuestros precios en javacript y colocar el separador de miles correspondientes */
        function formatMoney (n, c, d, t) {
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
