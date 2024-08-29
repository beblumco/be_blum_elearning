<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Shop\Entities\Cg1NombreArchivo;
use Modules\Shop\Entities\Cg1DetalleCarguePedido;
use Modules\Shop\Entities\Cg1Configuracion;

class PedidoDetalle extends Model
{
    protected $table = 'pedido_detalle';

    protected $fillable = [
        'id',
        'cantidad',
        'precio_unitario',
        'impuesto',
        'valor_total',
        'observacion',
        'estado',
        'id_pedido',
        'id_producto',
        'id_usuario',
        'id_extension'
    ];

    public static function GetOrderPendingByUser($id_user)
    {
         return self::where([['pedido_detalle.id_usuario', $id_user], ['pedido_detalle.estado', 3]])->get();
    }

    public static function UpdateOrderBYIdUser($id_user, $is_admin=false)
    {

        if(!$is_admin)
        {
            $order_detail = self::select('pedido_detalle.*')
            ->where([
                ['pedido_detalle.id_pedido', '=', null],
                ['pedido_detalle.id_usuario', '=', $id_user]
            ])
            ->get();
        }
        else
        {
            $usr = \DB::table('usuarios')->find($id_user);
            $company_group = \DB::table('usuarios')
            ->select('unidad.*')
            ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
            ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
            ->where([
                ['usuarios.id', '=', $usr->id],
                ['unidad.main_account_id', '=', $usr->main_account_id]
            ])
            ->first();

            $order_detail = self::select('pedido_detalle.*')
            ->Join('usuarios', 'pedido_detalle.id_usuario', '=', 'usuarios.id')
            ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
            ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
            ->Join('productos', 'pedido_detalle.id_producto', '=', 'productos.id')
            ->where([
                ['pedido_detalle.estado', '=', 2],
                ['pedido_detalle.id_pedido', '=', null],
                ['unidad.centro_operacion_id', '=', $company_group->centro_operacion_id]
            ])
            ->get();
        }

        foreach ($order_detail as $key_detail => $value_detail)
        {
            $valor_impuesto = ($value_detail->precio_unitario * (($value_detail->impuesto == 0 ? 0 : $value_detail->impuesto / 100)));
            $valor_total = ($value_detail->precio_unitario + $valor_impuesto) * $value_detail->cantidad;

            $order = PedidoDetalle::find($value_detail->id);
            if ($order)
            {
                $order->valor_total = $valor_total;
                $order->save();
            }
        }

        // $order = self::GetSumHeadOrderByOrderId($id_order);

        // CALCULAR ENCABEZADO DE LA ORDEN
        // $order_head = self::find($id_order);
        // if ($order_head)
        // {
        //     $order_head->subtotal = ISSET($order->SUBTOTAL) ? $order->SUBTOTAL : 0;
        //     $order_head->total = ISSET($order->TOTAL) ? $order->TOTAL : 0;
        //     $order_head->save();
        // }
    }

    public static function GetSumCarAddProducts($id_user, $mode=1)
    {
        $sum = self::selectRaw('SUM(pedido_detalle.precio_unitario * (pedido_detalle.impuesto/100) * pedido_detalle.cantidad) AS IVA')
         ->selectRaw('SUM(pedido_detalle.precio_unitario * pedido_detalle.cantidad) as SUBTOTAL')
         ->selectRaw('SUM(pedido_detalle.valor_total) as TOTAL')
         ->where([
            ['pedido_detalle.id_pedido', '=', null],
            ['pedido_detalle.id_usuario', '=', $id_user]
         ]);

         if($mode==1) // EN EL CARRITO
            $sum = $sum->where('pedido_detalle.estado', 3);

        $sum = $sum->first();

        return $sum;
    }

    public static function GetSumApproveDetailOrder($id_company_group)
    {
        $puntoEvaluacion = new \Modules\Administration\Entities\PuntoEvaluacion();
        $pdvs = $puntoEvaluacion->puntosxLider()->pluck('id')->toArray();

         return self::selectRaw('SUM(pedido_detalle.precio_unitario * (pedido_detalle.impuesto/100) * pedido_detalle.cantidad) AS IVA')
         ->selectRaw('SUM(pedido_detalle.precio_unitario * pedido_detalle.cantidad) as SUBTOTAL')
         ->selectRaw('SUM(pedido_detalle.valor_total) as TOTAL')
         ->Join('usuarios', 'pedido_detalle.id_usuario', '=', 'usuarios.id')
         ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
         ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
         ->Join('productos', 'pedido_detalle.id_producto', '=', 'productos.id')
         ->where([
            ['pedido_detalle.estado', '=', 2],
            ['pedido_detalle.id_pedido', '=', null],
            ['unidad.centro_operacion_id', '=', $id_company_group]
        ])
        ->whereIn('punto_evaluacion.id', $pdvs)
        ->first();
    }

