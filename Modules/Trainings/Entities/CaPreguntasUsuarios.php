<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaPreguntasUsuarios extends Model
{
    use HasFactory;

    protected $table = 'ca_preguntas_usuarios';
    protected $fillable = [
        'id_capacitacion',
        'id_modulo',
        'id_usuario',
        'pregunta',
        'respuesta',
        'estado',
    ];

    public function getPreguntasCap($id_usuario, $id_capacitacion, $id_modulo)
    {
        $questions = $this->select(
            'ca_preguntas_usuarios.id',
            'ca_preguntas_usuarios.pregunta',
            'ca_preguntas_usuarios.respuesta',
            \DB::raw("IF(ca_preguntas_usuarios.estado = 0, 'Pendiente' , 'Resuelta') AS estado"),
        )
        ->where([
            ['ca_preguntas_usuarios.id_usuario', '=', $id_usuario],
            ['ca_preguntas_usuarios.id_capacitacion', '=', $id_capacitacion],
            ['ca_preguntas_usuarios.id_modulo', '=', $id_modulo],
        ])
        ->get();

        return $questions;
    }

}
