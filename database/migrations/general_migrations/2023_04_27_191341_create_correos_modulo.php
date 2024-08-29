<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorreosModulo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correos_modulo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evento_modulo_id');
            $table->foreign('evento_modulo_id')->references('id')->on('eventos_modulo');
            $table->string('correo');
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
        Schema::dropIfExists('correos_modulo');
    }
}
