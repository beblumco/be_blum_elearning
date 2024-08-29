<?php

namespace Modules\Accompaniment\Entities;

use App\Models\CentroOperacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Administration\Entities\SavkLideresCentroDeCostos;
use Modules\Administration\Entities\SavkLideresEmpresa;
use Modules\Administration\Entities\SavkLideresGrupoEmpresa;
use Modules\Administration\Entities\SavkLideresZonas;

class SavkVisita extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'modalidad',
        'id_usuario_registro',
        'id_centro_costo',
        'estado',
        'observacion',
        'interno_externo',
    ];

    protected static function newFactory()
    {
        //return \Modules\Accompaniment\Database\factories\SavkVisitaFactory::new();
    }

    public static function getAll($where, $cant_pag,  $filters)
    {

        $data = self::select(
            'savk_visitas.id',
            DB::raw('DATE_FORMAT(savk_visitas.fecha, "%Y-%m-%d") as fecha'),
            DB::raw('IF(savk_visitas.modalidad = 1, "Presencial", "Virtual") as modalidad'),
            'usuarios.nombre_com',
            'punto_evaluacion.nombre',
            'usuarios.id as idUsuario',
            'punto_evaluacion.id as idCentroCosto',

        )
            ->join('auditoria_iniciadas', 'auditoria_iniciadas.visita_id', 'savk_visitas.id')
            ->join('punto_evaluacion', 'punto_evaluacion.id', 'savk_visitas.id_centro_costo')
            ->join('usuarios', 'usuarios.id', 'savk_visitas.id_usuario_registro')
            ->where($where)
            ->groupBy('id');

        if (isset($filters['fecha']['inicial']) && isset($filters['fecha']['final'])) {
            $data->whereBetween('fecha', [[$filters['fecha']['inicial'], $filters['fecha']['final']]]);
        }

        if (isset($filters['modalidad'])) {
            $data->where('modalidad', $filters['modalidad']);
        }

        if (isset($filters['centro_costo'])) {
            $data->where('punto_evaluacion.id', $filters['centro_costo']);
        }

        if (isset($filters['responsable'])) {
            $data->where('usuarios.id', $filters['responsable']);
        }

        if (isset($filters['nombre_centro_costo'])) {
            $data->where('punto_evaluacion.nombre', 'LIKE', "%" . $filters['nombre_centro_costo'] . "%");
        }

        if (Auth::user()->main_account_id != 1 && Auth::user()->savk_principal == 1) {
            $ids = CentroOperacion::select(
                'punto_evaluacion.id'
            )
                ->join('unidad', 'unidad.centro_operacion_id', 'centro_operacion.id')
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
                ->where('centro_operacion.main_account_id', Auth::user()->main_account_id)->get()
                ->pluck('id');

            $data->whereIn('savk_visitas.id_centro_costo', $ids);
        } else if (Auth::user()->id_grupo == 30 || \Auth::user()->id_grupo == 39) {
            $ids = CentroOperacion::select(
                'punto_evaluacion.id'
            )
                ->join('unidad', 'unidad.centro_operacion_id', 'centro_operacion.id')
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
                ->where('centro_operacion.asesor_id', Auth::user()->id)->get()
                ->pluck('id');

            $data->whereIn('savk_visitas.id_centro_costo', $ids)
                ->orWhere('savk_visitas.id_usuario_registro', Auth::user()->id);
        } else if (Auth::user()->id_grupo == 44) {
            $ids = SavkLideresGrupoEmpresa::select(
                'punto_evaluacion.id'
            )
                ->join('unidad', 'unidad.centro_operacion_id', 'savk_lideres_grupo_empresas.id_grupo_empresa')
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
                ->where('savk_lideres_grupo_empresas.id_usuario', Auth::user()->id)
                ->get()
                ->pluck('id');

            $data->whereIn('savk_visitas.id_centro_costo', $ids);
        } else if (Auth::user()->id_grupo == 45) {
            $ids = SavkLideresEmpresa::select(
                'punto_evaluacion.id'
            )
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'savk_lideres_empresas.id_empresa')
                ->where('savk_lideres_empresas.id_usuario', Auth::user()->id)
                ->get()
                ->pluck('id');

            $data->whereIn('savk_visitas.id_centro_costo', $ids);
        }else if (Auth::user()->id_grupo == 46) {
            $ids = SavkLideresZonas::join('grupos_sub_puntos', 'grupos_sub_puntos.grupo_punto_id', 'id_grupos_puntos')
            ->where('id_usuario',auth()->user()->id)
            ->pluck('punto_id');

            $data->whereIn('savk_visitas.id_centro_costo', $ids);
        } else if (Auth::user()->id_grupo == 47) {
            $ids = SavkLideresCentroDeCostos::select(
                'savk_lideres_centro_de_costos.id_centro_de_costo'
            )
                ->where('savk_lideres_centro_de_costos.id_usuario', Auth::user()->id)
                ->get()
                ->pluck('id_centro_de_costo');

            $data->whereIn('savk_visitas.id_centro_costo', $ids);
        }

        if (isset($filters['tipo'])) {
            $idVisitas = $data->get()->pluck('id');

            $idsFiltrados =  DB::table(
                DB::table('auditoria_iniciadas')
                    ->select(
                        'auditoria_iniciadas.visita_id',
                        DB::raw("
                (CASE
                    WHEN auditoria_iniciadas.auditoria_id = '65' THEN (
                        SELECT rd.descripcion
                        FROM respuestas_auditoria_iniciadas AS ri
                        JOIN respuesta_detalle rd ON ri.respuesta_id = rd.id
                        WHERE ri.auditoria_iniciada_id = auditoria_iniciadas.id
                    )
                    WHEN auditoria_iniciadas.auditoria_id <> '65' THEN auditorias.nombre
                END) AS nombre_tipo
                ")
                    )
                    ->join('auditorias', 'auditorias.id', 'auditoria_iniciadas.auditoria_id')
                    ->whereIn('auditoria_iniciadas.visita_id', $idVisitas),
                'nombres'
            )
                ->select('nombres.visita_id', 'nombres.nombre_tipo')
                ->where('nombres.nombre_tipo',  $filters['tipo'])
                ->get()
                ->unique('visita_id')
                ->pluck('visita_id');

            $data->whereIn('savk_visitas.id', $idsFiltrados);
        }


        if ($cant_pag == null) {
            $data = $data->orderBy('fecha', 'DESC')->get();
        } else {
            $data = $data->orderBy('fecha', 'DESC')->paginate($cant_pag);
        }

        return $data;
    }

    public static function getTipoAuditorias()
    {
        $data =   DB::table('auditoria_iniciadas')
            ->select(
                DB::raw("
                (CASE
                    WHEN auditoria_iniciadas.auditoria_id = '65' THEN (
                        SELECT rd.descripcion
                        FROM respuestas_auditoria_iniciadas AS ri
                        JOIN respuesta_detalle rd ON ri.respuesta_id = rd.id
                        WHERE ri.auditoria_iniciada_id = auditoria_iniciadas.id
                    )
                    WHEN auditoria_iniciadas.auditoria_id <> '65' THEN auditorias.nombre
                END) AS name
                ")
            )
            ->join('auditorias', 'auditorias.id', 'auditoria_iniciadas.auditoria_id')
            ->join('savk_visitas', 'savk_visitas.id', 'auditoria_iniciadas.visita_id');
        if (Auth::user()->main_account_id != 1 && Auth::user()->savk_principal == 1) {
            $ids = CentroOperacion::select(
                'punto_evaluacion.id'
            )
                ->join('unidad', 'unidad.centro_operacion_id', 'centro_operacion.id')
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
                ->where('centro_operacion.main_account_id', Auth::user()->main_account_id)->get()
                ->pluck('id');

            $data->whereIn('savk_visitas.id_centro_costo', $ids);
        } else if (Auth::user()->id_grupo == 30 || \Auth::user()->id_grupo == 39) {
            $ids = CentroOperacion::select(
                'punto_evaluacion.id'
            )
                ->join('unidad', 'unidad.centro_operacion_id', 'centro_operacion.id')
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
                ->where('centro_operacion.asesor_id', Auth::user()->id)->get()
                ->pluck('id');

            $data->whereIn('savk_visitas.id_centro_costo', $ids)
                ->orWhere('savk_visitas.id_usuario_registro', Auth::user()->id);
        } else if (Auth::user()->id_grupo == 44) {
            $ids = SavkLideresGrupoEmpresa::select(
                'punto_evaluacion.id'
            )
                ->join('unidad', 'unidad.centro_operacion_id', 'savk_lideres_grupo_empresas.id_grupo_empresa')
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
                ->where('savk_lideres_grupo_empresas.id_usuario', Auth::user()->id)
                ->get()
                ->pluck('id');

            $data->whereIn('savk_visitas.id_centro_costo', $ids);
        } else if (Auth::user()->id_grupo == 45) {
            $ids = SavkLideresEmpresa::select(
                'punto_evaluacion.id'
            )
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'savk_lideres_empresas.id_empresa')
                ->where('savk_lideres_empresas.id_usuario', Auth::user()->id)
                ->get()
                ->pluck('id');

            $data->whereIn('savk_visitas.id_centro_costo', $ids);
        } else if (Auth::user()->id_grupo == 47) {
            $ids = SavkLideresCentroDeCostos::select(
                'savk_lideres_centro_de_costos.id_centro_de_costo'
            )
                ->where('savk_lideres_centro_de_costos.id_usuario', Auth::user()->id)
                ->get()
                ->pluck('id_centro_de_costo');

            $data->whereIn('savk_visitas.id_centro_costo', $ids);
        }
        $data = $data->get()->unique('name');

        $response = [];

        foreach ($data as $key => $value) {
            array_push($response, ['id' => $key+1, 'name' => $value->name]); // fallo, Se le suma 1 al key por falla del SelectSavk
        }

        return $response;
    }
}
