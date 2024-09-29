<!-- Apertura Cierre -->
<div class="modal fade" id="apertura" tabindex="null" role="dialog"
aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Apertura Caja </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <form method="POST" action="{{ route('apertura_cierre.store') }}" id="form-apertura">
                    @csrf
                    <div class="row">
                        <div class="form-group col-xs-12 col-md-12 col-lg-12">
                            <label>Fecha Apertura </label>
                            {!! Form::date('fecha_apertura', \Carbon\Carbon::now()->format('Y-m-d'),
                            ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-xs-12 col-md-12 col-lg-12">
                            <label>Caja </label>
                           {!! Form::select('caj_cod', $cajas, null,
                                ['class' => 'form-control',
                                'required' => 'required',
                                'placeholder' => 'Seleccione..'])
                           !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-xs-12 col-md-6 col-lg-6">
                            <label>Monto Apertura</label>
                            {!! Form::text('monto_apertura', null,
                            ['class' => 'form-control', 'onkeyup' => 'format(this)']) !!}
                        </div>
                        <div class="form-group col-xs-12 col-md-6 col-lg-6">
                            <label>Monto Cierre</label>
                            {!! Form::text('monto_cierre', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Grabar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--FIN MODAL CONFIRM-->
