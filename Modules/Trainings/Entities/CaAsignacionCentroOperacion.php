<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class CaAsignacionCentroOperacion extends Model
{
    use HasFactory;

    protected $table = 'ca_asignacion_centro_operacion';
    protected $fillable = [
        'id',
        'id_centro_operacion',
        'id_capacitacion'
    ];

    public function GetCentroOperacionByIdTraining($idTraining)
    {
        $sectors = $this->select(
            'centro_operacion.*'
        )
        ->Join('centro_operacion', 'ca_asignacion_centro_operacion.id_centro_operacion','=','centro_operacion.id')
        ->where('ca_asignacion_centro_operacion.id_capacitacion', '=', $idTraining)
        ->get();

        return $sectors;
    }

    public static function DeleteByIdTraining($id_training)
    {
        self::where('id_capacitacion', '=', $id_training)->delete();
    }
}
