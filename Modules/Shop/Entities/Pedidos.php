<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Shop\Entities\PedidoDetalle;

class Pedidos extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'consecutivo',
        'subtotal',
        'total',
        'id_usuario_aprobador',
        'fecha_despacho',
        'observacion',
        'id_tipo_factura',
        'numero_factura',
        'estado',
        'id_solicitud_materiales',
        'obs_general',
        'orden_compra',
        'path_oc',
        'pres_date_consum',
        'zona'
    ];

    public static function GetOrderDetailByOrderId($id_order)
    {
        return self::select(
            'pedido_detalle.*',
            'productos.nombre as name',
            \DB::raw('IF(productos.imagen IS NULL, CONCAT("'.env('API').'assets/img/sin_imagen.png"),CONCAT("'.env('API').'storage/Products/",productos.imagen)) as image'),
            \DB::raw("(SELECT '') as ext"),
            'unidades_empaque.nombre as unit',
            \DB::raw('(pedido_detalle.precio_unitario) as price'),
            \DB::raw('((pedido_detalle.precio_unitario * (pedido_detalle.impuesto/100) + pedido_detalle.precio_unitario) * pedido_detalle.cantidad) as total')
        )
        ->Join('pedido_detalle', 'pedidos.id', '=', 'pedido_detalle.id_pedido')
        ->Join('productos', 'pedido_detalle.id_producto', '=', 'productos.id')
        ->Join('unidades_empaque', 'productos.id_unidades_empaque', '=', 'unidades_empaque.id')
        ->where('pedidos.id', '=', $id_order)
        ->get();
    }
    public static function UpdateOrderByOriderId($id_order)
    {
        $order_detail = self::select('pedido_detalle.*')
        ->Join('pedido_detalle', 'pedidos.id', '=', 'pedido_detalle.id_pedido')
        ->get();

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

        $order = self::GetSumHeadOrderByOrderId($id_order);

        // CALCULAR ENCABEZADO DE LA ORDEN
        $order_head = self::find($id_order);
        if ($order_head)
        {
            $order_head->subtotal = ISSET($order->SUBTOTAL) ? $order->SUBTOTAL : 0;
            $order_head->total = ISSET($order->TOTAL) ? $order->TOTAL : 0;
            $order_head->save();
        }
    }

    public static function GetSumHeadOrderByOrderId($id_order)
    {
        if(!ISSET($id_order))
            return null;

         return self::join('pedido_detalle', 'pedidos.id', '=', 'pedido_detalle.id_pedido')
         ->selectRaw('SUM(pedido_detalle.precio_unitario * (pedido_detalle.impuesto/100) * pedido_detalle.cantidad) AS IVA')
         ->selectRaw('SUM(pedido_detalle.precio_unitario * pedido_detalle.cantidad) as SUBTOTAL')
         ->selectRaw('SUM(pedido_detalle.valor_total) as TOTAL')
         ->where('pedidos.id', $id_order)
         ->first();
    }

    public static function GetAllPedidosByPdv($id_pdv, $search="", $limit_result, $quantity_rows)
    {
        \DB::statement("SET lc_time_names = 'es_ES'");
        $pedidos = self::select(
            'pedidos.*',
            \DB::raw('DATE_FORMAT(pedidos.created_at, "%d de %M %Y") as format_date'),
            'punto_evaluacion.nombre AS pdv',
            \DB::raw("
            (CASE
                WHEN pedidos.estado = 0 THEN 'Cancelado'
                WHEN pedidos.estado = 1 THEN 'Sin confirmacion'
                WHEN pedidos.estado = 2 THEN 'Solicitado'
                WHEN pedidos.estado = 3 THEN 'Sin solicitar'
                WHEN pedidos.estado = 4 THEN 'Despachado'
                ELSE 'Sin estado'
            END) AS estado_texto
            "),
            \DB::raw("(SELECT SUM(pds.precio_unitario * (pds.impuesto/100)) FROM pedido_detalle pds WHERE pds.id_pedido = pedidos.id) as impuestos")
        )
        ->Join('usuarios', 'pedidos.id_usuario_aprobador', '=', 'usuarios.id')
        ->leftJoin('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
        ->where('punto_evaluacion.id', '=', $id_pdv)
        ->where('pedidos.orden_compra', 'LIKE', "%$search%")
        ->whereRaw('(pedidos.estado=0 OR pedidos.estado=4)');

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

    public static function GetOrdersDetailByCompanyGroup($id_company_group, $status, $limit_result, $quantity_rows, $filter=null)
    {
        \DB::statement("SET lc_time_names = 'es_ES'");
        $pedidos = self::select(
            'pedido_detalle.id',
            'pedido_detalle.id_pedido',
            'pedidos.obs_general',
            'usuarios.nombre_com AS usuario',
            'punto_evaluacion.nombre AS pdv',
            'productos.nombre AS producto',
            \DB::raw("(SELECT 'Tienda') as tipo"),
            \DB::raw("(SELECT '') as ext"),
            \DB::raw("(SELECT 0) as selected"),
            'unidades_empaque.nombre as und',
            'pedido_detalle.precio_unitario as valor_bruto',
            'pedido_detalle.impuesto as impuesto',
            'pedido_detalle.cantidad as quantity',
            \DB::raw('((pedido_detalle.precio_unitario * (pedido_detalle.impuesto/100) + pedido_detalle.precio_unitario) * pedido_detalle.cantidad) as total'),
            \DB::raw('DATE_FORMAT(pedidos.created_at, "%d de %M %Y") as format_date')
        )
        ->Join('punto_evaluacion', 'pedidos.id_punto_evaluacion', '=', 'punto_evaluacion.id')
        ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
        ->Join('usuarios', 'pedidos.id_usuario', '=', 'usuarios.id')
        ->Join('pedido_detalle', 'pedidos.id', '=', 'pedido_detalle.id_pedido')
        ->Join('productos', 'pedido_detalle.id_producto', '=', 'productos.id')
        ->Join('unidades_empaque', 'productos.id_unidades_empaque', '=', 'unidades_empaque.id')
        ->where('unidad.centro_operacion_id', '=', $id_company_group)
        ->whereRaw("(pedido_detalle.estado=$status)");

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

    public static function GetOrdersByCompanyGroup($id_company_group, $status, $limit_result, $quantity_rows, $filter=null)
    {
        $puntoEvaluacion = new \Modules\Administration\Entities\PuntoEvaluacion();
        $pdvs = $puntoEvaluacion->puntosxLider()->pluck('id')->toArray();

        \DB::statement("SET lc_time_names = 'es_ES'");
        $pedidos = self::select(
            'pedidos.*',
            'usuarios.nombre_com as usuario_approved',
            \DB::raw('DATE_FORMAT(pedidos.created_at, "%d de %M %Y") as format_date'),
            \DB::raw('DATE_FORMAT(pedidos.fecha_despacho, "%d de %M %Y") as format_date_despacho'),
            'punto_evaluacion.nombre AS PDV',
            \DB::raw("
            (CASE
                WHEN pedidos.estado = 0 THEN 'Cancelado'
                WHEN pedidos.estado = 1 THEN 'Sin confirmacion'
                WHEN pedidos.estado = 2 THEN 'Solicitado'
                WHEN pedidos.estado = 3 THEN 'Sin solicitar'
                WHEN pedidos.estado = 4 THEN 'Despachado'
                ELSE 'Sin estado'
            END) AS estado_texto
            "),
            \DB::raw("(SELECT SUM((pds.precio_unitario * pds.cantidad) * (pds.impuesto/100)) FROM pedido_detalle pds WHERE pds.id_pedido = pedidos.id) as impuestos")
        )
        ->Join('usuarios', 'pedidos.id_usuario_aprobador', '=', 'usuarios.id')
        ->leftJoin('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
        ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
        ->where('unidad.centro_operacion_id', '=', $id_company_group)
        ->whereIn('punto_evaluacion.id', $pdvs)
        ->whereRaw("(pedidos.estado=$status)")
        ->orderBy('pedidos.id', 'DESC');

        $from = $limit_result['desde'];
        $until = $limit_result['hasta'];

        if($filter != null && $filter != -1)
            $pedidos = $pedidos->where('punto_evaluacion.id', '=', $filter);

        $per_page = $pedidos->paginate($quantity_rows)->lastPage();

        $totalResults = $pedidos->count();

        if ($totalResults >= $from)
            $pedidos = $pedidos->skip($from)->take($until)->get();
        else
            $pedidos = $pedidos->get();

        foreach ($pedidos as $key_order => $order)
        {
            $order->cc_implicados = PedidoDetalle::select('punto_evaluacion.*')
            ->Join('usuarios', 'pedido_detalle.id_usuario', '=', 'usuarios.id')
            ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
            ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
            ->where('pedido_detalle.id_pedido', '=', $order->id)
            ->groupBy('punto_evaluacion.id')
            ->get();

            $order->detail = self::GetDetailByOrder($order->id);
        }

        return [
            'data' => $pedidos,
            'per_page' => $per_page
        ];
    }

    public static function GetDetailByOrder($id_order)
    {
        $pedidos = PedidoDetalle::select(
            'pedido_detalle.id',
            'pedido_detalle.id_pedido',
            'usuarios.id as id_usuario',
            'pedido_detalle.observacion',
            'usuarios.nombre_com AS usuario',
            'punto_evaluacion.nombre AS pdv',
            'productos.nombre AS producto',
            \DB::raw("(SELECT 'Tienda') as tipo"),
            \DB::raw("(SELECT '') as ext"),
            \DB::raw("(SELECT 0) as selected"),
            'unidades_empaque.nombre as und',
            'pedido_detalle.precio_unitario as valor_bruto',
            'pedido_detalle.impuesto as impuesto',
            'pedido_detalle.cantidad as quantity',
            \DB::raw('((pedido_detalle.precio_unitario * (pedido_detalle.impuesto/100) + pedido_detalle.precio_unitario) * pedido_detalle.cantidad) as total'),
            \DB::raw('DATE_FORMAT(pedido_detalle.created_at, "%d de %M %Y %h:%i:%s %p") as format_date')
        )
        ->Join('usuarios', 'pedido_detalle.id_usuario', '=', 'usuarios.id')
        ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
        ->Join('productos', 'pedido_detalle.id_producto', '=', 'productos.id')
        ->Join('unidades_empaque', 'productos.id_unidades_empaque', '=', 'unidades_empaque.id')
        ->whereRaw("pedido_detalle.id_pedido = $id_order")
        ->get();

        return $pedidos;
    }

    public static function GenCode($longitud)
    {
        $key = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZFOCOGxYDcrwEEdgF6KAF1yDrCMQcptViSYdZGK0gSC2f2L28AY8Vf6Sol3dUkTOc0D';
        $max = strlen($pattern) - 1;
        for ($i = 0; $i < $longitud; $i++) {
            $key .= $pattern[mt_rand(0, $max)];
        }
        $result = 'PE-'.$key;
        return $result;
    }

}
