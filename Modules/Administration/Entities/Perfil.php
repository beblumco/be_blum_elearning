<?php

namespace Modules\Administration\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Modules\Administration\Entities\User;
class Perfil extends Model
{
    use HasFactory;

    protected $table = 'savk_organizacion_perfiles';

    protected $fillable = [
        'id', 'nombre', 'estado'
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'savk_perfil_id');
    }
}
