<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaAsistentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ca_asistentes', function (Blueprint $table) {
            $table->id();

            $table->integer('documento')->nullable();
            $table->string('nombre', 200);
            $table->string('email', 200);
            $table->string('empresa', 200);

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
        Schema::dropIfExists('ca_asistentes');
    }
}
