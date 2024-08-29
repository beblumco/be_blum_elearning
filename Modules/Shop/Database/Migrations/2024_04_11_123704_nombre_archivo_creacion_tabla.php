<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NombreArchivoCreacionTabla extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cg1_nombre_archivo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_pedido');
            $table->string('cliente');
            $table->string('consecutivo');
            $table->string('oc')->nullable();
            $table->timestamp('fecha_solicitud')->nullable();
            $table->string('estado')->default(1)->comment("1=descargado ; 2=cargado ; 3= error archivo");
            $table->string('unidad')->comment("Consecutivo para el nombre PEDIDOSS.PEx");
            $table->string('modo_cargue')->nullable()->comment("0= automatico 1=manual (Este campo se usa para identificar el modo en que se cargo la factura)");
            $table->timestamps(); // Agrega los campos created_at y updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('cg1_nombre_archivo');
    }
}
