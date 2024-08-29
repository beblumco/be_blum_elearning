<?php

namespace Modules\Shop\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PuntoEvaluacion;
use App\Models\Unidad;
use Carbon\Carbon;
use Modules\Shop\Entities\PreciosClientes;
use Modules\Shop\Entities\Productos;
use Modules\Shop\Entities\Pedidos;
use Modules\Shop\Entities\PedidoDetalle;
use Modules\Shop\Entities\PresPdvPresupuesto;
use Modules\Shop\Entities\PresMeses;
use Modules\Shop\Entities\PresAnos;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Modules\Shop\Entities\Cg1Configuracion;
use Modules\Shop\Entities\Cg1NombreArchivo;
use Modules\Shop\Entities\Cg1DetalleCarguePedido;
use Modules\Shop\Entities\TappTrazapp;
use Modules\Shop\Entities\TappTramite;
use Modules\Shop\Entities\TappPedidoDetalle;
use Illuminate\Support\Collection;

class ShopController extends Controller
{
    public function __construct()
    {
        $exceptRoutes = [
            'UpdateStateBottyBD', 'GetOrderNames', 'UpdateOrderStatus', 'ActivateBottyByIdOrder'
        ];

        $this->middleware('auth')->except($exceptRoutes);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $page_title = 'Catálogo';
        $action = 'shop_module';
        $permisos = $this->GetAllPermisos();
        return view('shop::index', compact('page_title', 'action', 'permisos'));
    }

