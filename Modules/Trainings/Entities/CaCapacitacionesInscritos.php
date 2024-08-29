<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaCapacitacionesInscritos extends Model
{
    use HasFactory;

    protected $table = 'ca_capacitaciones_inscritos';
    protected $fillable = [
        'id',
        'id_capacitacion',
        'id_usuario'
    ];
}
