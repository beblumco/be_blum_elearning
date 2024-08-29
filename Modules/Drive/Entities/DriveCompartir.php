<?php

namespace Modules\Drive\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DriveCompartir extends Model
{
    use HasFactory;

    protected $table = 'savk_drive_compartir';
    protected $fillable = [
        'id',
        'carpeta_id',
        'archivo_id',
        'centro_operacion_id',
        'unidad_id',
        'punto_evaluacion_id',
        'responsable_id',
        'main_account_id'
    ];

    public function folder()
    {
        return $this->belongsTo(DriveCarpetas::class);
    }

}
