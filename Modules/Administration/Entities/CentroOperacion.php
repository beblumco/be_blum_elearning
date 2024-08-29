<?php

namespace Modules\Administration\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CentroOperacion extends Model
{
    use HasFactory;

    protected $table = 'centro_operacion';
    protected $fillable = [
        'id', 'nombre', 'identificacion', 'ciudad_id',
        'usuario_id', 'estado', 'estado_creacion', 'sector_id',
        'espacio_mb', 'asesor_id', 'main_account_id', 'img_avatar', 'img_certificado'
    ];

    /**
     * Obtengo todos los centros de costos
     * @param array $where
     * @param int $cant_pag
     * @return mixed
     */
    public function getAll(array $where = [], int $cant_pag, array $whereIn =[])
    {
        $data = $this->select(
            'centro_operacion.id AS id',
            'centro_operacion.main_account_id',
            'centro_operacion.asesor_id',
            'centro_operacion.nombre',
            'centro_operacion.img_avatar',
            'centro_operacion.img_certificado',
            'identificacion AS nit',
            'c.nombre AS ciudad',
            'c.id AS ciudad_id',
            \DB::raw("
                (CASE
                    WHEN centro_operacion.estado = '1' THEN 'Activo'
                    WHEN centro_operacion.estado = '2' THEN 'Inactivo'
                END) AS estado
            "),
            'centro_operacion.estado AS estado_num',
            'u.nombre_com AS usuario',
            'centro_operacion.usuario_id AS usuario_id',
            'a.nombre_com AS asesor',
            's.nombre AS sector',
            'centro_operacion.sector_id',
            'centro_operacion.created_at',
            'de.id AS departamento',
            'de.paises_id AS pais_id'
        )
        ->leftJoin('usuarios AS u', 'u.id', 'usuario_id')
        ->leftJoin('usuarios AS a', 'a.id', 'asesor_id')
        ->leftJoin('ciudades AS c', 'c.id', 'centro_operacion.ciudad_id')
        ->leftJoin('sector AS s', 's.id', 'sector_id')
        ->leftJoin('departamenos AS de', 'de.id', 'c.departamentos_id');

        if(sizeof($where) > 0){
            $data = $data->where($where);
        }
        if(sizeof($whereIn) > 0){
            $data = $data->whereIn('centro_operacion.id',$whereIn);
        }

        return $data->paginate($cant_pag);
    }
}
