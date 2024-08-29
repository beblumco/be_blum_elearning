<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PresAnos extends Model
{
    protected $table = 'pres_anos';

    protected $fillable = [
        'id',
        'descripcion',
        'estado'
    ];

    public static function GetYears()
    {
        return self::select("id", "descripcion as name")
            ->where('estado', '=', 1)
            ->get();
    }
}
