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
        $data = '<h1>Test</h1>';
        $pdf = Pdf::loadView('reportes.pdf_test', compact('data'));

        return $pdf->download('invoice.pdf');
    }

    public function rptClientes(Request $request)
    {
        ##recibir los datos enviados desde el filtro
        $input = $request->all();
        #dd($input);

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

        ##recuperar todoas las ciudades para nuestro select sección filtros
        $ciudad = DB::table('ciudad')->pluck('ciu_descripcion', 'id_ciudad');

        if (isset($input['exportar']) && $input['exportar'] == 'pdf') {
            ##crear vista pdf con loadView y utilizar la misma vista reportes rpt_clientes para convertir en pdf
            $pdf = Pdf::loadView(
                'reportes.pdf_clientes',
                compact('clientes', 'ciudad')
            )
            ->setPaper('a4', 'landscape');## especificar tamaño de hoja y disposición
            # de hoja landscape=horizontal, portrait=vertical

            ##retornar pdf con una configuracion de pagina tipo de impresion y que se hara una descarga
            return $pdf->download("ReporteClientes.pdf");
        }

        # Cargar la vista rpt_clientes al iniciar el formulario
        return view('reportes.rpt_clientes')
            ->with('clientes', $clientes)
            ->with('ciudad', $ciudad);
    }

    public function rptVentas(Request $request)
    {
        ## Definiciones de parametros de busqueda
        $input = $request->all();
        $desde = (isset($input['desde']) && !empty($input['desde'])) ? $input['desde'] : Carbon::now()->format('Y-m-d');
        $hasta = (isset($input['hasta']) && !empty($input['hasta'])) ? $input['hasta'] : Carbon::now()->format('Y-m-d');

        # Seccion de consultas
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


        #select * from ventas
        #where ven_fecha between '2024-09-01' and '2024-09-16' and id_cliente =1;

        # filtro de clientes
        if (!empty($input['clientes'])) {
            $ventas = $ventas->where('ventas.id_cliente', $input['clientes']);
        }

        # traer el array completo de ventas ordenado de manera descendente.
        $ventas = $ventas->orderBy('id_venta', 'desc')->get();

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

        $detalle_venta = $detalle_venta->orderBy('ventas.id_venta', 'desc')->get();

        $detalle = [];##se define un array detalle
        if ($detalle_venta->count()) {
            foreach ($detalle_venta as $value) {
                ##guardamos el array de la consulta y utilizamo como key el id_venta y creamos el nuevo array detalle
                $detalle[$value->id_venta][] = $value;
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

    public function rptCompras(Request $request)
    {
        ## Definiciones de parámetros de búsqueda
        $input = $request->all();
        $desde = (isset($input['desde']) && !empty($input['desde'])) ? $input['desde'] : Carbon::now()->format('Y-m-d');
        $hasta = (isset($input['hasta']) && !empty($input['hasta'])) ? $input['hasta'] : Carbon::now()->format('Y-m-d');
    
        # Sección de consultas
        $compras = DB::table('compras')->select(
            'compras.*',
            'users.name',
            'proveedores.prov_nombre',
            'sucursal.suc_descri'
        )
        ->join('users', 'users.id', 'compras.com_user_id')
        ->join('proveedores', 'proveedores.prov_id', 'compras.prov_id')
        ->join('sucursal', 'sucursal.cod_suc', 'compras.cod_suc')
        ->whereBetween('compras.com_fecha', [$desde, $hasta]);
    
        # Filtro de proveedores
        if (!empty($input['proveedor'])) {
            $compras = $compras->where('compras.prov_id', $input['proveedor']);
        }
    
        # Traer el array completo de compras ordenado de manera descendente
        $compras = $compras->orderBy('compra_id', 'desc')->get();
    
        ## Crear la consulta para recuperar los detalles de la compra según los filtros
        $detalle_compra = DB::table('detalle_compras')->select(
            'detalle_compras.*',
            'articulos.art_descripcion'
        )
        ->join('articulos', 'articulos.id_articulo', 'detalle_compras.id_articulo')
        ->join('compras', 'compras.compra_id', 'detalle_compras.compra_id')
        ->whereBetween('compras.com_fecha', [$desde, $hasta]);
    
        if (!empty($input['proveedor'])) {
            $detalle_compra = $detalle_compra->where('compras.prov_id', $input['proveedor']);
        }
    
        $detalle_compra = $detalle_compra->orderBy('compras.compra_id', 'desc')->get();
    
        $detalle = []; ## Se define un array detalle
        if ($detalle_compra->count()) {
            foreach ($detalle_compra as $value) {
                ## Guardamos el array de la consulta y utilizamos como key el compra_id y creamos el nuevo array detalle
                $detalle[$value->compra_id][] = $value;
            }
        }
    
        ## Filtros para seleccionar proveedores
        $proveedores = DB::table('proveedores')
            ->pluck('prov_nombre', 'prov_id');  // MOVIDO AQUÍ
    
        if (isset($input['exportar']) && $input['exportar'] == 'pdf') {
            ## Crear vista pdf con loadView y utilizar la vista reportes.pdf_compras para convertir en pdf
            $pdf = PDF::loadView(
                'reportes.pdf_compras',
                compact('compras', 'proveedores', 'detalle')
            );
    
            ## Retornar pdf con una configuración de página tipo de impresión y que se hará una descarga
            return $pdf->download("ReporteCompras.pdf");
        }
    
        return view('reportes.rpt_compras')
            ->with('compras', $compras)
            ->with('proveedores', $proveedores)
            ->with('detalle_compra', $detalle)
            ->with('desde', $desde)
            ->with('hasta', $hasta);
    }
}    