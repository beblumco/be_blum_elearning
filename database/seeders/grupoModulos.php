<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class grupoModulos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '1',
            'modulo_id' => '1',
            'submodulo_id' => '1'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '1',
            'modulo_id' => '2',
            'submodulo_id' => '4'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '1',
            'modulo_id' => '6',
            'submodulo_id' => '6'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '1',
            'modulo_id' => '6',
            'submodulo_id' => '15'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '1',
            'modulo_id' => '6',
            'submodulo_id' => '16'
        ]);

        /////////////////////////////////////////////////
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '44',
            'modulo_id' => '1',
            'submodulo_id' => '1'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '44',
            'modulo_id' => '2',
            'submodulo_id' => '4'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '44',
            'modulo_id' => '6',
            'submodulo_id' => '6'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '44',
            'modulo_id' => '6',
            'submodulo_id' => '15'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '44',
            'modulo_id' => '6',
            'submodulo_id' => '16'
        ]);

        /////////////////////////////////////////////////
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '45',
            'modulo_id' => '1',
            'submodulo_id' => '1'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '45',
            'modulo_id' => '2',
            'submodulo_id' => '4'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '45',
            'modulo_id' => '6',
            'submodulo_id' => '6'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '45',
            'modulo_id' => '6',
            'submodulo_id' => '15'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '45',
            'modulo_id' => '6',
            'submodulo_id' => '16'
        ]);

        /////////////////////////////////////////////////
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '46',
            'modulo_id' => '1',
            'submodulo_id' => '1'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '46',
            'modulo_id' => '2',
            'submodulo_id' => '4'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '46',
            'modulo_id' => '6',
            'submodulo_id' => '6'
        ]);

        DB::table('grupo_modulo')->insert([
            'grupo_id' => '46',
            'modulo_id' => '6',
            'submodulo_id' => '15'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '46',
            'modulo_id' => '6',
            'submodulo_id' => '16'
        ]);
        /////////////////////////////////////////////////
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '47',
            'modulo_id' => '1',
            'submodulo_id' => '1'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '47',
            'modulo_id' => '2',
            'submodulo_id' => '4'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '47',
            'modulo_id' => '6',
            'submodulo_id' => '6'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '47',
            'modulo_id' => '6',
            'submodulo_id' => '15'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '47',
            'modulo_id' => '6',
            'submodulo_id' => '16'
        ]);

        /////////////////////////////////////////////////
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '48',
            'modulo_id' => '1',
            'submodulo_id' => '1'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '48',
            'modulo_id' => '2',
            'submodulo_id' => '4'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '48',
            'modulo_id' => '6',
            'submodulo_id' => '6'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '48',
            'modulo_id' => '6',
            'submodulo_id' => '15'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '48',
            'modulo_id' => '6',
            'submodulo_id' => '16'
        ]);

        /////////////////////////////////////////////////
        // CORDINACIOÓN COMERCIAL
        // INDICADORES
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '39',
            'modulo_id' => '1',
            'submodulo_id' => '1'
        ]);

        // MI ORGANIZACION
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '39',
            'modulo_id' => '2',
            'submodulo_id' => '4'
        ]);

        // ENTRENAMIENTO
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '39',
            'modulo_id' => '6',
            'submodulo_id' => '6'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '39',
            'modulo_id' => '6',
            'submodulo_id' => '15'
        ]);
        DB::table('grupo_modulo')->insert([
            'grupo_id' => '39   ',
            'modulo_id' => '6',
            'submodulo_id' => '16'
        ]);
    }
}
