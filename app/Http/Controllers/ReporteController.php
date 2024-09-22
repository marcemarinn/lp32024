<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function test()
    {
        ##creamos un string con html que representa nuestro informe
        $data = '<h1>Test</h1>';
        ##creamos un objeto pdf con loadView y pasamos nuestra variable $data
        $pdf = Pdf::loadView('reportes.pdf_test', compact('data'));
        ##retornar pdf con una configuracion de pagina tipo de impresion
        ##y que se hara una descarga
        return $pdf->download('invoice.pdf');
    }

    public function rptClientes(Request $request)
    {
        ##recibir los datos enviados desde el filtro
        $input = $request->all();

        ##consultar clientes
        $clientes = DB::table('clientes')
            ->select(
                'clientes.*',
                'departamento.dep_descripcion',
                'ciudad.ciu_descripcion'
            )
        ->leftJoin('departamento', 'departamento.id_departamento',
        'clientes.id_departamento')
        ->leftJoin('ciudad', 'ciudad.id_ciudad', 'clientes.id_ciudad');

        ##SI LA VARIABLE CIUDAD ES ENVIADA CONSULTAR POR ESE DATO
        ##VALIDAR QUE EXISTA LA VARIBALE CIUDAD CON ISSET
        if (isset($input['ciudad']) && !empty($input['ciudad'])) {
            $clientes->where('clientes.id_ciudad', $input['ciudad']);
        }
        $clientes = $clientes->get();

        ##recuperar todoas las ciudades para nuestro select
        $ciudad = DB::table('ciudad')->pluck('ciu_descripcion', 'id_ciudad');

        if (isset($input['exportar']) && $input['exportar'] == 'pdf') {
            ##crear vista pdf con loadView y utilizar la misma vista reportes rpt_clientes para convertir en pdf
            $pdf = Pdf::loadView(
                'reportes.pdf_clientes',
                compact('clientes', 'ciudad')
            );

            ##retornar pdf con una configuracion de pagina tipo de impresion y que se hara una descarga
            return $pdf->download("ReporteClientes.pdf");
        }


        return view('reportes.rpt_clientes')
            ->with('clientes', $clientes)
            ->with('ciudad', $ciudad);
    }

    public function rptVentas(Request $request)
    {
        $input = $request->all();
        $desde = (isset($input['desde']) && !empty($input['desde'])) ? $input['desde'] : Carbon::now()->format('Y-m-d');
        $hasta = (isset($input['hasta']) && !empty($input['hasta'])) ? $input['hasta'] : Carbon::now()->format('Y-m-d');

        $ventas = DB::table('ventas')->select(
            'ventas.*',
            'users.name',
            'clientes.cli_nombre',
            'clientes.cli_apellido',
            'sucursal.suc_descri'
        )
            ->join('users', 'users.id', 'ventas.user_id')
            ->join('clientes', 'clientes.id_cliente', 'ventas.id_cliente')
            ->join('sucursal', 'sucursal.cod_suc', 'ventas.cod_suc')
            ->whereBetween('ventas.ven_fecha', [$desde, $hasta]);

        if (!empty($input['clientes'])) {
            $ventas = $ventas->where('ventas.id_cliente', $input['clientes']);
        }

        $ventas = $ventas->orderBy('id_venta', 'asc')->get();

        ##crear la consulta para recuperar los detalles de la venta segun los filtros
        $detalle_venta = DB::table('det_venta')->select(
            'det_venta.*',
            'articulos.art_descripcion'
        )
            ->join('articulos', 'articulos.id_articulo', 'det_venta.id_articulo')
            ->join('ventas', 'ventas.id_venta', 'det_venta.id_venta')
            ->whereBetween('ventas.ven_fecha', [$desde, $hasta]);

        if (!empty($input['cliente'])) {
            $detalle_venta = $detalle_venta->where('ventas.id_cliente', $input['cliente']);
        }

        $detalle_venta = $detalle_venta->orderBy('ventas.id_venta')->get();

        $detalle = [];##se define un array detalle
        if ($detalle_venta->count()) {
            foreach ($detalle_venta as $value) {
                $detalle[$value->id_venta][] = $value;##guardamos el array de la consulta y utilizamo como key el id_venta y creamos el nuevo array detalle
            }
        }

        if (isset($input['exportar']) && $input['exportar'] == 'pdf') {
            ##crear vista pdf con loadView y utilizar la misma vista reportes rpt_ventas para convertir en pdf
            $pdf = PDF::loadView(
                'reportes.pdf_ventas',
                compact('ventas', 'detalle')
            );

            ##retornar pdf con una configuracion de pagina tipo de impresion y que se hara una descarga
            return $pdf->download("ReporteVentas.pdf");
        }



        ##filtros cargar cliente
        $clientes = DB::table('clientes')
            ->select(DB::raw("concat(cli_nombre, ' ', cli_apellido) as cliente, id_cliente"))
            ->pluck('cliente', 'id_cliente');



        return view('reportes.rpt_ventas')
            ->with('ventas', $ventas)
            ->with('clientes', $clientes)
            ->with('detalle_venta', $detalle);
    }
}