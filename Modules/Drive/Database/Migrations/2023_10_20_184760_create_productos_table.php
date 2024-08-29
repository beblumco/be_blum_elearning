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

        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('referencia')->comment('KT...');
            $table->bigInteger('impuesto');
            $table->tinyInteger('estado')->default(1);
            $table->string('imagen');
            $table->bigInteger('id_categoria')->unsigned();
            $table->foreign('id_categoria')->references('id')->on('categorias_productos');
            $table->bigInteger('id_unidades_empaque')->unsigned();
            $table->foreign('id_unidades_empaque')->references('id')->on('unidades_empaque');
            $table->bigInteger('id_linea')->unsigned();
            $table->foreign('id_linea')->references('id')->on('linea');
            $table->bigInteger('id_etiqueta')->unsigned();
            $table->foreign('id_etiqueta')->references('id')->on('etiqueta_documentacion');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
