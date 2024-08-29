<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCaEvalucionIniciada2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ca_evaluacion_iniciada', function (Blueprint $table) {
            DB::statement('ALTER TABLE `ca_evaluacion_iniciada` DROP FOREIGN KEY `ca_evaluacion_iniciada_id_usuario_foreign`');
            DB::statement("ALTER TABLE ca_evaluacion_iniciada MODIFY COLUMN id_usuario INT(10) UNSIGNED NULL;");
            $table->foreign('id_usuario')->references('id')->on('usuarios');

            $table->bigInteger('id_asistente')->unsigned()->nullable()->after('id_usuario');
            $table->foreign('id_asistente')->references('id')->on('ca_asistentes');
        });
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
