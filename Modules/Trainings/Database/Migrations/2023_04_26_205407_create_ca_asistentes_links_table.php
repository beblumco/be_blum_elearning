<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaAsistentesLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ca_asistentes_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_link')->constrained('ca_links')->onDelete('restrict');
            $table->foreignId('id_asistente')->constrained('ca_asistentes')->onDelete('restrict');
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
        Schema::dropIfExists('ca_asistentes_links');
    }
}
