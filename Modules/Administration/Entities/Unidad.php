<?php

namespace Modules\Administration\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unidad extends Model
{
    use HasFactory;

    protected $table = 'unidad';
    protected $fillable = [
        'nombre',
        'nit',
        'direccion',
        'telefono',
        'email',
        'usuarios_id',
        'pais_id',
        'departamentos_id',
        'ciudad_id',
        'estado',
        'centro_operacion_id',
        'estado_creacion',
        'main_account_id',
        'img_avatar'
    ];

    public function getAll(array $where, int $cant_pag, array $whereIn)
    {
        $data = $this->select(
            'unidad.id',
            'unidad.nombre',
            'unidad.nit',
            'unidad.direccion',
            'unidad.telefono',
            'unidad.email',
            'unidad.pais_id',
            'unidad.departamentos_id',
            'unidad.ciudad_id',
            'unidad.img_avatar',
            \DB::raw("
                (CASE
                    WHEN unidad.estado = '1' THEN 'Activo'
                    WHEN unidad.estado = '2' THEN 'Inactivo'
                END) AS estado
            "),
            'unidad.estado AS estado_id',
            'unidad.centro_operacion_id',
            'p.nombre AS pais',
            'de.nombre AS departamento',
            'c.nombre AS ciudad',
            'u.nombre_com AS usuario',
            'co.nombre AS grupo_empresa',
            'unidad.created_at',
        )
        ->leftJoin('usuarios AS u', 'u.id', 'unidad.usuarios_id')
        ->leftJoin('paises AS p', 'p.id', 'unidad.pais_id')
        ->leftJoin('departamenos AS de', 'de.id', 'unidad.departamentos_id')
        ->leftJoin('ciudades AS c', 'c.id', 'unidad.ciudad_id')
        ->leftJoin('centro_operacion AS co', 'co.id', 'unidad.centro_operacion_id')
        ->where($where);

        if(sizeof($whereIn) > 0){
            if(auth()->user()->id_grupo == 44){
                $data = $data->whereIn('unidad.centro_operacion_id',$whereIn);
            }else if(auth()->user()->id_grupo == 45){
                $data = $data->whereIn('unidad.id',$whereIn);
            }
        }

        return $data->paginate($cant_pag);

    }

}
