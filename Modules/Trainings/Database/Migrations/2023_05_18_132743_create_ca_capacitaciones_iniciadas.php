<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaCapacitacionesIniciadas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ca_capacitaciones_iniciadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_capacitacion')->constrained('ca_capacitaciones')->onDelete('restrict');
            $table->foreignId('id_modulo')->constrained('ca_modulos')->onDelete('restrict');
            $table->integer('id_usuario')->unsigned();
            $table->foreign('id_usuario')->references('id')->on('usuarios');
            $table->timestamp('fecha_inicio')->default(\DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('ca_capacitaciones_iniciadas');
    }
}
