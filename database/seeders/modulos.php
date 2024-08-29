<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class modulos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // INSERT DE MODULOS
        DB::table('modulos')->insert([
            'nombre' => 'Panel de Información',
            'ruta' => 'dashboard',
            'icono' => 'flaticon-381-networking',
            'estado' => '1',
            'orden' => '1'
        ]);
        DB::table('modulos')->insert([
            'nombre' => 'Mi organización',
            'ruta' => 'administration',
            'icono' => 'flaticon-381-home-1',
            'estado' => '1',
            'orden' => '2'
        ]);
        DB::table('modulos')->insert([
            'nombre' => 'Drive',
            'ruta' => 'drive',
            'icono' => 'flaticon-381-cloud',
            'estado' => '1',
            'orden' => '3'
        ]);
        DB::table('modulos')->insert([
            'nombre' => 'Catálogo',
            'ruta' => 'catalogo',
            'icono' => 'flaticon-381-battery-6',
            'estado' => '1',
            'orden' => '4'
        ]);
        DB::table('modulos')->insert([
            'nombre' => 'Reportes',
            'ruta' => 'reportes',
            'icono' => 'flaticon-381-list-1',
            'estado' => '1',
            'orden' => '5'
        ]);
        DB::table('modulos')->insert([
            'nombre' => 'Capacitaciones',
            'ruta' => 'capacitaciones',
            'icono' => 'flaticon-381-video-clip',
            'estado' => '1',
            'orden' => '6'
        ]);


        ///////////////////// INSERT DE SUBMODULOS//////////////////////////////////////////////////
        DB::table('submodulos')->insert([
            'nombre' => 'Panel de Información',
            'ruta' => 'principal_index',
            'estado' => '1',
            'modulo_id' => '1',
            'submodulo_id' => '1',
            'nivel' => '1',
        ]);
        DB::table('submodulos')->insert([
            'nombre' => 'Reportes',
            'ruta' => 'report_index',
            'estado' => '1',
            'modulo_id' => '5',
            'submodulo_id' => '2',
            'nivel' => '1',
        ]);
        DB::table('submodulos')->insert([
            'nombre' => 'Drive',
            'ruta' => 'drive_index',
            'estado' => '1',
            'modulo_id' => '3',
            'submodulo_id' => '3',
            'nivel' => '1',
        ]);
        DB::table('submodulos')->insert([
            'nombre' => 'Mi organización',
            'ruta' => 'my_organization_index',
            'estado' => '1',
            'modulo_id' => '2',
            'submodulo_id' => '4',
            'nivel' => '1',
        ]);
        DB::table('submodulos')->insert([
            'nombre' => 'Catálogo',
            'ruta' => 'catalogo_index',
            'estado' => '1',
            'modulo_id' => '4',
            'submodulo_id' => '5',
            'nivel' => '1',
        ]);
        DB::table('submodulos')->insert([
            'nombre' => 'Capacitaciones',
            'ruta' => 'trainings_index',
            'estado' => '1',
            'modulo_id' => '6',
            'submodulo_id' => '6',
            'nivel' => '1',
        ]);
        DB::table('submodulos')->insert([
            'nombre' => 'Administrar',
            'ruta' => 'trainings_admin_index',
            'estado' => '1',
            'modulo_id' => '6',
            'submodulo_id' => '7',
            'nivel' => '1',
        ]);
    }
}
