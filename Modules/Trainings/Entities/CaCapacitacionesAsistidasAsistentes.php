<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaCapacitacionesAsistidasAsistentes extends Model
{
    use HasFactory;

    protected $table = 'ca_capacitaciones_asistidas_asistentes';
    protected $fillable = [
        'id',
        'id_capacitacion_asistida',
        'id_usuario',
        'id_asistente',
        'signature_path'
    ];
}
