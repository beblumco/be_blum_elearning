<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCaCapacitacionesAsistidasAsisPrivadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ca_capacitaciones_asistidas_asis_privadas', function (Blueprint $table) {
            DB::statement('ALTER TABLE `ca_capacitaciones_asistidas_asis_privadas` DROP FOREIGN KEY `ca_capacitaciones_asistidas_asis_privadas_id_usuario_foreign`');
            DB::statement("ALTER TABLE ca_capacitaciones_asistidas_asis_privadas MODIFY COLUMN id_usuario INT(10) UNSIGNED NULL;");
            $table->foreign('id_usuario')->references('id')->on('usuarios');

            $table->bigInteger('id_asistente')->unsigned()->nullable()->after('id_usuario');
            $table->foreign('id_asistente')->references('id')->on('ca_asistentes');
        });

        Schema::rename('ca_capacitaciones_asistidas_asis_privadas', 'ca_capacitaciones_asistidas_asistentes');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
