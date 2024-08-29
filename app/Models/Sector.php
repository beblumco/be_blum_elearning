<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $table = 'sector';
    protected $fillable = [
        'id',
        'nombre',
        'klaxen_aplicaciones_id'
    ];

    public static function GetAllSectors()
    {
        $sectors = self::select(
            'sector.*'
        )
        ->get();

        return $sectors;
    }
}
