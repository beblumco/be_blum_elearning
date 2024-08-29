<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavkLiederesZonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savk_liederes_zonas', function (Blueprint $table) {
            $table->id();

            $table->integer('id_grupos_puntos')->unsigned();
            $table->foreign('id_grupos_puntos')->references('id')->on('grupos_puntos');
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
        Schema::dropIfExists('savk_liederes_zonas');
    }
}
