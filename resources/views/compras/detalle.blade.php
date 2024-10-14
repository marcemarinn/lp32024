<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">Detalles</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            <!-- Botón para abrir el modal -->
            <div class="col-12 col-sm-12">
                <button type="button" class="btn btn-primary" id="buscar" style="float: right" data-toggle="modal"
                    data-target="#productSearchModal">
                    Buscar Producto
                </button>
            </div>
            
            <div class="table-responsive">
                <br>
                <table class="table item-table">
                    <thead>
                        <tr>
                            <th style="width:12%;">#</th>
                            <th style="width:35%;min-width:240px;">Producto</th>
                            <th class="text-center" style="width:15%;">Cantidad</th>
                            <th class="text-center" style="width:15%;">Precio Unit</th>
                            <th class="text-center" style="width:15%;">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="selectedProducts">
                        <!-- Los productos seleccionados se agregarán aquí --> 

                        <!-- si la funcion es editar preguntamos si existe la variable $det_venta -->
                        @if (isset($det_venta))
                            @foreach ($det_venta as $value)
                            <tr>
                                <td class="text-center">
                                    <input class="form-control text-center" type="text" name="codigo[]" readonly style="text-align: center"
                                        value="{!! $value->id_articulo !!}">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="producto[]" readonly
                                        placeholder="Buscar productos" value="{!! $value->art_descripcion !!}">
                                    {!! Form::hidden('id_articulo[]', $value->id_articulo) !!}
                                    {!! Form::hidden('id_det_venta[]', $value->id_det_venta) !!}
                                </td>
                            
                                <td class="text-center">
                                    <input class="form-control text-center" type="number" min="1" name="cantidad[]" readonly
                                        style="text-align: center" value="{!! $value->det_cantidad !!}">
                                </td>
                            
                                <td class="text-center" style="width: 20%">
                                    <input class="form-control text-center" type="text" name="precio[]" readonly
                                        style="text-align: center" value="{!! number_format($value->det_precio_unit, 0, ',', '.') !!}">
                                </td>
                            
                                <td class="text-center">
                                    <input class="form-control text-center" type="text" name="subtotal[]" readonly
                                        value="{!! number_format($value->det_precio_unit * $value->det_cantidad, 0, ',', '.') !!}" style="text-align: center">
                                </td>
                            
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
