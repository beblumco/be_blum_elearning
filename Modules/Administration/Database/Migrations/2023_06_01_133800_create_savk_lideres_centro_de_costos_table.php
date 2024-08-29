<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavkLideresCentroDeCostosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savk_lideres_centro_de_costos', function (Blueprint $table) {
            $table->id();
            $table->integer('id_centro_de_costo')->unsigned();
            $table->foreign('id_centro_de_costo')->references('id')->on('punto_evaluacion');
            $table->integer('id_usuario')->unsigned();
            $table->foreign('id_usuario')->references('id')->on('usuarios'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('savk_lideres_centro_de_costos');
    }
}
