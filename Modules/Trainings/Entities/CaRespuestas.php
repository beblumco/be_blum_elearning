<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaRespuestas extends Model
{
    use HasFactory;

    protected $table = 'ca_respuestas';
    protected $fillable = [
        'id',
        'respuesta',
        'ponderado',
        'estado',
        'id_tipo_respuesta',
        'id_pregunta',
    ];

    public function GetAnswersById($id_question)
    {
        $answers = $this->select(
            'ca_respuestas.*',
            \DB::raw('IF(ca_respuestas.ponderado = 0, false , true) checked')
        )
        ->where([
            ['ca_respuestas.id_pregunta', '=', $id_question],
            ['ca_respuestas.estado', '=', 1]
        ])
        ->get();

        return $answers;
    }

    public function DeleteAnswersByIdModule($id_module)
    {
        $delete_answer = CaRespuestas::Join('ca_preguntas', 'ca_respuestas.id_pregunta', '=', 'ca_preguntas.id')
        ->Join('ca_modulos', 'ca_preguntas.id_modulo', '=', 'ca_modulos.id')
        ->where('ca_modulos.id','=', $id_module)
        ->delete();
    }

    public function DeleteAnswersByIdTraining($id_training)
    {
        $delete_answer = CaRespuestas::Join('ca_preguntas', 'ca_respuestas.id_pregunta', '=', 'ca_preguntas.id')
        ->where('ca_preguntas.id_capacitacion','=', $id_training)
        ->delete();
    }
}
