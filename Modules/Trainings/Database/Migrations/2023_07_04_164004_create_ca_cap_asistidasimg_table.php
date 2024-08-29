<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaCapAsistidasimgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ca_cap_asistidasimg', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->foreignId('id_cap_asistida')->constrained('ca_cap_asistidas')->onDelete('restrict');
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
        Schema::dropIfExists('ca_cap_asistidasimg');
    }
}
