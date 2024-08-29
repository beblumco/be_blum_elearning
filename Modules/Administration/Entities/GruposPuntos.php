<?php

namespace Modules\Administration\Entities;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GruposPuntos extends Pivot
{
    use HasFactory;

    protected $table = 'grupos_puntos';

    protected $fillable = [
        'id',
        'nombre',
        'observacion',
        'main_account_id'
    ];

    public function getAll(array $where = [], int $cant_pag, array $whereIn =[])
    {
        $data = $this->select(
            'grupos_puntos.id AS id',
            'grupos_puntos.main_account_id',
            'grupos_puntos.nombre'
        )
        ->leftJoin('centro_operacion AS co', 'co.main_account_id', 'grupos_puntos.main_account_id')
        ->groupBy('grupos_puntos.id', 'grupos_puntos.main_account_id', 'grupos_puntos.nombre');

        if(sizeof($where) > 0){
            $data = $data->where($where);
        }
        if(sizeof($whereIn) > 0){
            $data = $data->whereIn('grupos_puntos.id',$whereIn);
        }

        return $data->paginate($cant_pag);
    }

}
