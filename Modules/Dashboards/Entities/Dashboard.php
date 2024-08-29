<?php

namespace Modules\Dashboards\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Dashboard extends Model
{
    private function setTimeName()
    {
        DB::statement("SET lc_time_names = 'es_ES'");
    } 

    public function GetValueProposalGP($idCompanyGroup)
    {
        $this->setTimeName();
        $materiales = DB::table("av_material_solicitud AS ams")->selectRaw("
              ams.id as AUDITORIA_INI_ID, 'Materiales' as AUDITORIA, ams.departamento as PDV, ams.empresa as EMPRESA,
              ams.grupo_empresa as GRUPO_EMPRESA, u2.nombre_com as RESPONSABLE, IFNULL(ams.dep_observacion, 'Sin ibservación') as OBSERVACION_GENERAL,
              DATE_FORMAT(ams.fecha_entrega, '%M %d %Y') as FECHA_FIN, TIME_FORMAT('10:00:00', '%r') as HORA_FIN 
              ")->join("usuarios AS u2", "u2.id", "ams.usuario_id")->where("ams.estado", 8);

        $data = DB::table('auditorias AS a')->selectRaw("
            ai.id as AUDITORIA_INI_ID, 
            IF(a.id = 65, CONCAT(a.nombre,' - ', (SELECT rds.descripcion FROM respuestas_auditoria_iniciadas rais INNER JOIN respuesta_detalle rds ON rais.respuesta_id = rds.id WHERE rais.auditoria_iniciada_id = ai.id LIMIT 1)) , a.nombre) as AUDITORIA, 
            pe.nombre as PDV, 
            u.nombre as EMPRESA,
            co.nombre as GRUPO_EMPRESA, ai.responsable as RESPONSABLE, IFNULL(og.observacion, 'Sin ibservación') as OBSERVACION_GENERAL,
            DATE_FORMAT(ai.fecha_fin_auditoria, '%M %d %Y') as FECHA_FIN, TIME_FORMAT(ai.hora_fin, '%r') as HORA_FIN, usuarios.nombre_com AS EVALUADOR
            ")
            ->join("auditoria_iniciadas AS ai", "ai.auditoria_id", "a.id")
            ->join("punto_evaluacion AS pe", "pe.id", "ai.punto_id")
            ->join("unidad AS u", "u.id", "pe.unidad_id")
            ->join("centro_operacion AS co", "co.id", "u.centro_operacion_id")
            ->Join('usuarios', 'ai.usuario_id', '=', 'usuarios.id')
            ->leftJoin("observacion_general AS og", "og.auditoria_iniciada_id", "ai.id")
            ->where([
                ["ai.estado", 4],
                ['co.id', '=', $idCompanyGroup]
            ])
            ->whereRaw('(SELECT rds.id FROM respuestas_auditoria_iniciadas rais INNER JOIN respuesta_detalle rds ON rais.respuesta_id = rds.id WHERE rais.auditoria_iniciada_id = ai.id LIMIT 1) != 4488')
            ->orderBy("ai.id", "DESC")->paginate(40);

        return $data;
    }
}
