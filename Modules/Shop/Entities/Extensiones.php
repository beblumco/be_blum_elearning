<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Extensiones extends Model
{
    protected $table = 'extensiones';

    protected $fillable = [
        'id',
        'nombre',
        'descripcion',
        'estado'
    ];

    public static function GetExtensionsByProduct($id_product)
    {
        return self::select("extensiones.*")
        ->Join('extensiones_productos', 'extensiones.id', '=', 'extensiones_productos.id_extension')
        ->where('extensiones_productos.id_producto', '=', $id_product)
        ->get();
    }
}
