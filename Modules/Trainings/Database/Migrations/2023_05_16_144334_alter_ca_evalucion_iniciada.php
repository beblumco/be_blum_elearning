<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCaEvalucionIniciada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ca_evaluacion_iniciada', function (Blueprint $table) {
            DB::statement('ALTER TABLE `ca_evaluacion_iniciada` MODIFY fecha_terminada DATETIME NULL');
            DB::statement('ALTER TABLE `ca_evaluacion_iniciada` MODIFY resultado  DECIMAL(18,2) NULL');

            DB::statement('ALTER TABLE `ca_evaluacion_iniciada` DROP FOREIGN KEY `ca_evaluacion_iniciada_id_modulo_foreign`');
            DB::statement("ALTER TABLE ca_evaluacion_iniciada MODIFY COLUMN id_modulo BIGINT(20) UNSIGNED NULL;");
            $table->foreign('id_modulo')->references('id')->on('ca_modulos');
        });

        Schema::table('ca_evaluacion_iniciada_detalle', function (Blueprint $table) {
            DB::statement('ALTER TABLE `ca_evaluacion_iniciada_detalle` DROP FOREIGN KEY `ca_evaluacion_iniciada_detalle_id_respueta_foreign`');
            DB::statement('ALTER TABLE ca_evaluacion_iniciada_detalle CHANGE COLUMN id_respueta id_respuesta BIGINT(20) UNSIGNED NOT NULL');
            $table->foreign('id_respuesta')->references('id')->on('ca_respuestas');
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
