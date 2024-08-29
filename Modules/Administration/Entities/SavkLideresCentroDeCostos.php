<?php

namespace Modules\Administration\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavkLideresCentroDeCostos extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_centro_de_costo',
        'id_usuario'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Administration\Database\factories\SavkLideresCentroDeCostosFactory::new();
    }
}
