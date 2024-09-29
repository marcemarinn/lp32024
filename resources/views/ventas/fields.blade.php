<!-- Ven Fecha Field -->
<div class="form-group col-sm-4">
    {!! Form::label('ven_fecha', 'Fecha:') !!}
    {!! Form::date('ven_fecha', \Carbon\Carbon::now()->format('Y-m-d'), [
        'class' => 'form-control',
        'id' => 'ven_fecha',
        'readonly' => 'readonly',
    ]) !!}


</div>

<!-- capturar el id ape_nro de apertura -->
<input type="hidden" name="ape_nro" value="{{ isset($caja) ? $caja->ape_nro : null }}">

<!-- Usu Cod Field -->
<div class="form-group col-sm-4">
    {!! Form::label('user_id', 'Vendedor:') !!}
    {!! Form::text('user_id', Auth::user()->name, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
</div>

<!-- Cod Suc Field -->
<div class="form-group col-sm-4">
    {!! Form::label('cod_suc', 'Sucursal:') !!}
    {!! Form::select('cod_suc', $sucursales, null, ['class' => 'form-control', 'id' => 'cod_suc']) !!}
</div>

<!-- Id Cliente Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_cliente', 'Cliente:') !!}
    {!! Form::select('id_cliente', $clientes, null, ['class' => 'form-control', 'required' => 'required']) !!}
</div>

<!-- Nro Factura Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nro_factura', 'Nro Factura:') !!}
    {!! Form::text('nro_factura', isset($caja)
    ? $caja->establecimiento . '-' .
    $caja->punto_expedicion . '-' .
    $caja->nro_factura : null
    , ['class' => 'form-control',
        'required' => 'required',
        'readonly' => 'readonly'
     ])
    !!}
</div>

<!-- Ven Condicion Field -->
<div class="form-group col-sm-4">
    {!! Form::label('ven_condicion', 'Condición Venta:') !!}
    {!! Form::select('ven_condicion', $condicion, null, [
        'class' => 'form-control',
        'onchange' => 'condicion(this)',
        'id' => 'ven_condicion'
    ]) !!}
</div>

<!-- Intervalo Field -->
<div class="form-group col-sm-4" id="div-intervalo" style="display: none;">
    {!! Form::label('intervalo', 'Intervalo:') !!}
    {!! Form::number('intervalo', null, ['class' => 'form-control', 'min' => 1]) !!}
</div>

<!-- Intervalo Field -->
<div class="form-group col-sm-4" id="div-cantcuo" style="display: none">
    {!! Form::label('cant_cuo', 'Cantidad Cuota:') !!}
    {!! Form::number('cant_cuo', null, ['class' => 'form-control', 'min' => 1]) !!}
</div>

<!-- DETALLE VENTA -->
<div class="form-group col-sm-12">
    <hr>
</div>

<div class="form-group col-sm-12">
    @includeIf('ventas.detalle')
</div>

<!-- Ven Total Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ven_total', 'Total:') !!}
    {!! Form::text('ven_total', isset($ventas) ? number_format($ventas->ven_total, 0, ',', '.') : null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
</div>

@includeIf('ventas.modal_producto')

<!-- SECCION JAVASCRIPT -->
@push('page_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            console.log("prueba de scritp::::::")
            /** evitar submit con el boton enter **/
            $("form").keypress(function(e) {
                if (e.which == 13) {
                    return false;
                }
            });

            // llamar funcion de condicion de ventas
            condicion($("#ven_condicion"));

            /** CONSULTAR AJAX PARA LLENAR POR DEFECTO EL MODAL AL ABRIR SE CONSULTA LA URL */
            document.getElementById('buscar').addEventListener('click', function() {
                fetch('{{ url('buscar-productos') }}?cod_suc=' + $("#cod_suc").val())
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('modalResults').innerHTML = html;
                    });
            });

            $('.ventas-form').submit(async function(e) {
                e.preventDefault();  // Previene el envío del formulario
                e.stopPropagation(); // Detiene la propagación del evento

                const form = this;
                const url = form.action;
                const method = form.method;
                const btn = form.querySelector('button');

                // Evita múltiples envíos deshabilitando el botón
                if (btn.classList.contains('disabled')) return false;

                // Añade un indicador de carga al botón
                btn.insertAdjacentHTML('beforeend', '<i class="fa fa-spin fa-refresh" style="margin-left:5px;"></i>');
                btn.disabled = true;
                // Construye los datos del formulario
                const formData = new FormData(form);
                try {
                    // Envío de la solicitud con fetch
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),// token del formulario
                            'X-Requested-With': 'XMLHttpRequest' // Este encabezado indica una solicitud AJAX
                        },
                        body: formData, // Para enviar los datos del formulario
                    });

                    // Convierte la respuesta a JSON
                    const result = await response.json();
                    // Si la respuesta es exitosa en el controlador
                    console.log("ok:::::", response.ok)
                    console.log("result:::::", result.success)

                    if (response.ok && result.success) {
                        // Muestra mensaje de éxito y redirige después de un tiempo
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: result.message || 'Datos guardados correctamente.',
                            timer: 2000,
                            timerProgressBar: true,
                            didClose: () => {
                                window.location.href = "{{ route('ventas.index') }}"; // Redirige a la pagina del index ventas si se agrego correctamente el dato
                            }
                        });

                    } else {
                        // Muestra los errores en SweetAlert2 segun los errores del validator si es que viene errors
                        if (result.errors) {
                            let errorMessages = '';
                            for (let field in result.errors) {
                                errorMessages += `${result.errors[field].join('<br>')}<br>`;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Verificar Datos',
                                html: errorMessages, // Muestra los errores formateados
                            });
                        } else {// Si no viene errors imprimir un error generico
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: result.message || 'Hubo un error desconocido.',//se imprime el mensaje enviado desde el controlador
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error en la solicitud:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Hubo un problema con la solicitud. Intenta nuevamente.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                } finally {
                    // Remueve el indicador de carga y habilita el botón
                    btn.disabled = false;
                    const icon = btn.querySelector('i.fa-spin');
                    if (icon) icon.remove();
                }
            });
        });

        function condicion(obj) {

            if ($(obj).val() == 'CREDITO') {
                $("#div-cantcuo").prop('style', 'display:block;');
                $("#cant_cuo").prop("required", true);

                $("#div-intervalo").prop('style', 'display:block;');
                $("#intervalo").prop("required", true);
            } else {
                //ocultar elemento
                $("#div-cantcuo").prop('style', 'display:none;');
                $("#div-intervalo").prop('style', 'display:none;');
                //quitar el required
                $("#cant_cuo").prop("required", false);
                $("#intervalo").prop("required", false);
                //limpiar el campo
                $("#cant_cuo").val("");
                $("#intervalo").val("");
            }
        }
    </script>
@endpush
