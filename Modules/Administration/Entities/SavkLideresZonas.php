<?php

namespace Modules\Administration\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavkLideresZonas extends Model
{
    use HasFactory;

    protected $table = 'savk_liederes_zonas';

    protected $fillable = [
        'id_grupos_puntos',
        'id_usuario'
    ];

}