    public static function GetOrderDetailCarAddProducts($id_user)
    {
        return self::select(
            'pedido_detalle.*',
            'productos.nombre as name',
            \DB::raw('IF(productos.imagen IS NULL, CONCAT("'.env('API').'assets/img/sin_imagen.png"),CONCAT("'.env('API').'storage/Products/",productos.imagen)) as image'),
            \DB::raw("IF(pedido_detalle.id_extension IS NULL,'', extensiones.nombre) as ext"),
            'unidades_empaque.nombre as unit',
            \DB::raw('(pedido_detalle.precio_unitario) as price'),
            \DB::raw('((pedido_detalle.precio_unitario * (pedido_detalle.impuesto/100) + pedido_detalle.precio_unitario) * pedido_detalle.cantidad) as total')
        )
        ->Join('productos', 'pedido_detalle.id_producto', '=', 'productos.id')
        ->Join('unidades_empaque', 'productos.id_unidades_empaque', '=', 'unidades_empaque.id')
        ->leftJoin('extensiones', 'pedido_detalle.id_extension', '=', 'extensiones.id')
        ->where([
            ['pedido_detalle.id_pedido', '=', null],
            ['pedido_detalle.id_usuario', '=', $id_user],
            ['pedido_detalle.estado', '=', 3]
        ])
        ->get();
    }

    public static function GetOrdersDetailByCompanyGroup($id_company_group, $status, $limit_result, $quantity_rows, $filter=null, $is_admin=false, $is_history=false, $id_product = null)
    {
        $puntoEvaluacion = new \Modules\Administration\Entities\PuntoEvaluacion();
        $pdvs = $puntoEvaluacion->puntosxLider()->pluck('id')->toArray();

        \DB::statement("SET lc_time_names = 'es_ES'");
        $pedidos = self::select(
            'pedido_detalle.id',
            'pedido_detalle.id_pedido',
            'usuarios.id as id_usuario',
            'pedido_detalle.observacion',
            'usuarios.nombre_com AS usuario',
            'punto_evaluacion.nombre AS pdv',
            'productos.nombre AS producto',
            \DB::raw("(SELECT 'Tienda') as tipo"),
            \DB::raw("(SELECT 0) as selected"),
            'unidades_empaque.nombre as und',
            'pedido_detalle.precio_unitario as valor_bruto',
            'pedido_detalle.impuesto as impuesto',
            'pedido_detalle.cantidad as quantity',
            \DB::raw("pedido_detalle.precio_unitario * (pedido_detalle.impuesto/100) impuesto_calc"),
            \DB::raw('((pedido_detalle.precio_unitario * (pedido_detalle.impuesto/100) + pedido_detalle.precio_unitario) * pedido_detalle.cantidad) as total'),
            \DB::raw('DATE_FORMAT(pedido_detalle.created_at, "%d de %M %Y %h:%i:%s %p") as format_date'),
            \DB::raw("IF(pedido_detalle.id_extension IS NULL,'', extensiones.nombre) as ext"),
            'pedidos.orden_compra'
        )
        ->Join('usuarios', 'pedido_detalle.id_usuario', '=', 'usuarios.id')
        ->leftJoin('extensiones', 'pedido_detalle.id_extension', '=', 'extensiones.id')
        ->leftJoin('pedidos', 'pedido_detalle.id_pedido', '=', 'pedidos.id');
        if($is_admin)
            $pedidos = $pedidos->Join('savk_lideres_centro_de_costos', 'usuarios.id', '=', 'savk_lideres_centro_de_costos.id_usuario')
            ->Join('punto_evaluacion', 'savk_lideres_centro_de_costos.id_centro_de_costo', '=', 'punto_evaluacion.id');
        else
            $pedidos = $pedidos->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id');

        $pedidos = $pedidos->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
        ->Join('productos', 'pedido_detalle.id_producto', '=', 'productos.id')
        ->Join('unidades_empaque', 'productos.id_unidades_empaque', '=', 'unidades_empaque.id')
        ->where('unidad.centro_operacion_id', '=', $id_company_group)
        ->whereIn('punto_evaluacion.id', $pdvs);

        if(!$is_history)
            $pedidos = $pedidos->whereRaw("(pedido_detalle.estado=$status)")
            ->orderBy("pedido_detalle.id","DESC");
        else
        {
            $pedidos = $pedidos->where("pedidos.estado", "=", $status)
            ->where("pedido_detalle.id_producto", "=", $id_product)
            ->orderBy("pedido_detalle.id","DESC");
        }

        if($filter != null && $filter != -1)
            $pedidos = $pedidos->where('punto_evaluacion.id', '=', $filter);

        $from = $limit_result['desde'];
        $until = $limit_result['hasta'];

        $per_page = $pedidos->paginate($quantity_rows)->lastPage();

        $totalResults = $pedidos->count();

        if ($totalResults >= $from)
            $pedidos = $pedidos->skip($from)->take($until)->get();
        else
            $pedidos = $pedidos->get();

        return [
            'data' => $pedidos,
            'per_page' => $per_page
        ];
    }


