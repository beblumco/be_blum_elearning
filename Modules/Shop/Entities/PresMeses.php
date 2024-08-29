<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Modules\Shop\Entities\DocumentacionProducto;

class PresMeses extends Model
{
    protected $table = 'pres_meses';

    protected $fillable = [
        'id',
        'descripcion',
        'estado'
    ];

    public static function GetMonths()
    {
        return self::select(
            "id",
            "descripcion as name"
        )
            ->where('estado', '=', 1)
            ->get();
    }
}
