<?php

namespace Modules\Administration\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class PuntoEvaluacion extends Model
{
    use HasFactory;

    protected $table = 'punto_evaluacion';
    protected $fillable = [
        'id',
        'nombre',
        'direccion',
        'telefono',
        'correo',
        'pais_id',
        'departamentos_id',
        'ciudad_id',
        'estado',
        'centro_operacion_id',
        'estado_creacion',
        'main_account_id',
        'unidad_id',
        'codigo'
    ];

    public function getAll(array $where, int $cant_pag, array $whereIn)
    {
        $data = $this->select(
            'punto_evaluacion.id',
            'punto_evaluacion.nombre',
            'punto_evaluacion.direccion',
            'punto_evaluacion.telefono',
            'punto_evaluacion.correo AS email',
            'punto_evaluacion.pais_id',
            'punto_evaluacion.departamentos_id',
            'punto_evaluacion.ciudad_id',
            \DB::raw("
                (CASE
                    WHEN punto_evaluacion.estado = '1' THEN 'Activo'
                    WHEN punto_evaluacion.estado = '2' THEN 'Inactivo'
                END) AS estado
            "),
            'punto_evaluacion.estado AS estado_id',
            'punto_evaluacion.unidad_id',
            'p.nombre AS pais',
            'de.nombre AS departamento',
            'c.nombre AS ciudad',
            'un.nombre AS empresa',
            'co.nombre AS grupo_empresa',
            'punto_evaluacion.created_at',
            'gp.id AS id_zona',
            'gp.nombre AS zona'
        )
            ->leftJoin('paises AS p', 'p.id', 'punto_evaluacion.pais_id')
            ->leftJoin('departamenos AS de', 'de.id', 'punto_evaluacion.departamentos_id')
            ->leftJoin('ciudades AS c', 'c.id', 'punto_evaluacion.ciudad_id')
            ->leftJoin('unidad AS un', 'un.id', 'punto_evaluacion.unidad_id')
            ->leftJoin('centro_operacion AS co', 'co.id', 'un.centro_operacion_id')
            ->leftJoin('grupos_sub_puntos', 'punto_evaluacion.id', 'grupos_sub_puntos.punto_id')
            ->leftJoin('grupos_puntos as  gp', 'grupos_sub_puntos.grupo_punto_id', 'gp.id')
            ->where($where);

        if(sizeof($whereIn) > 0){
            if(auth()->user()->id_grupo == 44){
                $data = $data->whereIn('punto_evaluacion.unidad_id',$whereIn);
            }else{
                $data = $data->whereIn('punto_evaluacion.id',$whereIn);
            }
        }

        if ($cant_pag == false) {
            return $data->get();
        }else{
            return $data->paginate($cant_pag);
        }

    }

    public function puntosxLider(){
        $whereIn = [];
        $where = [];
        if (\Auth::user()->savk_principal == 1) { //SAVK PRINCIPAL
            $where = [
                ['punto_evaluacion.main_account_id', \Auth::user()->main_account_id]
            ];
        }else if(auth()->user()->id_grupo == 44){// LIDER GRUPO EMPRESA
            $lider = SavkLideresGrupoEmpresa::where('id_usuario', \Auth::user()->id)->pluck('id_grupo_empresa')->toArray();
            $empresas = Unidad::whereIn('centro_operacion_id', $lider)->pluck('id')->toArray();
            $whereIn =  $empresas;
        }else if (Auth::user()->id_grupo == 45) {
            $whereIn = SavkLideresEmpresa::select(
                'punto_evaluacion.id'
            )
            ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'savk_lideres_empresas.id_empresa')
            ->where('savk_lideres_empresas.id_usuario', Auth::user()->id)
            ->get()
            ->pluck('id')->toArray();
        } else if (Auth::user()->id_grupo == 46) {
            $whereIn = SavkLideresZonas::join('grupos_sub_puntos', 'grupos_sub_puntos.grupo_punto_id', 'id_grupos_puntos')
            ->where('id_usuario',auth()->user()->id)
            ->pluck('punto_id')->toArray();
        } else if (Auth::user()->id_grupo == 47) {
            $whereIn = SavkLideresCentroDeCostos::select(
                'savk_lideres_centro_de_costos.id_centro_de_costo'
            )
                ->where('savk_lideres_centro_de_costos.id_usuario', Auth::user()->id)
                ->get()
                ->pluck('id_centro_de_costo')->toArray();
        }else{ //NO TIENE ACCESO
            $where = [
                ['punto_evaluacion.id', null] //Se deja null para que no devuelva ningún valor
            ];
        }

        if (sizeof($whereIn) == 0 && sizeof($where) == 0) {
            //NO TIENE GRUPO EMPRESA ASIGNADO
            $where = [
                ['punto_evaluacion.id', null] //Se deja null para que no devuelva ningún valor
            ];
        }

        $data = $this->getAll($where, false, $whereIn);

        return $data;
    }
}