    public static function GetDataOrdersConslPrepareBottyData($id_order)
    {
        $ejecucion = Cg1Configuracion::where("id","=",1)->first()->activo;
        $resultado = self::from('pedido_detalle as pd')
        ->select(
                'pe.consecutivo',
                'un.nit',
                \DB::raw('("00") as cc'), // QUEMADA PERO NO SE NECESITA
                \DB::raw('DATE_FORMAT(pe.created_at, "%Y%m%d") as fecha'),
                \DB::raw('bd.nombre as bodega_local'),
                'pr.referencia as referencia',
                \DB::raw('("") as ext'), // QUEMADA (DEBE AGREGARSE AUNQue NO SE USE) PUEDE TENER VARIAS
                \DB::raw('DATE_FORMAT(pe.created_at, "%Y%m%d") as fecha2'), //LA MISMA FECHA, ASI ESTABA EN EL PORTAL
                'ue.nombre as unidad',
                'pr.nombre as producto',
                'pd.cantidad',
                'pd.precio_unitario as valorUnitario',
                'pd.id AS id_consolidado',
                'pr.descripcion as descripcion',
                'pe.orden_compra AS oc',
                'pe.consecutivo AS old_oc',
                'pev.id as depto_id'
                )
        ->join('pedidos as pe', 'pd.id_pedido', '=', 'pe.id')
        ->join('productos as pr', 'pd.id_producto', '=', 'pr.id')
        ->leftJoin('bodegas as bd', 'pr.id_bodega', '=', 'bd.id')
        ->join('unidades_empaque as ue', 'pr.id_unidades_empaque', '=', 'ue.id')
        ->join('categorias_productos as cp', 'pr.id_categoria', '=', 'cp.id')
        ->Join('usuarios as us', 'pd.id_usuario', '=', 'us.id')
        ->Join('punto_evaluacion as pev', 'us.id_punto', '=', 'pev.id')
        ->Join('unidad as un', 'pev.unidad_id', '=', 'un.id')
        ->where('pd.id_pedido', $id_order)
        ->get();

        $array = [];
        $ocPedido ='';
        $depto = '';
        foreach ($resultado as $key_pr => $value_pr)
        {
                $fecha = $value_pr->fecha;

                $depto_id = $value_pr->depto_id;

                if ($value_pr->depto_id != $depto)
                    $ocPedido = Pedidos::GenCode(6);
                else
                    $ocPedido = $ocPedido;

                $array[$value_pr->consecutivo][] = ([
                    'consecutivo' => $value_pr->consecutivo,
                    'nit' => $value_pr->nit,
                    'cc' => $value_pr->cc,
                    'fecha' => $fecha,
                    'bodega_local' => $value_pr->bodega_local,
                    'referencia' => $value_pr->referencia,
                    'ext' => $value_pr->ext,
                    'fecha2' => $fecha,
                    'unidad' => $value_pr->unidad,
                    'cantidad' => $value_pr->cantidad,
                    'valorUnitario' => $value_pr->valorUnitario,
                    'id_consolidado' => $value_pr->id_consolidado,
                    'descripcion' => $value_pr->descripcion,
                    'oc' => $ocPedido,
                    'old_oc' => $value_pr->oc,
                    'depto_id'=>$depto_id
                ]);

                $depto = $value_pr->depto_id;
        }

        $respuestaArray = [];
        $i=1;
        foreach ($array as $key => $value)
        {
            // if ($i <= 9)
            // {
            $consecutivo = $key;

            $archivo = Cg1NombreArchivo::selectRaw('IFNULL(MAX(CAST(unidad AS UNSIGNED)), 0) as unidad')->first();

            // Verifica si se encontraron registros
            $cantidadRegistros = $archivo ? 1 : 0;
            $unidad = 1;
            if($archivo != NULL)
            {
                if ($archivo->unidad == 0)
                    $unidad = 1;
                else
                    $unidad = $archivo->unidad+1;
            }

            $nombre_archivo = 'PEDIDOSS.PE';
            $archivoCompleto = $nombre_archivo . $unidad;
            $estado =1;
            $nit  = $array[$consecutivo][0]['nit'];
            $consecutivo = $array[$consecutivo][0]['consecutivo'];
            $oc = $array[$consecutivo][0]['oc'];
            $old_oc = $array[$consecutivo][0]['old_oc'];
            $fecha = $array[$consecutivo][0]['fecha'];

            $nombreArchivo = new Cg1NombreArchivo();
            $nombreArchivo->nombre_pedido = $archivoCompleto;
            $nombreArchivo->cliente = $nit;
            $nombreArchivo->consecutivo = $consecutivo;
            $nombreArchivo->oc = $old_oc;
            $nombreArchivo->fecha_solicitud = $fecha;
            $nombreArchivo->estado = $estado;
            $nombreArchivo->unidad = $unidad;
            $nombreArchivo->save();

            $lastId = $nombreArchivo->id;

            foreach ($array[$key] as $keyConsecutivo => $valueConsecutivo)
            {
                $idCon = $valueConsecutivo['id_consolidado'];
                $referencia = $valueConsecutivo['referencia'];
                $valorUnitario = $valueConsecutivo['valorUnitario'];

                $carguePedido = new Cg1DetalleCarguePedido();
                $carguePedido->producto_referencia = $referencia;
                $carguePedido->valor = $valorUnitario;
                $carguePedido->nombre_archivo_id = $lastId;
                $carguePedido->oc = $old_oc;
                $carguePedido->save();
            }

            //actualiza el estado de la tabla pedido consolidado indicando que el archivo ya se descargo
            Pedidos::where('id', $idCon)->update(['estado' => 5]);

                $respuestaArray[$archivoCompleto] = $array[$key];
            // }

            $i++;
        }

        return [
            'arreglo' => $respuestaArray,
            'ejecucion' => $ejecucion
        ];
    }

