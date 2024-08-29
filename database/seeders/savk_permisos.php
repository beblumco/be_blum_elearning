<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SavkPermisos;


class savk_permisos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            'ind-ind-ver_indicadores',
            'org-mio-usuarios',
            'org-mio-centro_costo',
            'org-mio-empresas',
            'org-mio-grupo_empresa',
            'org-mio-invitar_usuarios',
            'org-mio-crear_usuarios',
            'org-mio-editar_usuarios',
            'org-mio-eliminar_usuarios',
            'org-mio-crear_centro_costo',
            'org-mio-crear_empresa',
            'org-mio-crear_grupo_empresa',
            'org-mio-editar_centro_costo',
            'org-mio-eliminar_centro_costo',
            'org-mio-editar_empresas',
            'org-mio-eliminar_empresas',
            'org-mio-editar_grupo_empresas',
            'org-mio-eliminar_grupo_empresas',
            'ent-ele-mi_plan',
            'ent-ele-mis_capacitaciones',
            'ent-ele-mis_certificados',
            'ent-ele-certificados_equipo',
            'ent-ele-crear_curso',
            'ent-ele-iniciar_capacitacion',
            'ent-ele-ver_capacitacion',
            'ent-ele-modificar_capacitacion',
            'ent-ele-inactivar_capacitacion',
            'ent-ele-compartir_capacitacion',
            'ent-ele-eliminar_capacitacion',
            'ent-asi-crear_asistida',
            'ent-asi-reporte',
            'ent-asi-asistentes',
            'ent-asi-link',
            'ent-asi-descargar_certificados',
            'ent-asi-descargar_asistentes',
            'ent-asi-cargar_asistentes',
            'ent-web-mis_webinars',
            'ent-web-agenda_eventos',
            'ent-web-crear_webinar',
            'org-mio-propuesta_valor'
        ];

        $permisos = [];

        foreach ($items as $index => $item) {
            $permisos[] = [
                'id' => $index+1, // Establece el valor del id manualmente
                'evento' => $item,
            ];
        }

        SavkPermisos::insert($permisos);
    }
}
