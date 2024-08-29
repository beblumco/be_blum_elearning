<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavkToken extends Model
{
    protected $table = 'savk_token';
    protected $fillable = [
        'token'
    ];
}
