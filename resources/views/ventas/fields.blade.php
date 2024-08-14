<!-- Ven Fecha Field -->
<div class="form-group col-sm-4">
    {!! Form::label('ven_fecha', 'Fecha:') !!}
    {!! Form::date('ven_fecha', \Carbon\Carbon::now()->format('Y-m-d'), [
        'class' => 'form-control',
        'id' => 'ven_fecha',
        'readonly' => 'readonly',
    ]) !!}
</div>

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
    {!! Form::text('nro_factura', null, ['class' => 'form-control', 'required' => 'required']) !!}
</div>

<!-- Ven Condicion Field -->
<div class="form-group col-sm-4">
    {!! Form::label('ven_condicion', 'Condición Venta:') !!}
    {!! Form::select('ven_condicion', $condicion, null, [
        'class' => 'form-control',
        'onchange' => 'condicion(this)',
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
    {!! Form::number('ven_total', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
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

            /** CONSULTAR AJAX PARA LLENAR POR DEFECTO EL MODAL AL ABRIR SE CONSULTA LA URL */
            document.getElementById('buscar').addEventListener('click', function() {
                fetch('{{ url('buscar-productos') }}?cod_suc=' + $("#cod_suc").val())
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('modalResults').innerHTML = html;
                    });
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
