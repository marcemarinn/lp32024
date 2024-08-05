<!-- Mar Cod Field -->
<div class="form-group col-sm-6">
    {!! Form::label('mar_cod', 'Codigo:') !!}
    {!! Form::select('mar_cod', $marcas,null, ['class' => 'form-control', 'placeholder' => 'Seleccione una opcion']) !!}
</div>



<!-- Art Descripcion Field -->
<div class="form-group col-sm-6">
    {!! Form::label('art_descripcion', 'Descripcion:') !!}
    {!! Form::text('art_descripcion', null, ['class' => 'form-control']) !!}
</div>

<!-- Art Precio Field -->
<div class="form-group col-sm-6">
    {!! Form::label('art_precio', 'Precio:') !!}
    {!! Form::number('art_precio', null, ['class' => 'form-control']) !!}
</div>

<!-- Art Imagen Field -->
<div class="form-group col-sm-6">
    {!! Form::label('art_imagen', 'Imagen Producto:') !!}
    <input type="file" name="art_imagen" accept="image/*" />

    @if( isset($articulo) AND $articulo->art_imagen != '' )
        <img src="{!! asset('img/articulos/' .$articulo->art_imagen) !!}"
             alt="image" style="max-height:50px;">
    @endif
</div>


<!-- Art Iva Field -->
<div class="form-group col-sm-6">
    {!! Form::label('art_iva', 'Iva:') !!}
    {!! Form::select('art_iva', $iva,null, ['class' => 'form-control', 'placeholder' => 'Seleccione una opcion']) !!}
</div>
