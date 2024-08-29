<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaPreguntasUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ca_preguntas_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_capacitacion')->constrained('ca_capacitaciones')->onDelete('restrict');
            $table->foreignId('id_modulo')->constrained('ca_modulos')->onDelete('restrict')->nullable();
            $table->integer('id_usuario')->unsigned();
            $table->foreign('id_usuario')->references('id')->on('usuarios');
            $table->string('pregunta', 500);
            $table->string('respuesta', 500)->nullable()->default(null);
            $table->tinyInteger('estado')->default(0)->comment('0:Pendiente; 1:Resuelta;');
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
        Schema::dropIfExists('ca_preguntas_usuarios');
    }
}
