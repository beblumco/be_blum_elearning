<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaCapAsistidasimg extends Model
{
    use HasFactory;

    protected $table = 'ca_cap_asistidasimg';
    protected $fillable = [
        'id',
        'path',
        'id_cap_asistida',
    ];

}
