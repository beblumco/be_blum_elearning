<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaCapAsistidas extends Model
{
    use HasFactory;

    protected $table = 'ca_cap_asistidas';
    protected $fillable = [
        'id',
        'fecha_agendamiento',
        'id_capacitacion',
        'id_asesor',
        'modalidad',
        'tipo',
        'id_cliente',
        'duracion',
        'link',
        'anfitrion_cliente',
        'observacion'
    ];
}
