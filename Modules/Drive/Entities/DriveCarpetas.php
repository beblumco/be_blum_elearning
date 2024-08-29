<?php

namespace Modules\Drive\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DriveCarpetas extends Model
{
    use HasFactory;

    protected $table = 'savk_drive_carpetas';
    protected $fillable = [
        'id',
        'nombre',
        'padre_id',
        'propietario_id',
        'cant_archivos',
        'tamano',
        'propietario_nombre',
        'main_account_id',
        'tipo',
        'permanente'
    ];

    protected $appends = ['type'];

    public function files()
    {
        return $this->hasMany(DriveArchivos::class, 'carpeta_id');
    }

    public function permissions()
    {
        return $this->hasMany(DrivePermisos::class, 'carpeta_id');
    }

    public function share()
    {
        return $this->hasMany(DriveCompartir::class, 'carpeta_id');
    }

    public function getTypeAttribute()
    {
        return 'folder';
    }

    public static function getParentFolder($id_folder)
    {
        $folder = self::select('id', 'padre_id', 'permanente')->where('id', $id_folder)->first();

        if ($folder->padre_id != NULL) {
            self::getParentFolder($folder->id);
        }

        if ($folder->permanente == 1) {
            return true;
        } else {
            return false;
        }
    }
}
