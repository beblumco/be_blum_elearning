<?php

namespace Modules\Administration\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Modules\Administration\Entities\Perfil;
use App\Models\Unidad;

class User extends Model
{
    use HasFactory;

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
        'can_to_approve',
        'can_ajust_pres'
    ];

    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'savk_perfil_id');
    }



    public function empresasLider()
    {
        return $this->belongsToMany(Unidad::class, 'savk_organizacion_lideres', 'usuario_id', 'empresa_id');
    }

    public function getAll(array $where, int $cant_pag)
    {
        $data = $this->select(
            'usuarios.id',
            'usuarios.nombre_com AS nombre',
            'usuarios.email',
            'usuarios.telefono',
            'usuarios.estado AS estado_id',
            \DB::raw("
                (CASE
                    WHEN usuarios.estado = '1' THEN 'Activo'
                    WHEN usuarios.estado = '2' THEN 'Inactivo'
                END) AS estado
            "),
            'usuarios.cargo_id',
            'ca.nombre AS cargo',
            'pa.nombre AS pais',
            'usuarios.pais_id',
            'de.nombre AS departamento',
            'usuarios.departamento_id',
            'c.nombre AS ciudad',
            'usuarios.ciudad_id',
            'usuarios.created_at',
            \DB::raw("(
                SELECT COUNT(*) FROM usuario_punto AS up
                WHERE up.usuario_id = usuarios.id
            ) AS cant_puntos ")
        )
            ->leftJoin('paises AS pa', 'pa.id', 'usuarios.pais_id')
            ->leftJoin('departamenos AS de', 'de.id', 'usuarios.departamento_id')
            ->leftJoin('ciudades AS c', 'c.id', 'usuarios.ciudad_id')
            ->leftJoin('cargos AS ca', 'ca.id', 'usuarios.cargo_id')
            ->where($where);

        return $data->paginate($cant_pag);
    }

    public function getPointsUser(int $user_id)
    {
        $data = \DB::table('usuario_punto')->select(
            'punto_id'
        )
            ->where('usuario_id', $user_id)->pluck('punto_id');

        return $data;
    }
}
