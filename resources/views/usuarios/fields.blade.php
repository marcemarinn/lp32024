<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Nombre y Apellido:') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<!-- Email Field -->
<div class="form-group col-sm-6">
    {!! Form::label('email', 'User Name:') !!}
    {!! Form::text('email', null, ['class' => 'form-control']) !!}
</div>

<!-- password Field -->
<div class="form-group col-sm-6">
    {!! Form::label('password', 'Password:') !!}
    {!! Form::password('password', ['class' => 'form-control', 
    'placeholder' => isset($usuario) ? 'Dejar Vacio si no se modifica.!' : 'Especifique contraseña']) !!}
</div>

<!-- Ci Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ci', 'Nro Ci:') !!}
    {!! Form::text('ci', null, ['class' => 'form-control']) !!}
</div>

<!-- Direccion Field -->
<div class="form-group col-sm-12">
    {!! Form::label('direccion', 'Dirección:') !!}
    {!! Form::text('direccion', null, ['class' => 'form-control']) !!}
</div>

<!-- Roles Field -->
<div class="form-group col-sm-6">
    {!! Form::label('role_id', 'Roles:') !!}
    {!! Form::select('role_id', $roles, null,
    ['class' => 'form-control']) !!}
</div>

<!-- Estado Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isactive', 'Estado:') !!}
    {!! Form::select('isactive', $isactive, (isset($usuario) ? $usuario->isactive : null), 
    ['class' => 'form-control']) !!}
</div>

<!-- Telefono Field -->
<div class="form-group col-sm-6">
    {!! Form::label('telefono', 'Telefono:') !!}
    {!! Form::text('telefono', null, ['class' => 'form-control']) !!}
</div>