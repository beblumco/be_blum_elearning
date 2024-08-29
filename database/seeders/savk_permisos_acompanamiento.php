<?php

namespace Database\Seeders;

use App\Models\SavkPermisos;
use Illuminate\Database\Seeder;

class savk_permisos_acompanamiento extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            'accomp-crear_acompañamiento',
            'accomp-reportes',
            'accomp-resultado',
            'accomp-preguntas'
        ];

        $permisos = [];

        $id = 41;
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
