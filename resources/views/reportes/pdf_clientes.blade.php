<html>

<head>
    <title>Reporte de Cliente</title>
    <style>
        @page {
            margin: 0cm 0cm;
            margin-bottom: 2cm;
        }

        body {
            margin-top: 1cm;
            margin-left: 1cm;
            margin-right: 1cm;
            margin-bottom: 1cm;
        }

        .tabla {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            border: 1px solid black;
        }

        .tabla td,
        .tabla th {
            border: 1px solid #ddd;
            padding: 2px;
        }

        .tabla tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .tabla tr:hover {
            background-color: #ddd;
        }

        .tabla th {
            padding-top: 3px;
            padding-bottom: 3px;
            /*text-align: left;*/
            background-color: #f6efef;
            color: black;
        }

        tr.was-replaced td {
            text-decoration: line-through;
        }

        th {
            font-size: 12px;
            font-weight: bold;
            padding-left: 5px;
            padding-bottom: 2px;
        }

        td {
            font-size: 12px;
            padding-left: 5px;
            padding-bottom: 2px;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="box box-primary">
        <p style="text-align: center;">
            <b>Reporte de Clientes<b>
        </p>
        <br>
        <div class="box-body">
            <table class="tabla table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nro Documento</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Sexo</th>
                        <th>Fecha Nac</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Departamento</th>
                        <th>Ciudad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente->id_cliente }}</td>
                            <td>{{ $cliente->cli_ci }}</td>
                            <td>{{ $cliente->cli_nombre }}</td>
                            <td>{{ $cliente->cli_apellido }}</td>
                            <td>{{ ($cliente->cli_sexo == 'M' ? 'MASCULINO' : 'FEMENINO') }}</td>
                            <td>{{ \Carbon\Carbon::parse($cliente->cli_fnac)->format('d/m/Y') }}</td>
                            <td>{{ $cliente->cli_direccion }}</td>
                            <td>{{ $cliente->cli_telefono }}</td>
                            <td>{{ $cliente->dep_descripcion }}</td>
                            <td>{{ $cliente->ciu_descripcion }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
