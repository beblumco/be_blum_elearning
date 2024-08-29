<?php

namespace Modules\Administration\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavkLideresEmpresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_empresa',
        'id_usuario'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Administration\Database\factories\SavkLideresEmpresaFactory::new();
    }
}
