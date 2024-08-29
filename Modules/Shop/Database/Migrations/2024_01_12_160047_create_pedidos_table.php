<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('pedidos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('consecutivo');
            $table->decimal('subtotal', 18, 2);
            $table->decimal('total', 18, 2);
            $table->dateTime('fecha_solicitud')->nullable();
            $table->dateTime('fecha_despacho')->nullable();
            $table->text('observacion')->nullable();
            $table->unsignedInteger('id_tipo_factura')->length(10)->nullable();
            $table->foreign('id_tipo_factura')->references('id')->on('tipos_factura');
            $table->string('numero_factura')->nullable();
            $table->tinyInteger('estado')->default(3)->comment('0- Cancelado; 1- Sin confirmacion; 2- Solicitado 3- Sin solicitar');
            $table->integer('id_solicitud_materiales')->nullable()->comment('Parece importante pero no se con precisión de que trata');
            $table->foreign('id_solicitud_materiales')->references('id')->on('av_material_solicitud');
            $table->unsignedInteger('id_punto_evaluacion')->length(10);
            $table->foreign('id_punto_evaluacion')->references('id')->on('punto_evaluacion');
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
        Schema::dropIfExists('pedidos');
    }
}
