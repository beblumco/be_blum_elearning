<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmodulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submodulos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ruta')->nullable();
            $table->tinyInteger('estado')->comment('1:activo|2:inactivo');

            $table->unsignedBigInteger('modulo_id');
            $table->foreign('modulo_id')->references('id')->on('modulos');

            $table->unsignedBigInteger('submodulo_id')->comment('Sino tiene submodulo ó es principal, poner el id propio');
            $table->foreign('submodulo_id')->references('id')->on('submodulos');

            $table->tinyInteger('nivel')->nullable()->default('1');

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
        Schema::dropIfExists('submodulos');
    }
}