    public static function GetDataCreateTrazapp($id_order)
    {
        $resultados = self::from('pedido_detalle as pd')
        ->select(
            'pe.id as PDV_ID',
            'pe.nombre as PDV_NAME',
            'ped.obs_general AS comentario_general',
            'ped.*',
            'pe.*',
            'pd.id_usuario AS ID_USUARIO'
        )
        ->join('usuarios as us', 'pd.id_usuario', '=', 'us.id')
        ->join('punto_evaluacion as pe', 'us.id_punto', '=', 'pe.id')
        ->join('pedidos as ped', 'pd.id_pedido', '=', 'ped.id')
        ->where('pd.id_pedido', $id_order)
        ->get();

        $array_order = [];

        foreach ($resultados as $key_pd => $value_pd)
        {
            $value_pd->PRODUCTOS = self::from('pedido_detalle as pd')
            ->select('pr.nombre as producto',
                    'pr.referencia as codigo',
                    'pr.descripcion as descripcion',
                    'pd.cantidad',
                    'pd.precio_unitario',
                    'pd.impuesto',
                    'pd.valor_total',
                    'cp.nombre',
                    'cp.id as categoria_id',
                    'pr.id as producto_id',
                    'pd.*')
            ->join('pedidos as pe', 'pd.id_pedido', '=', 'pe.id')
            ->join('usuarios as us', 'pd.id_usuario', '=', 'us.id')
            ->join('punto_evaluacion as pev', 'us.id_punto', '=', 'pev.id')
            ->join('productos as pr', 'pd.id_producto', '=', 'pr.id')
            ->join('categorias_productos as cp', 'pr.id_categoria', '=', 'cp.id')
            ->where([
                ['pev.id', $value_pd->PDV_ID],
                ['pe.id', $id_order]
            ])
            ->get();

            $array_order[$value_pd->PDV_ID][] = $value_pd;
    }

        return $array_order;
    }
}