    public function GetDataInit(Request $request)
    {

        $user = auth()->user();
        $products = [];
        $company_group = \DB::table('usuarios')
            ->select('unidad.*')
            ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
            ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
            ->where([
                ['usuarios.id', '=', $user->id],
                ['unidad.main_account_id', '=', $user->main_account_id]
            ])
            ->first();

        if ($company_group != NULL)
            $products = PreciosClientes::GetProductsWithPricesByCompany($company_group->id);

        // $order = Pedidos::where([
        //     ['id_punto_evaluacion', '=', $user->id_punto],
        //     ['estado', '=', 3]
        // ])->first();

        // $order_pending = [];
        // if ($order != NULL)
        //     $order_pending = Pedidos::GetOrderDetailByOrderId($order->id);

        $order_pending = PedidoDetalle::GetOrderPendingByUser($user->id);
        return response()->json([
            'success' => 1,
            'responseCode' => 202,
            'message' => 'Datos encontrados',
            'data' => [
                'products' => $products,
                'order_pending' => $order_pending
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function details($id_encrypt, $opc = NULL)
    {
        $page_title = 'Catálogo';
        $action = 'shop_module';
        $id_product = Crypt::decryptString($id_encrypt);
        if ($opc == NULL)
            $opc = 0;

        $permisos = $this->GetAllPermisos();
        return view('shop::details_product', compact('page_title', 'action', 'id_product', 'opc', 'permisos'));
    }

    public function GetDataProductDetail(Request $request)
    {
        $id_product = intval($request->get('id_product'));
        $user = auth()->user();

        $company_group = \DB::table('usuarios')
            ->select('unidad.*')
            ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
            ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
            ->where([
                ['usuarios.id', '=', $user->id],
                ['unidad.main_account_id', '=', $user->main_account_id]
            ])
            ->first();

        $quantity_rows = 50;
        $current_page = 1;
        $limit_result = $this->PaginationCalculate($current_page, $quantity_rows);

        $product = PreciosClientes::GetProductByCompanyGroupAndIdProduct($company_group->centro_operacion_id, $id_product, $limit_result, $quantity_rows);

        return response()->json([
            'success' => 1,
            'responseCode' => 202,
            'message' => 'Producto encontrado.',
            'data' => $product
        ]);
    }

    public function DownloadDocProduct(Request $request)
    {
        $url = $request->get('url');
        $rta = $this->GetPdfExternalDocument($url);

        return $rta;
    }

    function GetPdfExternalDocument($url)
    {
        // Obtener el contenido del archivo utilizando cURL
        $response = Http::get($url);

        // Verificar si la solicitud fue exitosa (código de estado 200)
        if ($response->successful()) {
            // Obtener el contenido del cuerpo de la respuesta
            $contenido = $response->body();

            // Retornar una respuesta con el contenido del PDF
            return response($contenido, 200)->header('Content-Type', 'application/pdf');
        }

        // Si la solicitud no fue exitosa, puedes manejarlo según tus necesidades
        return response()->json(['error' => 'No se pudo obtener el documento'], 500);
    }

    public function AddProductToCar(Request $request)
    {
        $id_product = $request->get('id_product');
        $quantity = $request->get('quantity');
        $ext = ($request->get('ext') == 'null' ? NULL : $request->get('ext'));
        $user = auth()->user();

        //VALIDAR SI TIENE PUNTOS A CARGO
        $has = PuntoEvaluacion::ToValidateAssignPDVs($user->id);
        if(!$has)
        {
            return response()->json([
                'success' => 1,
                'responseCode' => 400,
                'message' => 'No puedes agregar productos, porque no eres responsable de almenos 1 punto de evaluación.',
                'data' => null
            ]);
        }

        $company_group = \DB::table('usuarios')
            ->select('unidad.*', 'punto_evaluacion.id as id_punto')
            ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
            ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
            ->where([
                ['usuarios.id', '=', $user->id],
                ['unidad.main_account_id', '=', $user->main_account_id]
            ])
            ->first();
        //VALIDAR SI EXISTE ALGÚN  PEDIDO SIN SOLICITAR
        // $order = Pedidos::where([
        //     ['id_punto_evaluacion', '=', $user->id_punto],
        //     ['estado', '=', 3]
        // ])->first();
        $order = PedidoDetalle::select('pedido_detalle.*')
        ->where([
            ['pedido_detalle.id_pedido', '=', null],
            ['pedido_detalle.id_usuario', '=', $user->id]
        ])
        ->get();

        $quantity_rows = 50;
        $current_page = 1;
        $limit_result = $this->PaginationCalculate($current_page, $quantity_rows);
        $product = PreciosClientes::GetProductByCompanyGroupAndIdProduct($company_group->centro_operacion_id, $id_product, $limit_result, $quantity_rows);

        if ($order) // EXISTE YA UN PEDIDO INICIADO SIN SER SOLICITADO
        {
            //VALIDAR SI EL PRODUCTO EXISTE PARA INSERTARLO O AGREGAR UNA CANTIDAD MAS EN CASO DE EXISTENCIA
            $detail = PedidoDetalle::where([
                ['id_producto', '=', $id_product],
                ['id_usuario', '=', $user->id],
                ['id_pedido', '=', null],
                ['estado', '=', 3]
            ]);

            if($ext != NULL)
                $detail = $detail->where('id_extension', '=', $ext);

            $detail = $detail->first();

            // Verifica si se encontró el modelo
            if ($detail)
            {
                $detail->cantidad = $detail->cantidad + $quantity;
                $detail->save();
            }
            else
            {
                $detail_product = new PedidoDetalle();
                $detail_product->fill([
                    'cantidad' => $quantity,
                    'precio_unitario' => $product->price,
                    'impuesto' => $product->impuesto,
                    'valor_total' => 0,
                    'estado' => 3, // DETALLE SIN SOLICITAR
                    // 'id_pedido' => $newOrder->id,
                    'id_producto' => $id_product,
                    'id_usuario' => $user->id,
                    'id_extension' => $ext
                ]);

                $detail_product->save();
            }

            // $id_order = $order->id;
        }
        else // SI NO EXISTE PEDIDO, SE CREA
        {
            // $consecutivo_recent = Pedidos::where([
            //     ['estado', '=', 2]
            // ])
            //     ->orderBy('id', 'DESC')
            //     ->first();

            // if (!$consecutivo_recent)
            //     $id_consecutivo = 1;
            // else {
            //     $arr_cons = explode("-", $consecutivo_recent->consecutivo);
            //     $id_consecutivo = (int)$arr_cons[1] + 1;
            // }

            // $consecutivo = "PE" . str_pad($company_group->id, 2, "0", STR_PAD_LEFT) . "-" . str_pad($id_consecutivo, 4, "0", STR_PAD_LEFT);
            // $newOrder = new Pedidos();
            // $newOrder->fill([
            //     'consecutivo' => $consecutivo,
            //     'subtotal' => 0,
            //     'total' => 0,
            //     'estado' => 3,
            //     'id_punto_evaluacion' => $company_group->id_punto,
            //     'id_usuario' => auth()->user()->id
            // ]);

            // if ($newOrder->save())
            // {
                $detail_product = new PedidoDetalle();
                $detail_product->fill([
                    'cantidad' => $quantity,
                    'precio_unitario' => $product->price,
                    'impuesto' => $product->impuesto,
                    'valor_total' => 0,
                    'estado' => 3, // DETALLE SIN SOLICITAR
                    // 'id_pedido' => $newOrder->id,
                    'id_producto' => $id_product,
                    'id_usuario' => $user->id,
                    'id_extension' => $ext
                ]);

                $detail_product->save();
            // }

            // $id_order = $newOrder->id;
        }

        // Pedidos::UpdateOrderByOriderId($id_order);

        // $order = Pedidos::GetOrderDetailByOrderId($id_order);
        PedidoDetalle::UpdateOrderBYIdUser($user->id);
        $order = PedidoDetalle::GetOrderPendingByUser($user->id);
        return response()->json([
            'success' => 1,
            'responseCode' => 200,
            'message' => 'Pedido agregado a la orden correctamente.',
            'data' => $order
        ]);
    }

    function UpdateOrderByIdOrder($id_order)
    {
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('shop::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('shop::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }


    function IndexShoppingCar()
    {
        $page_title = 'Carro de compras';
        $action = 'shopping_car';
        $permisos = $this->GetAllPermisos();
        return view('shop::index_shpping_car', compact('page_title', 'action', 'permisos'));
    }

    public function GetDataInitCar(Request $request)
    {
        $user = auth()->user();
        $company_group = \DB::table('usuarios')
            ->select('unidad.*', 'punto_evaluacion.id as id_punto')
            ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
            ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
            ->where([
                ['usuarios.id', '=', $user->id],
                ['unidad.main_account_id', '=', $user->main_account_id]
            ])
            ->first();

        //VALIDAR SI EXISTE ALGÚN  PEDIDO SIN SOLICITAR
        // $order = Pedidos::where([
        //     ['id_punto_evaluacion', '=', $user->id_punto],
        //     ['estado', '=', 3]
        // ])->first();

        // $order_data = [];
        // if ($order != null)
        //     $order_data = Pedidos::GetOrderDetailByOrderId($order->id);

        $order_data = PedidoDetalle::GetOrderDetailCarAddProducts($user->id);

        $data_presupuesto = $this->GetBudget();

        $presupuesto = $data_presupuesto->presupuesto;
        $consumo_pres = $data_presupuesto->consumo_pres;
        $consumido = $data_presupuesto->consumido;
        $month = $data_presupuesto->month;

        return response()->json([
            'success' => 1,
            'responseCode' => 202,
            'message' => 'Datos encontrados.',
            'data' => [
                'detail' => $order_data,
                'totales' => PedidoDetalle::GetSumCarAddProducts($user->id),
                'presupuesto' => $presupuesto,
                'consumido' => $consumido,
                'month' => $this->GetMonthByDay($month)
            ]
        ]);
    }

    function GetBudget($id_pdv = null)
    {
        $user = auth()->user();
        if($id_pdv == null)
            $id_pdv = $user->id_punto;

        $presupuesto = PresPdvPresupuesto::where([
            ['id_pdv', $id_pdv],
            ['id_mes', ltrim(Carbon::now()->month, 0)],
            ['id_ano', PresAnos::where('descripcion', Carbon::now()->year)->pluck('id')->first()]
        ])
        ->select('presupuesto', 'dia_corte')
        ->first();

        $dia_corte = ISSET($presupuesto) ? $presupuesto->dia_corte : NULL;
        $presupuesto = ISSET($presupuesto) ? $presupuesto->presupuesto : NULL;
        $consumo_pres = false;

        if($dia_corte != null) // DE NUEVO SE CONSULTA PERO EL PRESUPUESTO UN MES SUPERIOR
        {
            $cal_day = $dia_corte-Carbon::now()->day;
            // $date_arm = Carbon::parse(Carbon::now()->year."-".Carbon::now()->month."-".$dia_corte)->format("Y-m-d");
            if($cal_day <= 0) // EL PRESUPUESTO SE CAMBIA AL MES SIGUIENTE PORQUE HOY ES MAYOR AL DIA DE CORTE
            {
                $presupuesto = PresPdvPresupuesto::where([
                    ['id_pdv', $id_pdv],
                    ['id_mes', ltrim(Carbon::now()->addMonth()->month, 0)],
                    ['id_ano', PresAnos::where('descripcion', Carbon::now()->year)->pluck('id')->first()]
                ])
                ->pluck('presupuesto')
                ->first();

                $date_init = Carbon::now()->addMonth()->firstOfMonth();
                $date_end = Carbon::now()->addMonth()->endOfMonth();

                $consumo_pres = true;
            }
            else
            {
                $consumo_pres = false;
                $date_init = Carbon::parse(Carbon::now()->year."-".Carbon::now()->subMonth()->month."-".$dia_corte)->format("Y-m-d");
                $date_end = Carbon::parse(Carbon::now()->year."-".Carbon::now()->month."-".$dia_corte)->format("Y-m-d");
            }
        }
        else
        {
            $consumo_pres = false;
            $date_init = Carbon::now()->firstOfMonth();
            $date_end = Carbon::now()->endOfMonth();
        }

        if($consumo_pres) // VALIDAR EL CONSUMO POR LA FECHA DEL PRESUPUESTO CONSUMIDA
        {
            $consumido = \DB::table(\DB::raw("(
                SELECT pedidos.total AS consumido
                FROM pedidos
                JOIN pedido_detalle ON pedidos.id = pedido_detalle.id_pedido
                JOIN savk_lideres_centro_de_costos ON pedido_detalle.id_usuario = savk_lideres_centro_de_costos.id_usuario
                WHERE savk_lideres_centro_de_costos.id_centro_de_costo =$id_pdv 
                AND pedido_detalle.estado = 1
                AND (pedidos.estado = 2 OR pedidos.estado = 4)
                AND pedidos.pres_date_consum BETWEEN '$date_init' AND '$date_end'
                GROUP BY pedidos.id
                UNION
                SELECT SUM(pedido_detalle.valor_total) AS consumido
                FROM pedido_detalle
                JOIN savk_lideres_centro_de_costos ON pedido_detalle.id_usuario = savk_lideres_centro_de_costos.id_usuario
                WHERE savk_lideres_centro_de_costos.id_centro_de_costo =$id_pdv 
                AND pedido_detalle.estado = 2
            ) as con"))
            ->sum('consumido');
        }
        else
        {
            $consumido = \DB::table(\DB::raw("(
                SELECT pedidos.total AS consumido
                FROM pedidos
                JOIN pedido_detalle ON pedidos.id = pedido_detalle.id_pedido
                JOIN savk_lideres_centro_de_costos ON pedido_detalle.id_usuario = savk_lideres_centro_de_costos.id_usuario
                WHERE savk_lideres_centro_de_costos.id_centro_de_costo =$id_pdv 
                AND pedido_detalle.estado = 1
                AND (pedidos.estado = 2 OR pedidos.estado = 4)
                AND pedidos.created_at BETWEEN '$date_init' AND '$date_end'
                GROUP BY pedidos.id
                UNION
                SELECT SUM(pedido_detalle.valor_total) AS consumido
                FROM pedido_detalle
                JOIN savk_lideres_centro_de_costos ON pedido_detalle.id_usuario = savk_lideres_centro_de_costos.id_usuario
                WHERE savk_lideres_centro_de_costos.id_centro_de_costo =$id_pdv 
                AND pedido_detalle.estado = 2
            ) as con"))
            ->sum('consumido');
        }

        return (Object)[
            'consumo_pres' => $consumo_pres,
            'presupuesto' => $presupuesto,
            'consumido' => $consumido,
            'month' => Carbon::parse($date_end)->month
        ];
    }

    public function SaveQuantityDetailProduct(Request $request)
    {
        $id_detail = $request->get('id_detail');
        $quantity = $request->get('quantity');
        $user = auth()->user();
        $detail = PedidoDetalle::where('id', '=', $id_detail)->first();

        PedidoDetalle::where('id', '=', $id_detail)
            ->update(["cantidad" => $quantity]);

        PedidoDetalle::UpdateOrderBYIdUser($user->id);
        $order_data = PedidoDetalle::GetOrderDetailCarAddProducts($user->id);

        $data_presupuesto = $this->GetBudget();

        $presupuesto = $data_presupuesto->presupuesto;
        $consumido = $data_presupuesto->consumido;
        // Pedidos::UpdateOrderByOriderId($detail->id_pedido);

        // $order_data = Pedidos::GetOrderDetailByOrderId($detail->id_pedido);

        return response()->json([
            'success' => 1,
            'responseCode' => 200,
            'message' => 'Actualización exitosa.',
            'data' => [
                'detail' => $order_data,
                'totales' => PedidoDetalle::GetSumCarAddProducts($user->id),
                'presupuesto' => $presupuesto,
                'consumido' => $consumido
            ]
        ]);
    }

    public function DeleteDetailProduct(Request $request)
    {
        $user = auth()->user();

        $id_detail = $request->get('id_detail');

        $detail = PedidoDetalle::where('id', '=', $id_detail)->first();

        PedidoDetalle::where('id', '=', $id_detail)->delete();

        // Pedidos::UpdateOrderByOriderId($detail->id_pedido);

        // $order_data = Pedidos::GetOrderDetailByOrderId($detail->id_pedido);

        PedidoDetalle::UpdateOrderBYIdUser($user->id);
        $order_data = PedidoDetalle::GetOrderDetailCarAddProducts($user->id);

        $data_presupuesto = $this->GetBudget();

        $presupuesto = $data_presupuesto->presupuesto;
        $consumido = $data_presupuesto->consumido;

        return response()->json([
            'success' => 1,
            'responseCode' => 200,
            'message' => 'Elimiación exitosa.',
            'data' => [
                'detail' => $order_data,
                'totales' => PedidoDetalle::GetSumCarAddProducts($user->id),
                'presupuesto' => $presupuesto,
                'consumido' => $consumido
            ]
        ]);
    }

    public function ClearCarOrder(Request $request)
    {
        $user = auth()->user();

        $delete_detail = PedidoDetalle::where([
            ["id_usuario", "=", $user->id],
            ["estado", "=", 3]
        ])->delete();

        if ($delete_detail)
        {
            return response()->json([
                'success' => 1,
                'responseCode' => 200,
                'message' => 'El carrito se ha vaciado exitosamente.',
                'data' => []
            ]);
        }
        else
        {
            return response()->json([
                'success' => 1,
                'responseCode' => 200,
                'message' => 'No se pudo vaciar el carrito de compras, comunícate con el administrador.',
                'data' => []
            ]);
        }
    }

    function ToActivateBotty($id_order)
    {
        $data_order_cg1 = PedidoDetalle::GetDataOrdersConslPrepareBottyData($id_order);
        //DATA PARA TRAZAPP EN KLAXEN AUDEED

        $data_order_trazapp = PedidoDetalle::GetDataCreateTrazapp($id_order);
        foreach ($data_order_trazapp as $key_tr => $value_tr)
        {
            $this->CreateTrazapp($value_tr);
        }
        // dd($data_order_trazapp);

        // $data = [
        //     'encriptado' => 'SI',
        //     'cabecera_solicitud_materiales' => [
        //         'tercero_nit' => $direccionPdv['empresa_nit'],
        //         'empresa_id' => $direccionPdv['empresa_id'],
        //         'nom_empresa' => $direccionPdv['nom_empresa'],
        //         'punto_eval_codigo' => $direccionPdv['pdv_codigo'],
        //         'direccion' => $direccion,
        //        'observacion' => $comentario,
        //         'usuario_codigo' => 'portalklaxen123',
        //         'costo' => $subTotal, //$valorTotal,
        //         'tapp_tipo_tramite_id' => 1,
        //         'fecha_limite' => $fechaInicial,
        //         'tapp_tipo_pago' => 1,
        //         'consecutivo_portal' => $direccionPdv['oc'],
        //         'grupo_empresa' => $direccionPdv['grupo_empresa'],
        //         'gempresa_codigo' => $direccionPdv['gempresa_codigo'],
        //         'gempresa_id' => $direccionPdv['gempresa_id'],
        //         'departamento_id' => $direccionPdv['id'],
        //         'departamento_ciudad' => $direccionPdv['dept_ciudad'],
        //         'nom_departamento' => $direccionPdv['nom_departamento'],
        //         'dept_contacto' => $direccionPdv['dept_contacto'],
        //     ],
        //     'productos' => $pedidos_consolidados
        // ];

        //HAGO EL CURL A KLAXEN AUDEED PARA REGISTRAR EL TRAZAPP
        // $res = $this->crearTramiteKlaxenAudeed($data);

        //REGISTRO LOS MATERIALES
        //Obtengo la configuracion de la configuracion para el grupo empresa
        // $productos = $model->obtenerProductosPorIdConsolidado($id_consolidado);
        // $config_datos = $model->obtenerConfigGrupoEmpresa($direccionPdv['gempresa_id']);

        /**
         * Antes de enviar la solicitud de creación, debo validar que el PDV que hizo la solicitud pertenezca
         * al grupo empresa que tiene habilitado el negocio.
         */
        // $solicitud_material_id = $this->crearSolicitudMateriales($productos, $config_datos, $data);
        // if($solicitud_material_id != 0 || $solicitud_material_id != '0')
        //     $model->actualizarPedidoConsolidado($solicitud_material_id, $id_consolidado);

        //CURL PARA BOTY
        if(env("BOTTY") == 1)
        {
            $data = array(
                'key' => "n9ED7Iwxs1P7wdIWNbVskIcvOPrHLZh8lY84UENsmPw=",
                'data' => $data_order_cg1
            );

            $ch = curl_init();
            $url = ENV("IP_BOTTY");
            curl_setopt($ch, CURLOPT_URL,"$url/generar_csv_savk/generarCsv.php");
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $respuesta = curl_exec ($ch);
            if($respuesta === false)
            {
                $error = curl_error($ch);
                // Manejar el error, ya sea mostrándolo, registrándolo o tomando alguna otra acción
                dd($error);
            }
            else
            {
                $answer = json_decode($respuesta);
                \Log::info(http_build_query($data));
            }
            curl_close ($ch);
        }
    }

    function CreateTrazapp($data_pdv)
    {
        $msjs_correo = [];
        $usuario_codigo = "portalklaxen123";

        try
        {
        //   if(isset($d['encriptado']))
        //   {
            // $puntoEval = PuntoEvaluacion::where('codigo', '=', $d['cabecera_solicitud_materiales']['punto_eval_codigo'])->first();
            // $tercero = $this->unidad->where('nit', '=', $d['cabecera_solicitud_materiales']['tercero_nit'])->first();
            // $usuario_portal_id = $this->usuarios->where('codigo', '=', $usuario_codigo)->first();

            // if(!is_null($usuario_portal_id))
            // {
            //   $usuario_portal_id = $usuario_portal_id->id;
            // }

            // if($puntoEval == null)
            // {
            //   $punto_origen = $d['cabecera_solicitud_materiales']['punto_eval_codigo'];
            //   $puntoEval = $this->puntoEvaluacion->where('nombre', '=', 'Sin Punto')->first();
            //   $msjs_correo[] = "No se encontro el pdv $punto_origen en Klaxen Audeed.";
            // }

            // if($tercero == null)
            // {
            //   $tercero_origen = $d['cabecera_solicitud_materiales']['tercero_nit'];
            //   $tercero = $this->unidad->where('nit', '=', '1000000000')->first();
            //   $msjs_correo[] = "No se encontrol el tercero $tercero_origen en Klaxen Audeed.";
            // }
            $data_pdv = $data_pdv[0];
            //Creo el tramite
            $new_tramite = new TappTramite();
            $new_tramite->fill([
              'tercero_id' => $data_pdv->unidad_id,
              'tapp_tipo_tramite_id' => 1,
              'direccion' => (ISSET($data_pdv->direccion) ? $data_pdv->direccion : "Sin dirección"),
              'observacion' => $data_pdv->comentario_general,
              'fecha_limite' => date("Y-m-d H:i:s"),
              'tapp_tipo_pago' => 1,
              'usuario_id' => $data_pdv->ID_USUARIO,
              'costo' => $data_pdv->subtotal,
              'punto_evaluacion_id' => $data_pdv->PDV_ID,
              'consecutivo_portal' => $data_pdv->orden_compra
            ]);

            if($new_tramite->save())
            {
              foreach($data_pdv->PRODUCTOS as $key_it => $item)
              {
                $new_products_detail = new TappPedidoDetalle();
                $new_products_detail->fill([
                  'nombre' => $item->producto,
                  'valor_unitario' => $item->precio_unitario,
                  'descripcion' => $item->descripcion,
                  'valor_total' => $item->valor_total,
                  'cod_referencia' => $item->codigo,
                  'cantidad' => $item->cantidad,
                  'impuesto' => $item->impuesto,
                  'tapp_tramite_id' => $new_tramite->id
                ]);
                $new_products_detail->save();
              }
            }

            // if(count($msjs_correo) > 0)
            // {
            //   try
            //   {
            //     Mail::to(['desarrollotic@klaxen.com.co', 'tic@klaxen.com.co'])
            //       ->send(new TrazappApiTramites($msjs_correo, $d['cabecera_solicitud_materiales']['consecutivo_portal']));
            //   }
            //   catch( \Exception $e)
            //   {
            //     Log::error(strval($e));
            //   }
            // }

            return true;

        //   }
        //   else
        //   {
        //     return response()->json(['estado' => 204, 'msg' => 'La data envíada es invalida.']);
        //   }

        }
        catch(Exception $e)
        {
            return false;
        //   Log::error(strval($e));
        //   Mail::to(['desarrollotic@klaxen.com.co', 'tic@klaxen.com.co'])
        //     ->send(new TrazappApiTramites(["Se ha presentado un error interno en la aplicación. \n" . strval($e)],
        //       $d['cabecera_solicitud_materiales']['consecutivo_portal']));
        }
      }

    public function ActivateBottyByIdOrder(Request $request)
    {
        $id_order = $request->id_order;
        if(!ISSET($id_order))
        {
            return response()->json([
                'success' => 1,
                'responseCode' => 400,
                'message' => 'Es obligatorio enviar el id del pedido.',
                'data' => []
            ]);
        }

        $order_done = PedidoDetalle::select(
            'pedido_detalle.*',
            'pd.orden_compra'
        )
        ->Join('pedidos as pd', 'pedido_detalle.id_pedido', '=', 'pd.id')
        ->where('pedido_detalle.id_pedido', '=', $id_order)
        ->first();
        if($order_done)
        {
            // PedidoDetalle::where('id_pedido', '=', $id_order)->update(['id_pedido' => NULL, 'estado' => 3]); # SE QUITA EL PEDIDO QUE TENIA Y CAMBIA DE ESTADO COMO SI ESTUVIERA EN EL CARRITO
            Cg1DetalleCarguePedido::where('oc', '=', $order_done->orden_compra)->delete(); # SE ELIMINA EL CARGUE EN CG1
            Cg1NombreArchivo::where('oc', '=', $order_done->orden_compra)->delete(); # SE ELIMINA EL ARCHIVO DEL CARGUE EN CG1
            Cg1Configuracion::where('id', '=', 1)->update(['activo' => 0]); #ACTUALIZAR ESTADO DE BOTTY
            // Pedidos::where('id', '=', $id_order)->delete(); # SE ELIMINA EL ENCABEZADO DEL PEDIDO
            $this->ToActivateBotty($id_order); #SE VUELVA A GENERAR TODO EL PROCESO DEL PEDIDO
        }


        return response()->json([
            'success' => 1,
            'responseCode' => 200,
            'message' => 'Pedido generado nuevamente.',
            'data' => []
        ]);
    }

    public function SaveObsPedido(Request $request)
    {
        $mode = $request->get('mode');
        $obs = $request->get('obs');
        $pdf = $request->file('pdf');

        $user = auth()->user();
        $new_status = ($mode == 1 ? 1 : 2);
        $message = ($mode == 1 ? "El pedido fue enviado para su aprobación" : "El pedido ha pasado al área de facturación");


        $update = [
            "estado" => $new_status,
            "obs_general" => ($obs == "" ? NULL : $obs)
        ];

        if ($new_status == 2) //PEDIDO ENVIADO PARA FACTURACIÓN
        {
            // $update['fecha_solicitud'] = Carbon::now();
            $company_group = \DB::table('usuarios')
            ->select('unidad.*', 'punto_evaluacion.id as id_punto')
            ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
            ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
            ->where([
                ['usuarios.id', '=', $user->id],
                ['unidad.main_account_id', '=', $user->main_account_id]
            ])
            ->first();

            $consecutivo_recent = Pedidos::where([
                ['estado', '=', 2]
            ])
            ->orderBy('id', 'DESC')
            ->first();

            if (!$consecutivo_recent)
                $id_consecutivo = 1;
            else
            {
                $arr_cons = explode("-", $consecutivo_recent->consecutivo);
                $id_consecutivo = (int)$arr_cons[1] + 1;
            }

            $presupuesto = PresPdvPresupuesto::where([
                ['id_pdv', $user->id_punto],
                ['id_mes', ltrim(Carbon::now()->month, 0)],
                ['id_ano', PresAnos::where('descripcion', Carbon::now()->year)->pluck('id')->first()]
            ])
            ->select('presupuesto', 'dia_corte')
            ->first();

            $dia_corte = ISSET($presupuesto) ? $presupuesto->dia_corte : NULL;
            if($dia_corte != null) // DE NUEVO SE CONSULTA PERO EL PRESUPUESTO UN MES SUPERIOR
            {
                $cal_day = $dia_corte-Carbon::now()->day;
                // $date_arm = Carbon::parse(Carbon::now()->year."-".Carbon::now()->month."-".$dia_corte)->format("Y-m-d");
                if($cal_day <= 0) // EL PRESUPUESTO SE CAMBIA AL MES SIGUIENTE PORQUE HOY ES MAYOR AL DIA DE CORTE
                    $consumo_pres = true;
                else
                    $consumo_pres = false;
            }
            else
                $consumo_pres = false;

            //---------VALIDAR ZONA A LA QUE PERTENECE POR MEDIO DEL PUNTO AL QUE PERTENECE EL USUARIO QUE APRUEBA
            $cc = PuntoEvaluacion::find($user->id_punto);

            //ids de ciudades
            $RUTA_LOCAL = [150, 1087];
            $RUTA_VIAJERA = [
                639, 295, 157, 715, 441,
                127, 1011, 30986, 370, 382, 889, 341, 682
            ];

            if (in_array($cc->ciudad_id, $RUTA_LOCAL)) {
                $zona = 1; //'RUTA LOCAL'
            } elseif (in_array($cc->ciudad_id, $RUTA_VIAJERA)) {
                $zona = 2; //'RUTA VIAJERA'
            } else {
                $zona = 3; //'RUTA TRANSPORTADORA'
            }
            //---------END VALIDAR ZONA

            $consecutivo = "PE" . str_pad($company_group->id, 2, "0", STR_PAD_LEFT) . "-" . str_pad($id_consecutivo, 4, "0", STR_PAD_LEFT);
            $oc = Pedidos::GenCode(6);
            $newOrder = new Pedidos();
            $newOrder->fill([
                'consecutivo' => $consecutivo,
                'subtotal' => 0,
                'total' => 0,
                "estado" => $new_status,
                "zona" => $zona,
                "obs_general" => ($obs == "" ? NULL : $obs),
                "id_usuario_aprobador" => $user->id,
                "orden_compra" => $oc,
                "pres_date_consum" => ($consumo_pres) ? Carbon::now()->addMonth()->format("Y-m-d H:i:s") : NULL
            ]);

            if ($newOrder->save())
            {
                // AGREGAR OC SI EXISTE
                if ($pdf != null)
                {
                    $id_content = $newOrder->id;
                    $current_content = Pedidos::where('id', '=', $id_content)->first();

                    $pathVerified = storage_path('app/public/Ocs') . $current_content->path_oc;
                    if (\File::exists($pathVerified))
                        \File::delete($pathVerified);

                    $path = $this->FunctionSaveDocument($pdf, 'Ocs');
                    Pedidos::where('id', '=', $id_content)->update(['path_oc' => $path]);
                }

                $update_detail = [
                    "pedido_detalle.estado" => 1,
                    "pedido_detalle.id_pedido" => $newOrder->id
                ];

                $order_detail = PedidoDetalle::where([
                    ['pedido_detalle.id_pedido', '=', null],
                    ['pedido_detalle.id_usuario', '=', $user->id]
                ])->update($update_detail); // CAMBIA A

                Pedidos::UpdateOrderByOriderId($newOrder->id);

                $this->ToActivateBotty($newOrder->id);
            }
        }
        else
        {
            $update_detail = [
                "pedido_detalle.estado" => 2,
                "pedido_detalle.observacion" => ($obs == "" ? NULL : $obs)
            ];

            $order_detail = PedidoDetalle::where([
                ['pedido_detalle.id_pedido', '=', null],
                ['pedido_detalle.id_usuario', '=', $user->id],
                ['pedido_detalle.estado', '!=', 0]
            ])->update($update_detail); // CAMBIA A
        }

        $ano_actual = PresAnos::where('descripcion', Carbon::now()->year)->pluck('id')->first();
        $mes_actual = ltrim(Carbon::now()->month, 0);

        $data = $this->GetPressByCompanyGroupDate($mes_actual, $ano_actual);
        return response()->json([
            'success' => 1,
            'responseCode' => 200,
            'message' => $message,
            'data' => [
                'detail' => [],
                'totales' => null,
                'presupuesto' => $data
            ]
        ]);
    }

    function GetPressByCompanyGroupDate($mes_actual, $ano_actual)
    {
        $user = auth()->user();
        $company_group = \DB::table('usuarios')
            ->select('unidad.*')
            ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
            ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
            ->where([
                ['usuarios.id', '=', $user->id],
                ['unidad.main_account_id', '=', $user->main_account_id]
            ])
            ->first();

        $company_group_id = $company_group->centro_operacion_id;

        $puntos = PuntoEvaluacion::select('punto_evaluacion.id as id')
            ->join("unidad", "punto_evaluacion.unidad_id", "=", "unidad.id")
            ->join("centro_operacion", "unidad.centro_operacion_id", "=", "centro_operacion.id")
            ->where('centro_operacion.id', '=', $company_group_id)
            ->get();

        $press = PresPdvPresupuesto::select(
            'punto_evaluacion.id',
            'punto_evaluacion.nombre',
            'pres_pdv_presupuesto.presupuesto',
            'pres_pdv_presupuesto.dia_corte'
        )
            ->join('punto_evaluacion', 'punto_evaluacion.id', 'pres_pdv_presupuesto.id_pdv')
            ->whereIn('id_pdv', $puntos->pluck('id'))
            ->where('pres_pdv_presupuesto.id_mes', $mes_actual)
            ->where('pres_pdv_presupuesto.id_ano', $ano_actual)
            ->get();

        $totalPresuPuntos = 0;
        foreach ($press as $key => $value)
        {
            // TIENE DIA DE CORTE?
            if($value->dia_corte!=NULL)
            {
                $date_start = Carbon::create(PresAnos::where('id', $ano_actual)->pluck('descripcion')->first(), $mes_actual, $value->dia_corte)->subMonth()->startOfDay();
                $date_end = Carbon::create(PresAnos::where('id', $ano_actual)->pluck('descripcion')->first(), $mes_actual, $value->dia_corte)->endOfDay();
                // $date_start = Carbon::create(PresAnos::where('id', $ano_actual)->pluck('descripcion')->first(), $mes_actual, 1)->firstOfMonth();
                // $date_end = Carbon::create(PresAnos::where('id', $ano_actual)->pluck('descripcion')->first(), $mes_actual, 1)->endOfMonth();
            }
            else
            {
                $date_start = Carbon::create(PresAnos::where('id', $ano_actual)->pluck('descripcion')->first(), $mes_actual, 1)->firstOfMonth();
                $date_end = Carbon::create(PresAnos::where('id', $ano_actual)->pluck('descripcion')->first(), $mes_actual, 1)->endOfMonth();
            }

            $value->consumido = Pedidos::Join('pedido_detalle','pedidos.id', '=', 'pedido_detalle.id_pedido')
            ->Join('savk_lideres_centro_de_costos', 'pedido_detalle.id_usuario', '=', 'savk_lideres_centro_de_costos.id_usuario')
            ->where([
                ['savk_lideres_centro_de_costos.id_centro_de_costo', $value->id],
            ])
            ->whereRaw("(pedidos.estado = 2 OR pedidos.estado = 4)")
            ->whereBetween('pedidos.created_at', [$date_start, $date_end])
            ->sum('pedido_detalle.valor_total');

            $value->porcentaje = $value->presupuesto != 0 ? round(($value->consumido /  $value->presupuesto) * 100, 2) : 0;

            $totalPresuPuntos += ($value->presupuesto - $value->consumido);
        }

        return (Object)[
            "totalPresuPuntos" => $totalPresuPuntos,
            "press" => $press
        ];
    }
    public function GetDataInitAssign(Request $request)
    {
        $mode = $request->get('mode');

        $user = auth()->user();

        $company_group = \DB::table('usuarios')
            ->select('unidad.*')
            ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
            ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
            ->where([
                ['usuarios.id', '=', $user->id],
                ['unidad.main_account_id', '=', $user->main_account_id]
            ])
            ->first();

        if ($request->year != "") {
            $ano_actual = $request->year;
        } else {
            $ano_actual = PresAnos::where('descripcion', Carbon::now()->year)->pluck('id')->first();
        }

        if ($request->month != "") {
            $mes_actual = $request->month;
        } else {
            $mes_actual = ltrim(Carbon::now()->month, 0);
        }



        switch ($mode) {
            case 1:  // PDV's

                $months = PresMeses::GetMonths();
                $years = PresAnos::GetYears();

                $data = $this->GetPressByCompanyGroupDate($mes_actual, $ano_actual);

                return response()->json([
                    'success' => 1,
                    'responseCode' => 202,
                    'message' => "Data found.",
                    'data' => [
                        'months' => $months,
                        'years' => $years,
                        'press' => $data->press,
                        'totalPresuPuntos' => $data->totalPresuPuntos
                    ]
                ]);
                break;

            case 2: // ASSIGN
                $pdvs = PresPdvPresupuesto::GetPdvsAssign($company_group->centro_operacion_id);

                return response()->json([
                    'success' => 1,
                    'responseCode' => 202,
                    'message' => "Data found.",
                    'data' => $pdvs
                ]);
                break;

            case 3:

                $puntos = PuntoEvaluacion::select('punto_evaluacion.id as id', 'punto_evaluacion.nombre')
                    ->join("unidad", "punto_evaluacion.unidad_id", "=", "unidad.id")
                    ->join("centro_operacion", "unidad.centro_operacion_id", "=", "centro_operacion.id")
                    ->where('centro_operacion.id', '=', $company_group->centro_operacion_id)
                    ->get();

                $meses = PresMeses::where('estado', 1)->get();

                $press = [];

                foreach ($puntos as $key => $p) {
                    $presupuesto = [];
                    foreach ($meses as $key => $m) {
                        $presupuesto[$m->id] = PresPdvPresupuesto::where([
                            ['id_pdv', $p->id],
                            ['id_mes', $m->id],
                            ['id_ano', $ano_actual]
                        ])->first();
                    }
                    $p->presupuesto = $presupuesto;
                }

                return response()->json([
                    'success' => 1,
                    'responseCode' => 202,
                    'message' => "Data found.",
                    'data' => $puntos
                ]);
                break;

            default:
                break;
        }
    }

    public function SaveAssign(Request $request)
    {
        $user = auth()->user();
        $value_pres = (!ISSET($request->value) ? 0 : $request->value);
        $day_end = ($request->day == "" ? NULL : $request->day);
        $ano_actual = PresAnos::where('descripcion', Carbon::now()->year)->pluck('id')->first();
        $mes_actual = ltrim(Carbon::now()->month, 0);

        if(!ISSET($request->value)) //SI NO ENVIA PRESUPUESTO, SE ASUME QUE SE QUITA PRESUPUESTO DEL PUNTO Y DE ESE MES Y AŃO
        {
            $presupuesto = PresPdvPresupuesto::where([
                ['id_pdv', $request->id_pdv],
                ['id_ano', $request->year],
                ['id_mes', $request->month]
            ])->delete();

            $data = $this->GetPressByCompanyGroupDate($mes_actual, $ano_actual);

            return response()->json([
                'success' => 1,
                'responseCode' => 202,
                'message' => "Presupuesto creado correctamente.",
                'data' => $data
            ]);
        }
        else
        {
            $presupuesto = PresPdvPresupuesto::where([
                ['id_pdv', $request->id_pdv],
                ['id_ano', $request->year],
                ['id_mes', $request->month]
            ]);

            if ($presupuesto->count() > 0)
            {
                $presupuesto->update([
                    'presupuesto' => $value_pres,
                    'dia_corte' => $day_end
                ]);

                if ($request->all_month)
                {
                    $meses_presupuestados =  PresPdvPresupuesto::select('id_mes')->where([
                        ['id_pdv', $request->id_pdv],
                        ['id_ano', $request->year],
                    ])
                    ->get()
                    ->pluck('id_mes');

                    $meses_sin_presupuestar = PresMeses::select('pres_meses.id')
                    ->whereNotIn('id', $meses_presupuestados)
                    ->get()
                    ->pluck('id');

                    foreach ($meses_sin_presupuestar as $key => $value)
                    {
                        PresPdvPresupuesto::create([
                            'id_pdv' => $request->id_pdv,
                            'id_ano' => $request->year,
                            'id_mes' => $value,
                            'presupuesto' => $value_pres,
                            'dia_corte' => $day_end
                        ]);
                    }
                }
            }
            else
            {
                if ($request->all_month)
                {
                    $meses_presupuestados =  PresPdvPresupuesto::select('id_mes')->where([
                        ['id_pdv', $request->id_pdv],
                        ['id_ano', $request->year],
                    ])
                        ->get()
                        ->pluck('id_mes');

                    $meses_sin_presupuestar = PresMeses::select('pres_meses.id')
                        ->whereNotIn('id', $meses_presupuestados)
                        ->get()
                        ->pluck('id');

                    foreach ($meses_sin_presupuestar as $key => $value)
                    {
                        PresPdvPresupuesto::create([
                            'id_pdv' => $request->id_pdv,
                            'id_ano' => $request->year,
                            'id_mes' => $value,
                            'presupuesto' => $value_pres,
                            'dia_corte' => $day_end
                        ]);
                    }
                } else
                {
                    PresPdvPresupuesto::create([
                        'id_pdv' => $request->id_pdv,
                        'id_ano' => $request->year,
                        'id_mes' => $request->month,
                        'presupuesto' => $value_pres,
                        'dia_corte' => $day_end
                    ]);
                }
            }

            $data = $this->GetPressByCompanyGroupDate($mes_actual, $ano_actual);

            return response()->json([
                'success' => 1,
                'responseCode' => 202,
                'message' => "Presupuesto creado correctamente.",
                'data' => $data
            ]);
        }

    }

    function getAllYears(Request $request)
    {
        $years = PresAnos::GetYears();

        return response()->json([
            'success' => 1,
            'responseCode' => 202,
            'message' => "Data encontrada.",
            'data' => $years
        ]);
    }

    public function IndexHistorical()
    {
        $page_title = 'Historial';
        $permisos = $this->GetAllPermisos();
        $action = __FUNCTION__;

        return view('shop::index_historical', compact('page_title', 'action', 'permisos'));
    }

    public function GetDataInitHistorical(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        $current_page = $request->get('current_page');

        $quantity_rows = 10;
        $limit_result = $this->PaginationCalculate($current_page, $quantity_rows);

        $company_group = \DB::table('usuarios')
            ->select('unidad.*')
            ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
            ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
            ->where([
                ['usuarios.id', '=', $user->id],
                ['unidad.main_account_id', '=', $user->main_account_id]
            ])
            ->first();

            if($user->id_grupo == 44)
                $orders = Pedidos::GetOrdersByCompanyGroup($company_group->centro_operacion_id, 4, $limit_result, $quantity_rows);
            else
                $orders = Pedidos::GetAllPedidosByPdv($user->id_punto, $search, $limit_result, $quantity_rows);
        return response()->json([
            'success' => 1,
            'responseCode' => 202,
            'message' => "Data encontrada.",
            'data' => $orders
        ]);
    }

    public function GetDetailOrder(Request $request)
    {
        \DB::statement("SET lc_time_names = 'es_ES'");
        $detail = PedidoDetalle::select(
            'pedido_detalle.id',
            'usuarios.nombre_com as usuario',
            'punto_evaluacion.nombre AS centro_costos',
            'productos.nombre as producto',
            \DB::raw("(SELECT '') as extension"),
            'unidades_empaque.nombre as unidad',
            'pedido_detalle.precio_unitario',
            'pedido_detalle.cantidad',
            'pedido_detalle.impuesto',
            'pedido_detalle.valor_total',
            \DB::raw('DATE_FORMAT(pedido_detalle.created_at, "%d de %M %Y") as format_date')
        )
        ->join('productos', 'pedido_detalle.id_producto', 'productos.id')
        ->join('unidades_empaque', 'productos.id_unidades_empaque', 'unidades_empaque.id')
        ->join('pedidos', 'pedido_detalle.id_pedido', 'pedidos.id')
        ->Join('usuarios', 'pedidos.id_usuario_aprobador', '=', 'usuarios.id')
        ->leftJoin('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
        ->where('pedido_detalle.id_pedido', $request->id)
        ->get();

        return response()->json([
            'success' => 1,
            'responseCode' => 202,
            'message' => "Data encontrada.",
            'data' => $detail
        ]);
    }


    // REPORTS
    public function IndexReports()
    {
        $page_title = 'Administración';
        $permisos = $this->GetAllPermisos();
        $action = __FUNCTION__;

        return view('shop::index_reports', compact('page_title', 'action', 'permisos'));
    }

    public function GetDataInitActiveProducts(Request $request)
    {
        $mode = $request->get("mode");
        $current_page = $request->get('current_page');

        $quantity_rows = 50;
        $limit_result = $this->PaginationCalculate($current_page, $quantity_rows);

        $user = auth()->user();
        $is_admin = $user->can_to_approve == 1;
        $company_group = \DB::table('usuarios')
            ->select('unidad.*')
            ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
            ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
            ->where([
                ['usuarios.id', '=', $user->id],
                ['unidad.main_account_id', '=', $user->main_account_id]
            ])
            ->first();


        $orders = [];
        $total = [];
        $filter_pdv = $request->get('filter');
        // $pdvs = PuntoEvaluacion::select('punto_evaluacion.*')
        // ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
        // ->where('unidad.centro_operacion_id', '=', $company_group->centro_operacion_id)
        // ->get();
        $puntoEvaluacion = new \Modules\Administration\Entities\PuntoEvaluacion();
        $pdvs = $puntoEvaluacion->puntosxLider();

        switch ($mode)
        {
            case 1: //PRODUCTOS ACTIVOS
                $orders = PedidoDetalle::GetOrdersDetailByCompanyGroup($company_group->centro_operacion_id, 2, $limit_result, $quantity_rows, $filter_pdv, $is_admin);
                $total = (PedidoDetalle::GetSumApproveDetailOrder($company_group->centro_operacion_id) == NULL ? 0 : PedidoDetalle::GetSumApproveDetailOrder($company_group->centro_operacion_id)->TOTAL);

                // foreach ($orders["data"] as $key => $detail)
                // {
                //     $total += (PedidoDetalle::GetSumApproveDetailOrder($company_group->centro_operacion_id) == NULL ? 0 : PedidoDetalle::GetSumApproveDetailOrder($detail->id_usuario)->TOTAL);
                // }
            break;

            case 2: //PRODUCTOS CANCELADOS
                $orders = PedidoDetalle::GetOrdersDetailByCompanyGroup($company_group->centro_operacion_id, 0, $limit_result, $quantity_rows, $filter_pdv, $is_admin);
                $total = (PedidoDetalle::GetSumApproveDetailOrder($company_group->centro_operacion_id) == NULL ? 0 : PedidoDetalle::GetSumApproveDetailOrder($company_group->centro_operacion_id)->TOTAL);
                break;

            case 3: //PRODUCTOS SOLICITADOS POR ADMIN
                $orders = Pedidos::GetOrdersByCompanyGroup($company_group->centro_operacion_id, 2, $limit_result, $quantity_rows, $filter_pdv);
                break;

            case 4: //PEDIDOS CANCELADOS POR FACTURACION
                $orders = Pedidos::GetOrdersByCompanyGroup($company_group->centro_operacion_id, 3, $limit_result, $quantity_rows, $filter_pdv);
            break;

            case 5: //PEDIDOS DESPACHADOS POR FACTURACION
                $orders = Pedidos::GetOrdersByCompanyGroup($company_group->centro_operacion_id, 4, $limit_result, $quantity_rows, $filter_pdv);
                break;
            default:
                break;
        }

        return response()->json([
            'success' => 1,
            'responseCode' => 202,
            'message' => "Data encontrada.",
            'data' => [
                'orders' => $orders["data"],
                'totales' => $total,
                'per_page' => $orders["per_page"],
                'pdvs' => $pdvs
            ]
        ]);
    }

    public function SaveQuantityDetailAdminSection(Request $request)
    {
        $id_detail = $request->get('id_detail');
        $quantity = $request->get('quantity');
        $current_page = $request->get('current_page');

        $quantity_rows = 10;
        $limit_result = $this->PaginationCalculate($current_page, $quantity_rows);
        $user = auth()->user();
        $company_group = \DB::table('usuarios')
            ->select('unidad.*')
            ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
            ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
            ->where([
                ['usuarios.id', '=', $user->id],
                ['unidad.main_account_id', '=', $user->main_account_id]
            ])
            ->first();
        $detail = PedidoDetalle::where('id', '=', $id_detail)->first();


        PedidoDetalle::where('id', '=', $id_detail)->update(["cantidad" => $quantity]);
        $is_admin = $user->can_to_approve == 1;
        // Pedidos::UpdateOrderByOriderId($detail->id_pedido);

        // $order_data = Pedidos::GetOrdersDetailByCompanyGroup($company_group->centro_operacion_id, 1, $limit_result, $quantity_rows);
        if($user->can_to_approve != 1)
            PedidoDetalle::UpdateOrderBYIdUser($user->id);
        else
            PedidoDetalle::UpdateOrderBYIdUser($user->id, true);

        $order_data = PedidoDetalle::GetOrdersDetailByCompanyGroup($company_group->centro_operacion_id, 2, $limit_result, $quantity_rows, null, $is_admin);

        $total = (PedidoDetalle::GetSumApproveDetailOrder($company_group->centro_operacion_id) == NULL ? 0 : PedidoDetalle::GetSumApproveDetailOrder($company_group->centro_operacion_id)->TOTAL);
        // foreach ($order_data["data"] as $key => $detail)
        // {
        //     $total += (Pedidos::GetSumHeadOrderByOrderId($detail->id_pedido) == NULL ? 0 : Pedidos::GetSumHeadOrderByOrderId($detail->id_pedido)->TOTAL);
        // }

        return response()->json([
            'success' => 1,
            'responseCode' => 200,
            'message' => 'Actualización exitosa.',
            'data' => [
                'detail' => $order_data["data"],
                'totales' => $total,
                'per_page' => $order_data["per_page"]
            ]
        ]);
    }

    public function DeleteProductsActive(Request $request)
    {
        $items = json_decode($request->items);
        foreach ($items as $key => $item)
        {
            $id_pedido = $item->id_pedido;
            PedidoDetalle::where('id', '=', $item->id)->update(['estado' => 0]); // Se Cancela

            // VALIDAR SI EXISTE DETALLES
            // if(PedidoDetalle::where('id_pedido', '=', $id_pedido)->count() == 0)
            //     Pedidos::where('id', '=', $id_pedido)->delete();
        }

        return response()->json([
            'success' => 1,
            'responseCode' => 200,
            'message' => 'Eliminación exitoso.',
            'data' => null
        ]);
    }

    public function DeleteProductsCancel(Request $request)
    {
        $items = json_decode($request->items);
        foreach ($items as $key => $item)
        {
            PedidoDetalle::where('id', '=', $item->id)->delete();
        }

        return response()->json([
            'success' => 1,
            'responseCode' => 200,
            'message' => 'Eliminación de item exitoso.',
            'data' => null
        ]);
    }

    public function ToGenerateOrderProductsActive(Request $request)
    {
        $items = json_decode($request->items);
        $comment = $request->comment;
        $pdf = $request->file('pdf');
        $user = auth()->user();

        // $update['fecha_solicitud'] = Carbon::now();
        $company_group = \DB::table('usuarios')
        ->select('unidad.*', 'punto_evaluacion.id as id_punto')
        ->Join('punto_evaluacion', 'usuarios.id_punto', '=', 'punto_evaluacion.id')
        ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
        ->where([
            ['usuarios.id', '=', $user->id],
            ['unidad.main_account_id', '=', $user->main_account_id]
        ])
        ->first();

        $consecutivo_recent = Pedidos::where([
            ['estado', '=', 2]
        ])
        ->orderBy('id', 'DESC')
        ->first();

        if (!$consecutivo_recent)
            $id_consecutivo = 1;
        else
        {
            $arr_cons = explode("-", $consecutivo_recent->consecutivo);
            $id_consecutivo = (int)$arr_cons[1] + 1;
        }

        $presupuesto = PresPdvPresupuesto::where([
            ['id_pdv', $user->id_punto],
            ['id_mes', ltrim(Carbon::now()->month, 0)],
            ['id_ano', PresAnos::where('descripcion', Carbon::now()->year)->pluck('id')->first()]
        ])
        ->select('presupuesto', 'dia_corte')
        ->first();

        $dia_corte = ISSET($presupuesto) ? $presupuesto->dia_corte : NULL;
        if($dia_corte != null) // DE NUEVO SE CONSULTA PERO EL PRESUPUESTO UN MES SUPERIOR
        {
            $cal_day = $dia_corte-Carbon::now()->day;
            // $date_arm = Carbon::parse(Carbon::now()->year."-".Carbon::now()->month."-".$dia_corte)->format("Y-m-d");
            if($cal_day <= 0) // EL PRESUPUESTO SE CAMBIA AL MES SIGUIENTE PORQUE HOY ES MAYOR AL DIA DE CORTE
                $consumo_pres = true;
            else
                $consumo_pres = false;
        }
        else
            $consumo_pres = false;

        // VALIDAR SI SUPERA EL MONTO MAXIMO DEL PRESUPUESTO
        // $data_pdv = collect($items)->groupBy('pdv')
        //                     ->map(function ($items) {
        //                         return $items->pluck('pdv');
        //                     })
        //                     ->values()
        //                     ->collapse()
        //                     ->unique()
        //                     ->all();

        // $pdv_presupuesto_superado = false;
        // foreach ($data_pdv as $key_pdv => $value_pdv) 
        // {
        //     $pdv = \DB::table('punto_evaluacion')->where('nombre','=',$value_pdv)->first();
        //     $data_presupuesto = $this->GetBudget($pdv->id);
        //     dd($data_pre);
        //     if($data_presupuesto != null)
        //     {
        //         if($data_presupuesto->consumido > $data_presupuesto->presupuesto)
        //             $pdv_presupuesto_superado = true;
        //     }
        // }

        // if($pdv_presupuesto_superado)
        // {
        //     return response()->json([
        //         'success' => 1,
        //         'responseCode' => 400,
        //         'message' => 'Uno de los puntos ha superado su presupuesto, revisalo y vuelve a intentar generarlo.',
        //         'data' => null
        //     ]);

        // }

        $consecutivo = "PE" . str_pad($company_group->id, 2, "0", STR_PAD_LEFT) . "-" . str_pad($id_consecutivo, 4, "0", STR_PAD_LEFT);
        $oc = Pedidos::GenCode(6);
        $newOrder = new Pedidos();
        $newOrder->fill([
            'consecutivo' => $consecutivo,
            'subtotal' => 0,
            'total' => 0,
            'id_usuario_aprobador' => auth()->user()->id,
            "estado" => 2,
            'obs_general' => ($comment == "" ? NULL : $comment),
            'orden_compra' => $oc,
            "pres_date_consum" => ($consumo_pres) ? Carbon::now()->addMonth()->format("Y-m-d H:i:s") : NULL
        ]);

        if ($newOrder->save())
        {
            // AGREGAR OC SI EXISTE
            if ($pdf != null)
            {
                $id_content = $newOrder->id;
                $current_content = Pedidos::where('id', '=', $id_content)->first();

                $pathVerified = storage_path('app/public/Ocs') . $current_content->path_oc;
                if (\File::exists($pathVerified))
                    \File::delete($pathVerified);

                $path = $this->FunctionSaveDocument($pdf, 'Ocs');
                Pedidos::where('id', '=', $id_content)->update(['path_oc' => $path]);
            }

            $update_detail = [
                "pedido_detalle.estado" => 1,
                "pedido_detalle.id_pedido" => $newOrder->id
            ];

            foreach ($items as $key_item => $value_item)
            {
                PedidoDetalle::where('id', '=', $value_item->id)->update($update_detail); // CAMBIA A
            }

            Pedidos::UpdateOrderByOriderId($newOrder->id);
            $this->ToActivateBotty($newOrder->id); #SE VUELVA A GENERAR TODO EL PROCESO DEL PEDIDO

            return response()->json([
                'success' => 1,
                'responseCode' => 200,
                'message' => 'El pedido se ha generado con éxito.',
                'data' => null
            ]);
        }
        else
        {
            return response()->json([
                'success' => 1,
                'responseCode' => 400,
                'message' => 'El pedido no se ha podido realizar de forma éxitosa, comunícate con el administrador.',
                'data' => null
            ]);
        }

    }

    public function SaveCommentDetail(Request $request)
    {
        $id_detail = $request->get('id_detail');
        $comment = $request->get('comment');

        PedidoDetalle::where('id', '=', $id_detail)->update(["observacion" => $comment]);

        return response()->json([
            'success' => 1,
            'responseCode' => 200,
            'message' => 'Comentario actualizado correctamente',
            'data' => []
        ]);
    }

    public function FillDetailItems(Request $request)
    {
        $items = json_decode($request->get('items'));
        $user = auth()->user();
        $mode = $request->get("mode");

        foreach ($items as $key_i => $value_i)
        {
            if($mode == NULL) // ES NULO, PORQUE YA EXISTE UN ENCABEZADO DE PEDIDO POR ENDE SE PUEDE CONSULTAR POR ID PEDIDO, EL DETALLE
            {
                $detail = PedidoDetalle::select('pedido_detalle.*')->where([['pedido_detalle.id_pedido', $value_i->id], ['pedido_detalle.estado','!=', 0]])->get();
                foreach ($detail as $key_d => $value_d)
                {
                    $request_lo = new Request();
                    $request_lo->request->add([
                        'id_product' => $value_d->id_producto,
                        'quantity' => $value_d->cantidad,
                        'ext' => $value_d->id_extension]
                    );

                    $response = $this->AddProductToCar($request_lo);
                }
            }
            else
            {
                $info_detail = PedidoDetalle::select('pedido_detalle.*')->where("pedido_detalle.id", $value_i->id)->first();
                $request_lo = new Request();
                $request_lo->request->add([
                    'id_product' => $info_detail->id_producto,
                    'quantity' => $info_detail->cantidad,
                    'ext' => $info_detail->id_extension]
                );

                $response = $this->AddProductToCar($request_lo);
            }
        }

        if(COUNT($items) != 0)
        {
            return response()->json([
               'success' => 1,
               'responseCode' => 200,
               'message' => 'Carrito de compras actualizado.',
                'data' => null
            ]);
        }
        else
        {
            return response()->json([
               'success' => 1,
               'responseCode' => 400,
               'message' => 'No se pudo actualizar el carrito de compras, comunícate con el administrador',
                'data' => null
            ]);
        }
    }

    public function SaveOcOrder(Request $request)
    {

        dd($request);
    }

     // -------------- METODOS DE API BOTTY -----------------//
    public function UpdateStateBottyBD(Request $request)
    {
        $state = $request->estado;
        $key = $request->key;
        if($key != "n9ED7Iwxs1P7wdIWNbVskIcvOPrHLZh8lY84UENsmPw=")
        {
            return response()->json([
                'success' => 1,
                'responseCode' => 400,
                'message' => 'No tienes permisos para realizar esta acción, envia la key para acceder.',
                'data' => null
            ]);
        }

        Cg1Configuracion::where('id', '=', 1)->update(["activo" => $state]);
        $resultado = \DB::connection('ka_mysql')->table('tda_ejecuta_proceso')->where('id', '=', 1)->update(["activo" => $state]);

        return response()->json([
            'success' => 1,
            'responseCode' => 200,
            'message' => 'El estado ha sido actualizado correctamente.',
            'data' => null
        ]);
    }

    public function GetOrderNames(Request $request)
    {
        $key = $request->key;
        $state = $request->estado;
        if($key != "n9ED7Iwxs1P7wdIWNbVskIcvOPrHLZh8lY84UENsmPw=")
        {
            return response()->json([
                'success' => 1,
                'responseCode' => 400,
                'message' => 'No tienes permisos para realizar esta acción, envia la key para acceder.',
                'data' => null
            ]);
        }

        $orderNames = Cg1NombreArchivo::select(
            "cg1_nombre_archivo.nombre_pedido",
            "cg1_nombre_archivo.unidad",
            "cg1_nombre_archivo.cliente",
            "cg1_nombre_archivo.consecutivo",
            \DB::raw("(SELECT un.nombre from unidad AS un WHERE un.nit=cg1_nombre_archivo.cliente limit 1) AS nombre_empresa")
        )
        ->where('cg1_nombre_archivo.estado', '=', 1)
        ->get();

        return response()->json([
            'success' => 1,
            'responseCode' => 200,
            'message' => 'Nombres encontrados',
            'data' => $orderNames
        ]);
    }

    public function UpdateOrderStatus(Request $request)
    {
        $key = $request->key;
        $state = $request->estado;
        $nombreArchivo = $request->nombreArchivo;
        // $nuevaUnidad = 0;

        if($key != "n9ED7Iwxs1P7wdIWNbVskIcvOPrHLZh8lY84UENsmPw=")
        {
            return response()->json([
                'success' => 1,
                'responseCode' => 400,
                'message' => 'No tienes permisos para realizar esta acción, envia la key para acceder.',
                'data' => null
            ]);
        }

        if ($state == 3)
            $nuevoNombre = $nombreArchivo."_error_savk_".$request->time_cg1;
        else
            $nuevoNombre = $nombreArchivo.time();

        Cg1NombreArchivo::where('nombre_pedido', '=', $nombreArchivo)
        ->update([
            'estado' => $state,
            'nombre_pedido' => $nuevoNombre,
            // 'unidad' => $nuevaUnidad,
        ]);

        $data = Cg1NombreArchivo::select(
            'cg1_nombre_archivo.id as archivo_id',
            'cdcp.oc'
        )
        ->Join('cg1_detalle_cargue_pedido as cdcp', 'cg1_nombre_archivo.id', '=', 'cdcp.nombre_archivo_id')
        ->where(
            ['cg1_nombre_archivo.nombre_pedido', '=', $nuevoNombre],
            ['cdcp.estado', '=', 0]
        )
        ->groupBy('cdcp.oc');

        return response()->json([
            'success' => 1,
            'responseCode' => 200,
            'message' => 'Actualización del pedido correcto.',
            'data' => $data
        ]);
    }

     // -------------- FINAL - METODOS DE API BOTTY -----------------//
}
