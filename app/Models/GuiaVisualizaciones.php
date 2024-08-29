<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuiaVisualizaciones extends Model
{
    protected $table = 'guia_visualizaciones';
    protected $fillable = [
        'id',
        'id_usuario',
        'id_guia',
        'fecha_realizacion',
    ];
}
