<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCaCapacitacionesTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ca_capacitaciones', function (Blueprint $table) {
            $table->tinyInteger('tipo_capacitacion')->default(2)->comment('1: Privada; 2: Publica')->notNullable()->after('descripcion');
            $table->tinyInteger('aplica_certificado')->default(2)->comment('1: Si; 2: No')->notNullable()->after('estado');
            $table->tinyInteger('aplica_evaluacion')->default(2)->comment('1: Si; 2: No')->notNullable()->after('assign');
            $table->tinyInteger('evaluara_por')->comment('1: Capacitación general; 2: Módulos')->nullable()->after('aplica_evaluacion');

            DB::statement("ALTER TABLE ca_capacitaciones MODIFY COLUMN assign INT(11) NULL COMMENT '1:Sector; 2:Centro operación;';");
            DB::statement("ALTER TABLE ca_capacitaciones MODIFY COLUMN permitir_certificacion TINYINT(4) NULL COMMENT '1: Por capacitación general; 2: Por Módulos';");
        });

        Schema::table('ca_preguntas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_capacitacion')->after('id_modulo')->nullable();
            $table->foreign('id_capacitacion')->references('id')->on('ca_capacitaciones');

            DB::statement('ALTER TABLE `ca_preguntas` DROP FOREIGN KEY `ca_preguntas_id_modulo_foreign`');
            DB::statement("ALTER TABLE ca_preguntas MODIFY COLUMN id_modulo BIGINT(20) UNSIGNED NULL;");
            Schema::table('ca_preguntas', function (Blueprint $table) {
                $table->foreign('id_modulo')
                    ->references('id')
                    ->on('ca_modulos');
            });
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
