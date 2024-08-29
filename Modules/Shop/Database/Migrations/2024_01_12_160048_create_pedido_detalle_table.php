<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('pedido_detalle', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cantidad');
            $table->integer('precio_unitario');
            $table->integer('impuesto')->nullable();
            $table->decimal('valor_total', 18, 2);
            $table->text('observacion')->nullable();
            $table->integer('estado')->default(1)->comment('1');
            $table->unsignedInteger('id_pedido')->length(10);
            $table->foreign('id_pedido')->references('id')->on('pedidos');
            $table->unsignedbigInteger('id_producto')->length(10);
            $table->foreign('id_producto')->references('id')->on('productos');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedido_detalle');
    }
}
