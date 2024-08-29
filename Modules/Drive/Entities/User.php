<?php

namespace Modules\Drive\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $fillable = [
        'id',
        'codigo',
        'nombre_com',
        'email',
        'telefono',
        'usuario',
        'alias',
        'password',
        'ultimo_acceso',
        'estado',
        'id_grupo',
        'cargo_id',
        'pais_id',
        'departamento_id',
        'ciudad_id',
        'email_recibe',
        'tipo_cliente',
        'cguno_id',
        'img_avatar',
        'main_account_id'
    ];

}
