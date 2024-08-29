<?php

namespace Modules\Drive\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DriveAlmacenamiento extends Model
{
    use HasFactory;

    protected $table = 'savk_drive_almacenamiento';
    protected $fillable = [
        'id',
        'main_account_id',
        'tamano_total',
        'tamano_usado',
    ];

    protected static function newFactory()
    {
        return \Modules\Drive\Database\factories\DriveAlmacenamientoFactory::new();
    }
}
