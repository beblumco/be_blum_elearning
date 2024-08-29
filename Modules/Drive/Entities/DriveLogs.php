<?php

namespace Modules\Drive\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DriveLogs extends Model
{
    use HasFactory;

    protected $table = 'savk_drive_logs';
    protected $fillable = [
        'carpeta_id',
        'archivo_id',
        'descripcion',
        'usuario_id',
        'usuario_nombre'
    ];

    public static function saveLog(
        int $folder_id = 0,
        int $file_id = 0,
        string $description,
        int $user_id,
        string $user_name
    )
    {
        $data = [
            'descripcion'    => $description,
            'usuario_id'     => $user_id,
            'usuario_nombre' => $user_name,
            'created_at'     => now(),
            'updated_at'     => now()
        ];

        if ($folder_id != 0)
            $data['carpeta_id'] = $folder_id;

        if ($file_id != 0)
            $data['archivo_id'] = $file_id;

        \DB::table('savk_drive_logs')->insert($data);
    }

}
