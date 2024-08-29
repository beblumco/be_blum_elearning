<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Trainings\Entities\CaRespuestas;

class CaPreguntas extends Model
{
    use HasFactory;

    protected $table = 'ca_preguntas';
    protected $fillable = [
        'id',
        'pregunta',
        'orden',
        'estado',
        'id_modulo',
        'id_capacitacion'
    ];

    public function GetQuestionsByIdModule($id_module)
    {
        $questions = $this->select(
            'ca_preguntas.*'
        )
        ->where('ca_preguntas.id_modulo', '=', $id_module)
        ->get();

        $instace_answers = new CaRespuestas();
        foreach ($questions as $key => $question)
        {
            $questions[$key]->respuestas = $instace_answers->GetAnswersById($question->id);
        }

        return $questions;
    }

    public function GetQuestionsByIdTraining($id_training)
    {
        $questions = $this->select(
            'ca_preguntas.*'
        )
        ->where('ca_preguntas.id_capacitacion', '=', $id_training)
        ->get();

        $instace_answers = new CaRespuestas();
        foreach ($questions as $key => $question)
        {
            $questions[$key]->respuestas = $instace_answers->GetAnswersById($question->id);
        }

        return $questions;
    }

    public function DeleteQuestionsByIdModule($id_module)
    {
        $delete_question =$this->where('ca_preguntas.id_modulo','=', $id_module)->delete();
    }

    public function DeleteQuestionsByIdTraining($id_training)
    {
        $delete_question =$this->where('ca_preguntas.id_capacitacion','=', $id_training)->delete();
    }

    public function FGetAnswersByQuestionId($idQuestion)
    {
        $answers = CaRespuestas::select(
            'ca_respuestas.*',
            \DB::raw('IF(ca_respuestas.ponderado = 0, "false", "true") AS correct'),
            \DB::raw('(SELECT "false") AS checked')
        )
        ->where('ca_respuestas.id_pregunta', '=', $idQuestion)
        ->get();

        return $answers;
    }

    public function FGetAnswersByQuestionIdView($idQuestion, $idEvaluacion)
    {
        $answers = CaRespuestas::select(
            'ca_respuestas.*',
            \DB::raw('IF(ca_respuestas.ponderado = 0, "false", "true") AS correct'),
            \DB::raw(' if(
                    (SELECT id_respuesta from ca_evaluacion_iniciada_detalle as i
                        where i.id_evaluacion_iniciada = '.$idEvaluacion.' and i.id_pregunta = ca_respuestas.id_pregunta) = ca_respuestas.id, "true", "false" ) AS checked')
        )
        ->where('ca_respuestas.id_pregunta', '=', $idQuestion)
        ->get();

        return $answers;
    }

    public function existPreguntasByTrainig($id_training)
    {
        $questions = $this->select(
            'ca_preguntas.*'
        )
        ->join('ca_modulos', 'ca_preguntas.id_modulo', 'ca_modulos.id')
        ->join('ca_capacitaciones', 'ca_modulos.id_capacitacion', 'ca_capacitaciones.id')
        ->where('ca_capacitaciones.id', '=', $id_training)
        ->count();

        return $questions;
    }
}
