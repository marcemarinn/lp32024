<!DOCTYPE html>
<html>

<head>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!------ Include the above in your HEAD tag ---------->
    <style>
        /* heading */

        h1 {
            font: bold 100% sans-serif;
            letter-spacing: 0.5em;
            text-align: center;
            text-transform: uppercase;
        }

        /* table */

        table {
            font-size: 75%;
            table-layout: fixed;
            width: 100%;
        }

        table {
            border-collapse: separate;
            border-spacing: 2px;
        }

        th,
        td {
            border-width: 1px;
            padding: 0.5em;
            position: relative;
            text-align: left;
        }

        th,
        td {
            border-radius: 0.25em;
            border-style: solid;
        }

        th {
            background: #EEE;
            border-color: #BBB;
        }

        td {
            border-color: #DDD;
        }

        body {
            box-sizing: border-box;
            height: 11in;
            margin: 0 auto;
            overflow: hidden;
            padding: 0.5in;
            width: 7.5in;
        }

        body {
            background: #FFF;
            border-radius: 1px;
            box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
        }

        /* header */

        header {
            margin: 0 0 3em;
        }

        header:after {
            clear: both;
            content: "";
            display: table;
        }

        header h1 {
            background: #e40101;
            border-radius: 0.25em;
            color: #FFF;
            margin: 0 0 1em;
            padding: 0.5em 0;
        }

        header address {
            float: left;
            font-size: 95%;
            font-style: normal;
            line-height: 1.25;
            margin: 0 1em 1em 0;
        }

        article address.norm h4 {
            font-size: 125%;
            font-weight: bold;
        }

        article address.norm {
            float: left;
            font-size: 95%;
            font-style: normal;
            font-weight: normal;
            line-height: 1.25;
            margin: 0 1em 1em 0;
        }

        header address p {
            margin: 0 0 0.25em;
        }

        header span,
        header img {
            display: block;
            float: right;
        }

        header span {
            margin: 0 0 1em 1em;
            max-height: 25%;
            max-width: 60%;
            position: relative;
        }

        header img {
            max-height: 100%;
            max-width: 100%;
        }

        header input {
            cursor: pointer;
            -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
            height: 100%;
            left: 0;
            opacity: 0;
            position: absolute;
            top: 0;
            width: 100%;
        }

        /* article */

        article,
        article address,
        table.meta,
        table.inventory {
            margin: 0 0 3em;
        }

        article:after {
            clear: both;
            content: "";
            display: table;
        }

        article h1 {
            clip: rect(0 0 0 0);
            position: absolute;
        }

        article address {
            float: left;
            font-size: 125%;
            font-weight: bold;
        }

        /* table meta & balance */

        table.meta,
        table.balance {
            float: right;
            width: 36%;
        }

        table.meta:after,
        table.balance:after {
            clear: both;
            content: "";
            display: table;
        }

        /* table meta */

        table.meta th {
            width: 40%;
        }

        table.meta td {
            width: 60%;
        }

        /* table items */

        table.inventory {
            clear: both;
            width: 100%;
        }

        table.inventory th:first-child {
            width: 50px;
        }

        table.inventory th:nth-child(2) {
            width: 300px;
        }

        table.inventory th {
            font-weight: bold;
            text-align: center;
        }

        table.inventory td:nth-child(1) {
            width: 26%;
        }

        table.inventory td:nth-child(2) {
            width: 38%;
        }

        table.inventory td:nth-child(3) {
            text-align: right;
            width: 12%;
        }

        table.inventory td:nth-child(4) {
            text-align: right;
            width: 12%;
        }

        table.inventory td:nth-child(5) {
            text-align: right;
            width: 12%;
        }

        /* table balance */

        table.balance th,
        table.balance td {
            width: 50%;
        }

        table.balance td {
            text-align: right;
        }

        /* aside */

        aside h1 {
            border: none;
            border-width: 0 0 1px;
            margin: 0 0 1em;
        }

        aside h1 {
            border-color: #999;
            border-bottom-style: solid;
        }

        table.sign {
            float: left;
            width: 220px;
        }

        table.sign img {
            width: 100%;
        }

        table.sign tr td {
            border-color: transparent;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact;
            }

            html {
                background: none;
                padding: 0;
            }

            body {
                box-shadow: none;
                margin: 0;
            }

            span:empty {
                display: none;
            }

            .add,
            .cut {
                display: none;
            }
        }

        @page {
            margin: 0;
        }
    </style>
</head>

<header>
    <h1>Factura</h1>
    <address>
        <p> Empresa Utic </p>
        <p> Atyra 1750 c/Capitan Rivas </p>
        <p> 021 200 100 </p>
    </address>
    <span><img alt="it" src="https://www.utic.edu.py/v7/images/logosutic/Foto%20de%20perfil.png" height="80px"
            width="80px"></span>
</header>
<body style=" overflow-y: scroll;">
<article>
    <h1>Clientes</h1>
    <address class="norm">
        <h4>
            {{$ventas->cli_nombre . ' ' . $ventas->cli_apellido}}
        </h4>
        <p> {{$ventas->cli_direccion}} <br>
        <p> {{$ventas->cli_telefono}} <br>
    </address>

    <table class="meta">
        <tr>
            <th><span>Factura #</span></th>
            <td><span>{{$ventas->nro_factura}}</span></td>
        </tr>
        <tr>
            <th><span>Condición Venta</span></th>
            <td><span>{{$ventas->ven_condicion}}</span></td>
        </tr>
        <tr>
            <th><span>Fecha</span></th>
            <td>
                <span>
                    {{\Carbon\Carbon::parse($ventas->ven_fecha)->format('d/m/Y')}}
                </span>
            </td>
        </tr>
        <tr>
            <th><span>Monto Total</span></th>
            <td>
                <span id="prefix">Gs.</span><span>
                {{number_format($ventas->ven_total, 0, ',', '.')}}
                </span>
            </td>
        </tr>
    </table>
    <table class="inventory">
        <thead>
            <tr>
                <th><span>Código</span></th>
                <th><span>Decripcion</span></th>
                <th><span>Cantidad</span></th>
                <th><span>Precio Unitario</span></th>
                <th><span>Subtotal</span></th>
            </tr>
        </thead>
        <tbody>
            @if($detalles->count())
                @foreach ($detalles as $value)
                    <tr>
                        <td><span>{{$value->id_articulo}}</span></td>
                        <td><span>{{$value->art_descripcion}}</span></td>
                        <td>
                            <span data-prefix>Gs.</span><span>
                            {{number_format($value->det_precio_unit, 0, ',', '.')}}
                            </span>
                        </td>
                        <td><span>{{$value->det_cantidad}}</span></td>
                        <td>
                            <span data-prefix>Gs.</span>
                            <span>{{number_format($value->det_subtotal, 0, ',', '.')}}</span>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <table>
        <!-- numero a letras -->
        <tr>
            <th>Total en letras</th>
            <td colspan="3">
                <span>
                    {{ $letras }}
                </span>
            </td>
        </tr>
        
        <!-- total -->
        <tr aria-rowspan="4">
            <th><span>Total</span></th>
            <td colspan="3" style="text-align: right">
                <span data-prefix>Gs.</span>
                <span>
                    {{number_format($ventas->ven_total, 0, ',', '.')}}
                </span>
            </td>
        </tr>
    </table>
</article>
</body>
</html>
