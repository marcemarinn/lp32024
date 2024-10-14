<!-- Fecha de la Compra Field -->
<div class="form-group col-sm-4">
    {!! Form::label('com_fecha', 'Fecha de la Compra:') !!}
    {!! Form::date('com_fecha', \Carbon\Carbon::now()->format('Y-m-d'), [
        'class' => 'form-control',
        'id' => 'com_fecha',
        'readonly' => 'readonly',
    ]) !!}
</div>
  
 <!-- Campo de número de factura -->
 <div class="form-group">
    <label for="numero_factura">Número de Factura</label>
    <input type="text" name="numero_factura" id="numero_factura" class="form-control" value="{{ $numeroFactura }}" readonly>
</div>

<!-- Campo oculto para capturar el id de la apertura -->
<input type="hidden" name="ape_nro" value="{{ isset($caja) ? $caja->ape_nro : null }}">

<!-- Usuario Field -->
<div class="form-group col-sm-4">
    {!! Form::label('com_user_id', 'Comprador:') !!}
    {!! Form::text('com_user_id', Auth::user()->name, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
</div>



<!-- Sucursal Field -->
<div class="form-group col-sm-4">
    {!! Form::label('cod_suc', 'Sucursal:') !!}
    {!! Form::select('cod_suc', $sucursales, null, ['class' => 'form-control', 'id' => 'cod_suc']) !!}
</div>

<!-- Proveedor Field -->
<div class="form-group col-sm-6">
    {!! Form::label('prov_id', 'Proveedor:') !!}
    {!! Form::select('prov_id', $proveedores, null, ['class' => 'form-control', 'required' => 'required']) !!}
</div>

<!-- Condición de Compra Field -->
<div class="form-group col-sm-4">
    {!! Form::label('com_condicion', 'Condición de Compra:') !!}
    {!! Form::select('com_condicion', ['CONTADO' => 'CONTADO', 'CREDITO' => 'CRÉDITO'], null, [
        'class' => 'form-control',
        'onchange' => 'toggleCondicion(this)',
        'id' => 'com_condicion'
    ]) !!}
</div>

<!-- Plazo de Pago Field (Solo visible si es CREDITO) -->
<div class="form-group col-sm-4" id="div-plazo" style="display: none;">
    {!! Form::label('com_plazo', 'Plazo de Pago (en días):') !!}
    {!! Form::number('com_plazo', null, ['class' => 'form-control', 'min' => 1]) !!}
</div>

<!-- Cantidad de Cuotas Field (Solo visible si es CREDITO) -->
<div class="form-group col-sm-4" id="div-cantcuo" style="display: none;">
    {!! Form::label('com_cat_cuo', 'Cantidad de Cuotas:') !!}
    {!! Form::number('com_cat_cuo', null, ['class' => 'form-control', 'min' => 1]) !!}
</div>

<!-- Detalle de la Compra -->
<div class="form-group col-sm-12">
    <hr>
</div>

<div class="form-group col-sm-12">
    @includeIf('compras.detalle')
</div>

{{-- <!-- Total Field -->
<div class="form-group col-sm-6">
    {!! Form::label('com_total', 'Total:') !!}
    {!! Form::text('com_total', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
</div> --}}


<!-- Ven Total Field -->
<div class="form-group col-sm-6">
    {!! Form::label('com_total', 'Total:') !!}
    {!! Form::text('com_total', isset($compras) ? number_format($compra->com_total, 0, ',', '.') : null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
</div>

<input type="hidden" name="com_total_hidden" id="com_total_hidden" value="">

<!-- Campo para cambiar el estado de la compra -->
@can('approve compras')
    <div class="form-group col-sm-4">
        {!! Form::label('com_estado', 'Estado de la Compra:') !!}
        {!! Form::select('com_estado', ['Pendiente' => 'Pendiente', 'Aprobado' => 'Aprobado'], $compra->com_estado ?? 'Pendiente', ['class' => 'form-control']) !!}
    </div>
@endcan

@includeIf('compras.modal_producto')

<!-- Sección JavaScript -->
@push('page_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            console.log("Script cargado");

            $("form").keypress(function(e) {
                if (e.which == 13) {
                    return false;
                }
            });

            toggleCondicion($("#com_condicion"));

            $('.compras-form').submit(async function(e) {
                e.preventDefault();  // Previene el envío del formulario
                e.stopPropagation(); // Detiene la propagación del evento

                const form = this;
                const url = form.action;
                const method = form.method;
                const btn = form.querySelector('button');

                if (btn.classList.contains('disabled')) return false;

                btn.insertAdjacentHTML('beforeend', '<i class="fa fa-spin fa-refresh" style="margin-left:5px;"></i>');
                btn.disabled = true;

                const formData = new FormData(form);
                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData,
                    });

                    const result = await response.json();
                    if (response.ok && result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: result.message || 'Datos guardados correctamente.',
                            timer: 2000,
                            timerProgressBar: true,
                            didClose: () => {
                                window.location.href = "{{ route('compras.index') }}";
                            }
                        });
                    } else {
                        if (result.errors) {
                            let errorMessages = '';
                            for (let field in result.errors) {
                                errorMessages += `${result.errors[field].join('<br>')}<br>`;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Verificar Datos',
                                html: errorMessages,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: result.message || 'Hubo un error desconocido.',
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
                    btn.disabled = false;
                    const icon = btn.querySelector('i.fa-spin');
                    if (icon) icon.remove();
                }
            });
        });

        function toggleCondicion(obj) {
            if ($(obj).val() == 'CREDITO') {
                $("#div-cantcuo").prop('style', 'display:block;');
                $("#div-plazo").prop('style', 'display:block;');
                $("#com_cat_cuo").prop("required", true);
                $("#com_plazo").prop("required", true);
            } else {
                $("#div-cantcuo").prop('style', 'display:none;');
                $("#div-plazo").prop('style', 'display:none;');
                $("#com_cat_cuo").prop("required", false);
                $("#com_plazo").prop("required", false);
                $("#com_cat_cuo").val("");
                $("#com_plazo").val("");
            }
        }
    </script>
@endpush
