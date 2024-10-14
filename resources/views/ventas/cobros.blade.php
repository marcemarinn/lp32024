@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1>
                    Cobrar
                </h1>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">

    @include('sweetalert::alert')
    <div class="card">

        {!! Form::open(['route' => 'cobros.store']) !!}

        <div class="card-body">
            <!-- cabecera de ventas -->
            <div class="row">
                {!! Form::hidden('id_venta', $ventas->id_venta, ['class' => 'form-control']) !!}
                <!-- Ven Fecha Field -->
                <div class="form-group col-sm-6">
                    {!! Form::label('ven_fecha', 'Fecha Venta:') !!}
                    {!! Form::date(
                    'ven_fecha',
                    isset($ventas)
                    ? \Carbon\Carbon::parse($ventas->ven_fecha)->format('Y-m-d')
                    : \Carbon\Carbon::now()->format('Y-m-d'),
                    ['class' => 'form-control', 'id' => 'ven_fecha', 'readonly' => 'readonly'],
                    ) !!}
                </div>

                <!-- Nro Factura Field -->
                <div class="form-group col-sm-6">
                    {!! Form::label('nro_factura', 'Nro Factura:') !!}
                    {!! Form::text('nro_factura', $ventas->nro_factura,
                    ['class' => 'form-control',
                    'readonly' => 'readonly']) !!}
                </div>

                <!-- Id Cliente Field -->
                <div class="form-group col-sm-6">
                    {!! Form::label('id_cliente', 'Cliente:') !!}
                    {!! Form::text('id_cliente', $ventas->cli_nombre . ' ' . $ventas->cli_apellido, [
                    'class' => 'form-control',
                    'readonly' => 'readonly',
                    ]) !!}
                </div>

                <div class="form-group col-sm-6">
                    {!! Form::label('vtot_fac', 'Importe a Pagar:') !!}
                    {!! Form::text('vtot_fac', number_format($ventas->ven_total, 0, ',', '.'), [
                    'class' => 'form-control',
                    'readonly' => 'readonly',
                    'id' => 'vtot_fac'
                    ]) !!}
                </div>
            </div>

            <!-- DETALLE DE COBRO -->
            <div class="row">
                <table class="table listado_for_pago">
                    <thead>
                        <tr>
                            <th style="width:35%;min-width:240px;">Forma de cobro</th>
                            <th class="text-center" style="width:20%;">Importe</th>
                            <th class="text-center">Nro Voucher</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>

                    <tfoot>
                        <tr>
                            <td colspan="3">
                                <a href="javascript:void(0);"
                                    class='btn btn-primary btn-sm btn-add-row'>
                                    <i class="fa fa-plus"></i> Agregar
                                </a>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row">
                <div class="form-group col-sm-6">
                    {!! Form::label('pendiento_cobro', 'Pendiente:') !!}
                    {!! Form::text('pendiento_cobro', number_format($ventas->ven_total, 0, ',', '.') , [
                    'class' => 'form-control text-right',
                    'readonly' => 'readonly',
                    'id' => 'vtot_pend',
                    ]) !!}
                </div>

                <div class="form-group col-sm-6">
                    {!! Form::label('total_cobro', 'Total Pago:') !!}
                    {!! Form::text('total_cobro', 0, [
                    'class' => 'form-control text-right vtot_fpa',
                    'readonly' => 'readonly',
                    ]) !!}
                    <input type="hidden" id="tot_fpa" name="tot_fpa">
                </div>
            </div>
        </div>

        <div class="card-footer">
            {!! Form::submit('Pagar', ['class' => 'btn btn-primary']) !!}
            <a href="{{ route('ventas.index') }}" class="btn btn-default"> Cancelar </a>
        </div>

        {!! Form::close() !!}

    </div>

    <template tpl-cobros>
        <tr>
            <td>
                {!! Form::select('forma_pago[]', $forma_pago, null, [
                'class' => 'form-control',
                'style' => 'width: 100%',
                'id' => 'forma_pago',
                ]) !!}
                {!! Form::hidden('id_cobro[]', null) !!}
            </td>

            <td class="text-center">
                <input class="form-control text-center totalFpa"
                    type="text" min="1"
                    name="importe[]"
                    onchange="actTotalFpa(this)"
                    onkeyup="format(this);"
                    style="text-align: center">
            </td>

            <td class="text-center" style="width: 20%">
                <input class="form-control text-center"
                type="text"
                name="nro_voucher[]" style="text-align: center">
            </td>

            <td class="text-center">
                <a href="javascript:void(0);"
                class="btn btn-danger"
                title="Eliminar Fila"
                onclick="eliminarFila(this)">
                <i class="far fa-trash-alt "></i>
                </a>
            </td>
        </tr>
    </template>
