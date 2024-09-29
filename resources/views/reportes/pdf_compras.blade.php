<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Compras</title>
    <style>
        /* Estilos CSS para el PDF */
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Reporte de Compras</h1>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($compras as $compra)
                <tr>
                    <td>{{ $compra->compra_id }}</td>
                    <td>{{ $compra->com_fecha }}</td>
                    <td>{{ $compra->prov_nombre }}</td>
                    <td>{{ number_format($compra->com_total, 2) }}</td>
                    <td>{{ $compra->com_estado }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>