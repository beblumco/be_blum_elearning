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

        Schema::create('documentacion_producto', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_documentacion')->unsigned();
            $table->foreign('id_documentacion')->references('id')->on('documentacion_tecnica');
            $table->bigInteger('id_producto')->unsigned();
            $table->foreign('id_producto')->references('id')->on('productos');
            $table->bigInteger('id_etiqueta')->unsigned();
            $table->foreign('id_etiqueta')->references('id')->on('etiqueta_documentacion');
            $table->bigInteger('id_drive_archivo')->unsigned();
            $table->foreign('id_drive_archivo')->references('id')->on('savk_drive_archivos');
            $table->string('ruta_documento');

            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentacion_producto');
    }
};
