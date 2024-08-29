<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSavkDriveCarpetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('savk_drive_carpetas', function (Blueprint $table) {
            $table->tinyInteger('tipo')->comment('1:DRIVE, 2:ENTORNO DE APRENDIZAJE')->nullable()->after('propietario_nombre');
        });

        Schema::table('savk_drive_archivos', function (Blueprint $table) {
            $table->tinyInteger('tipo_drive')->comment('1:DRIVE, 2:ENTORNO DE APRENDIZAJE')->nullable();
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
