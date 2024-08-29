<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class PresAnosMesesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         // Seeder para la tabla pres_anos
         \DB::table('pres_anos')->insert([
            ['descripcion' => '2024', 'estado' => 1],
            ['descripcion' => '2025', 'estado' => 0],
            ['descripcion' => '2026', 'estado' => 0],
            // Agrega más años según sea necesario
        ]);

        // Seeder para la tabla pres_meses
        \DB::table('pres_meses')->insert([
            ['descripcion' => 'Enero', 'estado' => 1],
            ['descripcion' => 'Febrero', 'estado' => 1],
            ['descripcion' => 'Marzo', 'estado' => 1],
            ['descripcion' => 'Abril', 'estado' => 1],
            ['descripcion' => 'Mayo', 'estado' => 1],
            ['descripcion' => 'Junio', 'estado' => 1],
            ['descripcion' => 'Julio', 'estado' => 1],
            ['descripcion' => 'Agosto', 'estado' => 1],
            ['descripcion' => 'Septiembre', 'estado' => 1],
            ['descripcion' => 'Octubre', 'estado' => 1],
            ['descripcion' => 'Noviembre', 'estado' => 1],
            ['descripcion' => 'Diciembre', 'estado' => 1]
        ]);
    }
}
