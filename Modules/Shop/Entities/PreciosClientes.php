<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Modules\Shop\Entities\DocumentacionProducto;
use Modules\Shop\Entities\Extensiones;

class PreciosClientes extends Model
{
    protected $table = 'precios_clientes';

    protected $fillable = [
        'id',
        'precio',
        'id_producto',
        'id_empresa',
        'estado'
    ];

    public static function GetProductsWithPricesByCompanyGroup($id_company_group)
    {
        $products = self::select(
            'productos.*',
            'categorias_productos.nombre as category',
            'precios_clientes.precio as price',
            \DB::raw('IF(productos.imagen IS NULL, CONCAT("'.env('API').'assets/img/sin_imagen.png"),CONCAT("'.env('API').'storage/Products/",productos.imagen)) as image'),
        )
        ->join('productos', 'precios_clientes.id_producto', '=', 'productos.id')
        ->join('unidad', 'precios_clientes.id_empresa', '=', 'unidad.id')
        ->join('centro_operacion', 'unidad.centro_operacion_id', '=', 'centro_operacion.id')
        ->join('categorias_productos', 'productos.id_categoria', '=', 'categorias_productos.id')
        ->where('centro_operacion.id', '=', $id_company_group)
        ->get();

        foreach ($products as $key => $product) 
        {
            $product->id_encrypt = Crypt::encryptString($product->id);
            $product->extensions = Extensiones::GetExtensionsByProduct($product->id);
        }

        return $products;
        
    }
    
    public static function GetProductsWithPricesByCompany($id_company)
    {
        $products = self::select(
            'productos.*',
            'categorias_productos.nombre as category',
            'precios_clientes.precio as price',
            \DB::raw('IF(productos.imagen IS NULL, CONCAT("'.env('API').'assets/img/sin_imagen.png"),CONCAT("'.env('API').'storage/Products/",productos.imagen)) as image'),
        )
        ->join('productos', 'precios_clientes.id_producto', '=', 'productos.id')
        ->join('unidad', 'precios_clientes.id_empresa', '=', 'unidad.id')
        ->join('categorias_productos', 'productos.id_categoria', '=', 'categorias_productos.id')
        ->where('unidad.id', '=', $id_company)
        ->get();

        foreach ($products as $key => $product) 
        {
            $product->id_encrypt = Crypt::encryptString($product->id);
            $product->extensions = Extensiones::GetExtensionsByProduct($product->id);
        }

        return $products;
        
    }

    public static function GetProductByCompanyGroupAndIdProduct($id_company_group, $id_product, $limit_result=null, $quantity_rows=null)
    {
        $product = self::select(
            'productos.*',
            'categorias_productos.nombre as category',
            'precios_clientes.precio as price',
            \DB::raw('IF(productos.imagen IS NULL, CONCAT("'.env('API').'assets/img/sin_imagen.png"),CONCAT("'.env('API').'storage/Products/",productos.imagen)) as image'),
            \DB::raw("(SELECT 1) as cantidad")
        )
        ->join('productos', 'precios_clientes.id_producto', '=', 'productos.id')
        ->join('unidad', 'precios_clientes.id_empresa', '=', 'unidad.id')
        ->join('centro_operacion', 'unidad.centro_operacion_id', '=', 'centro_operacion.id')
        ->join('categorias_productos', 'productos.id_categoria', '=', 'categorias_productos.id')
        ->where([
            ['centro_operacion.id', '=', $id_company_group],
            ['productos.id', '=', $id_product]
        ])
        ->first();

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

        $product->id_encrypt = Crypt::encryptString($product->id);
        $product->documentacion = DocumentacionProducto::GetDocumentationByIdProduct($product->id);
        $product->historial = PedidoDetalle::GetOrdersDetailByCompanyGroup($company_group->centro_operacion_id, 4, $limit_result, $quantity_rows, null, false, true, $id_product);
        $product->extensions = Extensiones::GetExtensionsByProduct($product->id);

        return $product;
        
    }
    

}
