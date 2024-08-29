<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class CaAsistentes extends Model
{
    use HasFactory;

    protected $table = 'ca_asistentes';
    protected $fillable = [
        'id',
        'documento',
        'nombre',
        'email',
        'empresa'
    ];
}
