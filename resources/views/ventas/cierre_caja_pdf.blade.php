<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Cuentas por Cobrar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .totales {
            text-align: right;
            margin-top: 20px;
        }
        .totales h3 {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reporte de Cuentas por Cobrar</h1>
        
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Fecha de Venta</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resultado_apertura as $cuenta)
                <tr>
                    <td>{{ $cuenta->cliente }}</td> 
                    <td>{{ $cuenta->ven_fecha }}</td> 
                    <td>${{ number_format($cuenta->cob_importe, 2) }}</td> 
                @endforeach
            </tbody>
            
        </table>
        
        <div class="totales">
            <h3>Total de Cuentas por Cobrar: ${{ number_format($total_ingresos, 2) }}</h3>
        </div>
        
        <p>Este reporte refleja las cuentas por cobrar hasta la fecha {{ now()->format('d/m/Y') }}.</p>
    </div>
</body>
</html>
