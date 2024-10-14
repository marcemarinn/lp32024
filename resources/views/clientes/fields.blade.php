<!-- Cli Nombre Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cli_nombre', 'Nombre Cliente:') !!}
    {!! Form::text('cli_nombre', null, ['class' => 'form-control', 'required' => 'required']) !!}
</div>

<!-- Cli Apellido Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cli_apellido', 'Client Apellido:') !!}
    {!! Form::text('cli_apellido', null, ['class' => 'form-control', 'required' => 'required']) !!}
</div>

<!-- Cli Ci Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cli_ci', 'Nro de CI:') !!}
    {!! Form::text('cli_ci', null, ['class' => 'form-control', 'required' => 'required']) !!}
</div>

<!-- Cli Telefono Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cli_telefono', 'Teléfono:') !!}
    {!! Form::text('cli_telefono', null, ['class' => 'form-control']) !!}
</div>

<!-- Cli Fnac Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cli_fnac', 'Fecha Nacimiento:') !!}
    {!! Form::date('cli_fnac', null, ['class' => 'form-control', 'id' => 'cli_fnac', 'required' => 'required']) !!}
</div>
<!-- Cli Sexo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cli_sexo', 'Sexo:') !!}
    {!! Form::select('cli_sexo', $genero , null, [
    'class' =>'form-control',
    'placeholder' => 'Seleccione el sexo',
    'required' => 'required',
    ]) !!}
</div>

<!-- Cli Direccion Field -->
<div class="form-group col-sm-12">
    {!! Form::label('cli_direccion', 'Dirección:') !!}
    {!! Form::text('cli_direccion', null, ['class' => 'form-control']) !!}
</div>

<!-- Id Departamento Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_departamento', 'Departamento:') !!}
    {!! Form::select('id_departamento', $departamento, null, [
    'class' => 'form-control',
    'placeholder' => 'Seleccione..',
    'id' => 'departamento_id',
    ]) !!}
</div>

<!-- Id Ciudad Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_ciudad', 'Ciudad:') !!}
    {!! Form::select('id_ciudad', $ciudad, null, [
    'class' => 'form-control',
    'placeholder' => 'Seleccione..',
    'id' => 'ciudad_id'
    ]) !!}
</div>


{{-- @push('page_scripts')
<script type="text/javascript">
    $(document).ready(function(){
            $("#departamento_id").on("change", function(e) {
                e.preventDefault();
                e.stopPropagation();

                let url  = "{{ url('buscar-ciudad') }}?departamento_id="+$("#departamento_id").val()
                fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(res => {
                    /** recuperar valores y mostrarlos */
                    // Si la respuesta es success True proceso el resultado devuelto por la funcion
                    if (res.success) {
                        console.log("datos de ciudad segun departamento", res.data);
                        // armar el select de ciudad
                        $('#ciudad_id').children('option').remove();
                        $("#ciudad_id").append('<option value="">Seleccione</option>');
                        // recorrer res.data para imprimir valores de ciudad
                        $.each(res.data,function(key, registro) {
                            $("#ciudad_id").append(`<option value='${registro.id_ciudad}'>${registro.ciu_descripcion}</option>`);
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: res.message,
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Hubo un problema al procesar la solicitud.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                });
            });
        });
</script> --}}

{{-- @endpush --}}