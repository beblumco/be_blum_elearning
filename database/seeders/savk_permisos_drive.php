<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SavkPermisos;

class savk_permisos_drive extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            'dri-dri-cambiar_grupo_empresa',
            'dri-dri-subir_archivo',
            'dri-dri-nueva_carpeta',
            'dri-dri-cambiar_nombre',
            'dri-dri-propiedades',
            'dri-dri-descargar',
            'dri-dri-eliminar',
            'dri-dri-compartir',
        ];

        $permisos = [];

        $id = 46;
        foreach ($items as $index => $item) {
            $permisos[] = [
                'id' => $id, // Establece el valor del id manualmente
                'evento' => $item,
            ];

            $id++;
        }

        SavkPermisos::insert($permisos);
    }
}
