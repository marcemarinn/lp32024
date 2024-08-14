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
                <button type="button" class="btn btn-primary" style="float: right" data-toggle="modal"
                    data-target="#productSearchModal">
                    Buscar Producto
                </button>
            </div>
            
            <div class="table-responsive">
                <br>
                <table class="table item-table">
                    <thead>
                        <tr>
                            <th style="width:35%;min-width:240px;">Producto</th>
                            <th class="text-center" style="width:10%;">Cantidad</th>
                            <th class="text-center">Precio Unit</th>
                            <th class="text-center">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        {{--<tr>
                            @if (!isset($venta))
                                <td colspan="6">
                                    <a href="javascript:void(0);" class='btn btn-primary btn-sm btn-add-row'>
                                        <i class="fa fa-plus"></i> Agregar
                                    </a>
                                </td>
                            @endif
                        </tr>--}}
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
