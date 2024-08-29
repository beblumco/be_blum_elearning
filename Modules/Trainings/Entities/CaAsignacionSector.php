<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class CaAsignacionSector extends Model
{
    use HasFactory;

    protected $table = 'ca_asignacion_sector';
    protected $fillable = [
        'id',
        'id_sector',
        'id_capacitacion'
    ];

    public function GetSectorsByIdTraining($idTraining)
    {
        $sectors = $this->select(
            'sector.*'
        )
        ->Join('sector', 'ca_asignacion_sector.id_sector','=','sector.id')
        ->where('ca_asignacion_sector.id_capacitacion', '=', $idTraining)
        ->get();

        return $sectors;
    }

    public static function DeleteByIdTraining($id_training)
    {
        self::where('id_capacitacion', '=', $id_training)->delete();
    }
}
