<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class perfilesExternos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('grupo')->insert([
            'id' => '44',
            'nombre' => 'Líder Grupo Empresa',
            'estado' => '1',
            'acceso_savk' => '1',
            'tipo_perfil' => '1'
        ]);
        DB::table('grupo')->insert([
            'id' => '45',
            'nombre' => 'Líder Empresa',
            'estado' => '1',
            'acceso_savk' => '1',
            'tipo_perfil' => '1'
        ]);
        DB::table('grupo')->insert([
            'id' => '46',
            'nombre' => 'Líder Zona',
            'estado' => '1',
            'acceso_savk' => '1',
            'tipo_perfil' => '1'
        ]);
        DB::table('grupo')->insert([
            'id' => '47',
            'nombre' => 'Líder Centro de Costo',
            'estado' => '1',
            'acceso_savk' => '1',
            'tipo_perfil' => '1'
        ]);
        DB::table('grupo')->insert([
            'id' => '48',
            'nombre' => 'Colaborador',
            'estado' => '1',
            'acceso_savk' => '1',
            'tipo_perfil' => '1'
        ]);
    }
}
