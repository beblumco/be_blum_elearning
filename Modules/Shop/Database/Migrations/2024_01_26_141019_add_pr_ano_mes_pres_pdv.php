<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrAnoMesPresPdv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('pres_anos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion');
            $table->tinyInteger('estado')->default(1)->comment('1: Activo; 0: Inactivo');
            $table->timestamps();
        });

        Schema::create('pres_meses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion');
            $table->tinyInteger('estado')->default(1)->comment('1: Activo; 0: Inactivo');
            $table->timestamps();
        });

        Schema::create('pres_pdv_presupuesto', function (Blueprint $table) 
        {
            $table->increments('id');
            $table->unsignedInteger('id_pdv')->length(10);
            $table->foreign('id_pdv')->references('id')->on('punto_evaluacion');
            $table->unsignedInteger('id_ano')->length(10);
            $table->foreign('id_ano')->references('id')->on('pres_anos');
            $table->unsignedInteger('id_mes')->length(10);
            $table->foreign('id_mes')->references('id')->on('pres_meses');
            $table->decimal('presupuesto', 18, 2);
            $table->tinyInteger('estado')->default(1)->comment('1: Activo; 0: Inactivo');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pres_pdv_presupuesto');
        Schema::dropIfExists('pres_anos');
        Schema::dropIfExists('pres_meses');
    }
}
