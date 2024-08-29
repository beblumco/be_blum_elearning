<?php

namespace Modules\Drive\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DrivePermisos extends Model
{
    use HasFactory;

    protected $table = 'savk_drive_permisos';
    protected $fillable = [
        'id',
        'carpeta_id',
        'lectura',
        'escritura',
        'editar',
        'compartir',
        'eliminar'
    ];

    public function folders()
    {
        return $this->belongsTo(DriveCarpetas::class, 'carpeta_id');
    }

}
