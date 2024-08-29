<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaCapacitacionesAsistidasAsisPrivadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ca_capacitaciones_asistidas_asis_privadas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_capacitacion_asistida')->unsigned();
            $table->foreign('id_capacitacion_asistida', 'fk_ca_asis_priva_id_cap')->references('id')->on('ca_cap_asistidas');
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
        Schema::dropIfExists('ca_capacitaciones_asistidas_asis_privadas');
    }
}
