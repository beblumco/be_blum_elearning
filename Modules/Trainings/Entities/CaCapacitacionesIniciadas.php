<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaCapacitacionesIniciadas extends Model
{
    use HasFactory;

    protected $table = 'ca_capacitaciones_iniciadas';
    protected $fillable = [
        'id',
        'id_capacitacion',
        'id_modulo',
        'id_usuario',
        'fecha_inicio'
    ];

    public function ValidateExist($data)
    {
        $exist = $this
        ->where('id_capacitacion', '=', $data['id_capacitacion'])
        ->where('id_modulo', '=', $data['id_modulo'])
        ->where('id_usuario', '=', $data['id_usuario'])
        ->get();
        return COUNT($exist);
    }
}
