<!-- Apertura Cierre Caja-->
<div class="modal fade" id="cerrar-caja" tabindex="null" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Cerrar Caja </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <form method="GET" action="" id="form-apertura">
                    @csrf
                    <input type="hidden" name="_method">
                    <div class="row">
                        <div class="form-group col-xs-12 col-md-12 col-lg-12">
                            <label>Fecha Apertura </label>
                            {!! Form::date('fecha_apertura', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'fecha']) !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-xs-12 col-md-12 col-lg-12">
                            <label>Caja </label>
                           {!! Form::select('caj_cod', $cajas, null,
                                ['class' => 'form-control',
                                'disabled' => 'disabled',
                                'id' => 'caj_cod',
                                'placeholder' => 'Seleccione..'])
                           !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-xs-12 col-md-6 col-lg-6">
                            <label>Monto Apertura</label>
                            {!! Form::text('monto_apertura', null,
                            ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'monto_apertura']) !!}
                        </div>
                        <div class="form-group col-xs-12 col-md-6 col-lg-6">
                            <label>Monto Cierre</label>
                            {!! Form::text('monto_cierre', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'monto_cierre']) !!}
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Cerrar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--FIN MODAL CONFIRM-->
