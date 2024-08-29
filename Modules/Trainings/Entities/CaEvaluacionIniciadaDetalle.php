<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaEvaluacionIniciadaDetalle extends Model
{
    use HasFactory;

    protected $table = 'ca_evaluacion_iniciada_detalle';
    protected $fillable = [
        'id',	
        'id_evaluacion_iniciada',	
        'id_pregunta',	
        'id_respuesta',
    ];
}
