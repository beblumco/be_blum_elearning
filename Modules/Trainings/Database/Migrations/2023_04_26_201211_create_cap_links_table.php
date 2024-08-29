<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCapLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ca_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_capacitacion')->nullable()->constrained('ca_capacitaciones')->onDelete('restrict');
            $table->foreignId('id_modulo')->nullable()->constrained('ca_modulos')->onDelete('restrict');
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
        Schema::dropIfExists('ca_links');
    }
}
