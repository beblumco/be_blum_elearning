<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavkVisitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savk_visitas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->tinyInteger('modalidad')->default(1)->comment('1: Presencial, 2: Virtual');
            $table->integer('id_usuario_registro')->unsigned();
            $table->foreign('id_usuario_registro')->references('id')->on('usuarios');
            $table->integer('id_centro_costo')->unsigned();
            $table->foreign('id_centro_costo')->references('id')->on('punto_evaluacion');
            $table->tinyInteger('estado')->default(1)->comment('1: Pendiente, 2: Cerrada');
            $table->text('observacion')->nullable();
            $table->tinyInteger('interno_externo')->comment('1: Interno, 2: Externo');
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
        Schema::dropIfExists('savk_visitas');
    }
}
