<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('documentacion_tecnica', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ruta_imagen');
            $table->tinyInteger('estado')->default(1)->comment('1- Activo; 0- Inactivo');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentacion_tecnica');
    }
};
