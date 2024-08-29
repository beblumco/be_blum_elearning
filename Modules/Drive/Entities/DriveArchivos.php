<?php

namespace Modules\Drive\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DriveArchivos extends Model
{
    use HasFactory;

    protected $table = 'savk_drive_archivos';
    protected $fillable = [
        'id',
        'nombre',
        'tipo',
        'tamano',
        'carpeta_id',
        'propietario_id',
        'ext',
        'ruta',
        'propietario_nombre',
        'main_account_id',
        'tipo_drive'
    ];

    protected $appends = ['type'];
    public function folder()
    {
        return $this->belongsTo(DriveCarpetas::class);
    }

    public function permissions()
    {
        return $this->hasMany(DrivePermisos::class, 'archivo_id');
    }

    public function getTypeAttribute()
    {
        return 'file';
    }
}
