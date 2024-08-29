<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Modules\Administration\Entities\User;
use Modules\Administration\Entities\Lider;

class Unidad extends Model
{
    protected $table = 'unidad';
    protected $fillable = [
        'id',
        'nombre',
        'nit',
        'direccion',
        'telefono',
        'email',
        'usuarios_id',
    	'pais_id',
        'departamentos_id',
        'ciudad_id',
        'estado',
        'centro_operacion_id',
    	'estado_creacion'
    ];

    public function lideres()
    {
        return $this->belongsToMany(User::class, 'savk_organizacion_lideres', 'empresa_id', 'usuario_id');
    }
}
