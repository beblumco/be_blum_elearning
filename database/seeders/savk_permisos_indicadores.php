<?php

namespace Database\Seeders;

use App\Models\SavkPermisos;
use Illuminate\Database\Seeder;

class savk_permisos_indicadores extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            'indica-acompanamiento'
        ];

        $permisos = [];

        $id = 45;
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
