<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavkSecciones extends Model
{
    protected $table = 'savk_secciones';
    protected $fillable = [
        'id',
        'nombre',
        'main_account_id'
    ];
}
