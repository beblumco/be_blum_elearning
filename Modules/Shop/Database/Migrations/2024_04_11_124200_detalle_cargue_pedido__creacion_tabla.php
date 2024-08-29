<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DetalleCarguePedidoCreacionTabla extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cg1_detalle_cargue_pedido', function (Blueprint $table) {
            $table->id();
            $table->string('producto_referencia');
            $table->decimal('valor', 10, 2); // Decimal con 10 dígitos en total y 2 decimales
            $table->foreignId('nombre_archivo_id')->constrained('cg1_nombre_archivo'); // Referencia a la tabla pedidos
            $table->string('oc')->nullable();
            $table->string('estado')->default(0)->comment('1=cargado cg1 ; 2= no cargado cg1');
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cg1_detalle_cargue_pedido');
    }
}
