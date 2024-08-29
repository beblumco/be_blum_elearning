<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavkPermisosUsuarios extends Model
{
    protected $table = 'savk_permisos_usuarios';
    protected $fillable = [
        'id',
        'id_usuario',
        'id_permiso',
    ];
}
