<!DOCTYPE html>
<html lang="es">
<head>
    <title>Reporte de Compras</title>
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
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        }

        .tabla {
            border-collapse: collapse;
            width: 100%;
            border: 0px solid #ddd;
        }

        .tabla td,
        .tabla th {
            border: 0px solid #ddd;
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
            background-color: #f6efef;
            color: black;
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
            <b>Reporte de Compras</b>
        </p>
        <br>
        <div class="box-body">
            <table class="tabla">
                <thead>
                    @foreach($compras as $compra)
                        <tr>
                            <td colspan="2"><b>Fecha:</b> {{ \Carbon\Carbon::parse($compra->com_fecha)->format('d/m/Y') }}</td>
                            <td><b>Proveedor:</b> {{ $compra->prov_nombre }}</td>
                            <td><b>Estado:</b> {{ $compra->com_estado }}</td>
                        </tr>
                        <tr style="border: 1px; color:#000; background: #C5C9D3">
                            <td>#</td>
                            <td>Fecha</td>
                            <td>Proveedor</td>
                            <td>Total</td>
                            <td>Estado</td>
                        </tr>
                        <tr>
                            <td>{{ $compra->compra_id }}</td>
                            <td>{{ \Carbon\Carbon::parse($compra->com_fecha)->format('d/m/Y') }}</td>
                            <td>{{ $compra->prov_nombre }}</td>
                            <td>{{ number_format($compra->com_total, 0, ',', '.') }}</td>
                            <td>{{ $compra->com_estado }}</td>
                        </tr>
                        <tr>
                            <td colspan="5"><hr></td>
                        </tr>
                    @endforeach
                </thead>
            </table>
        </div>
    </div>
</body>
</html>