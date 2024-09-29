<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre de Caja</title>
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
        <h1>Reporte de Cierre de Caja</h1>
        
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Transacción</th>
                    <th>Tipo de Pago</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transacciones as $transaccion)
                <tr>
                    <td>{{ $transaccion->fecha }}</td>
                    <td>{{ $transaccion->descripcion }}</td>
                    <td>{{ $transaccion->forma_pago }}</td>
                    <td>${{ number_format($transaccion->monto, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="totales">
            <h3>Total de Ingresos: ${{ number_format($total_ingresos, 2) }}</h3>
        </div>

        <p>Este reporte refleja el cierre de caja correspondiente al día {{ $fecha_cierre }}.</p>
    </div>
</body>
</html>
