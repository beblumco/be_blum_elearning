<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavkPermisos extends Model
{
    protected $table = 'savk_permisos';
    protected $fillable = [
        'id',
        'evento',
        'descripcion',
    ];
}
