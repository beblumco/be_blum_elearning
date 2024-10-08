<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $table = 'usuarios';
    protected $fillable = [
        'codigo',
        'nombre_com',
        'email',
        'telefono',
        'usuario',
        'alias',
        'password',
        'ultimo_acceso',
        'estado',
        'id_grupo',
        'cargo_id',
        'pais_id',
        'departamento_id',
        'ciudad_id',
        'session',
        'remember_token',
        'created_at',
        'updated_at',
        'email_recibe',
        'tipo_cliente',
        'img_avatar',
        'cguno_id',
        'main_account_id',
        'empresa',
        'cargo',
        'lider_estados',
        'savk_perfil_id',
        'savk_principal',
        'id_punto',
        'id_seccion',
        'can_to_approve'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function existsMail($email)
    {
        return $this->where('email', $email)->exists();
    }

    public function changePassword($email, $pass_encry)
    {
        $person = $this->where('email', $email)->first();

        if ($person != null) {
            $person->password = $pass_encry;
            $person->save();
        }
    }
}
