<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaTiposRespuesta extends Model
{
    use HasFactory;

    protected $table = 'ca_tipos_respuesta';
    protected $fillable = [
        'id',	
        'nombre',	
        'description',	
        'icono',	
        'estado',
    ];
}
