<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guia extends Model
{
    protected $table = 'guia';
    protected $fillable = [
        'id',
        'titulo',
        'descripcion',
        'id_elemento',
        'posicion_mensaje',
        'tipo',
    ];
}