</div>
@endsection

@push('page_scripts')
<script type="text/javascript">
    $(document).ready(function() {
        /** evitar submit con el boton enter **/
        $("form").keypress(function(e) {
            if (e.which == 13) {
                return false;
            }
        });

        /** funcion clic para clonar filas tr para la tabla de pagos **/
        $(".btn-add-row").click(function (e) {
            e.preventDefault();  // evitar cargar el el evento
            const $this = $(this);
            const tableRef = $this.parents(".listado_for_pago");
            // queryselector para seleccionar un elemento
            const row_pagos = document.querySelector('[tpl-cobros]')
            .content.cloneNode(true);

            // verificar que exista datos en row_pagos
            if ($(row_pagos).length > 0) {
                //agregar datos al body de la tabla con append()
                tableRef.find("tbody").append(row_pagos);
            }
            $this.removeClass('disabled');
        });
    });

        /** FUNCION PARA ELIMINAR FILA DE UNA TABLA **/
        function eliminarFila(t) {
            $(t).parents('tr').remove();
        }

        /** Funcion para calcular subtotal de forma de pagos **/
        function actTotalFpa(t) {
            var error = false;
            var totalFpa = $(t).parents("tr").find('.totalFpa').val().replace(/\./g, '');
            var totalFpa1 = $(t).parents("tr").find('.totalFpa').val();
            console.log("Valor antes del replace: ", totalFpa1);
            // definicion de variables
            var vtot = 0;
            var totfpa = 0;
            var totalFac = 0;
            // asignacion de valores a las variables
            totfpa = $(".vtot_fpa").val().replace(/\./g, '');//clase vtot_fpa
            totalFac = $("#vtot_fac").val().replace(/\./g, '');// id del elemnto

            // valicaion isNaN para validar valores undefined o nulos y que sea numerico
            if (isNaN(totalFpa)) {
                console.log("algo");
                Swal.fire({
                    title: 'Error!',
                    text: 'Ingrese un número válido',
                    icon: 'info',
                    confirmButtonText: 'Ok'
                });
                // variable auxiliar boolean
                error = true;
            } else if (t.value < 0) { // validcion que el monto sea mayor a cero
                Swal.fire({
                    title: 'Error!',
                    text: 'Ingrese un número mayor a cero',
                    icon: 'info',
                    confirmButtonText: 'Ok'
                });
                error = true;
            }

            // validacion del booleano variable error
            if (error) {
                $(t).parents("tr").find('.totalFpa').val().replace(/\./g, '');
                setTimeout(function() {
                    $(t).parents("tr").find('.totalFpa').val().replace(/\./g, '');
                }, 10);
                return;
            } else {
                $(t).parents("tr").find('.totalFpa').val(formatMoney(totalFpa));
                actTotFpa();
            }


            var total = 0;
            $('.listado_for_pago tbody tr').each(function(idx, el) {
                row = {
                    'monto': parseInt($(el).find("[name='importe[]']").val().replace(/\./g, '')),
                };
                total += row.monto;
            });
            totfpa = total;
           
            if (totfpa > totalFac) {
                 Swal.fire({
                        title: 'Error!',
                        text: 'Los totales no coinciden..!',
                        icon: 'info',
                        confirmButtonText: 'Ok'
                });

                $(t).parents("tr").find('.totalFpa').val(0).select();
                actTotFpa();
            } else {
                if (total > totalFac) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Los totales no coinciden..!',
                        icon: 'info',
                        confirmButtonText: 'Ok'
                    });
                    $(t).parents("tr").find('.totalFpa').val(0).select();
                    actTotFpa();
                } else if (total < totalFac) {} else {}
            }
        }

        function actTotFpa() {
            var total = 0,
                totfac = $("#vtot_fac").val().replace(/\./g, '');
            $('.listado_for_pago tbody tr').each(function(idx, el) {
                row = {
                    'monto': parseInt($(el).find("[name='importe[]']").val().replace(/\./g, '')),
                };
                total += row.monto;
            });
            console.log("totales por forma de pago::::", total);

            $(".vtot_fpa").val(formatMoney(total));
            $("#tot_fpa").val(total);
            $("#vtot_pend").val(formatMoney(totfac - total));
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
