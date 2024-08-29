<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'id',
        'nombre',
        'descripcion',
        'referencia',
        'impuesto',
        'estado',
        'imagen',
        'id_categoria',
        'id_unidades_empaque',
        'id_linea',
        'id_etiqueta'
    ];

    public static function GetProductPricesByIdProduct($id_product, $id_company)
    {
        
    }
}
