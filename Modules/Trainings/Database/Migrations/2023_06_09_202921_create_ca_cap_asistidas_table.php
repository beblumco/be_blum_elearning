<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaCapAsistidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ca_cap_asistidas', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha_agendamiento');
            $table->foreignId('id_capacitacion')->nullable()->constrained('ca_capacitaciones')->onDelete('restrict');
            $table->integer('id_asesor')->unsigned();
            $table->foreign('id_asesor')->references('id')->on('usuarios');
            $table->tinyInteger('modalidad')->comment('1:virtual, 2:presencial');
            $table->tinyInteger('tipo')->comment('1:publica, 2:privada');
            $table->integer('id_cliente')->unsigned()->nullable();
            $table->foreign('id_cliente')->references('id')->on('punto_evaluacion');
            $table->integer('duracion')->unsigned();
            $table->string('link');

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
        Schema::dropIfExists('ca_cap_asistidas');
    }
}
