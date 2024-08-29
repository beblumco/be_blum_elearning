<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('ca_evaluacion_iniciada_detalle', function(Blueprint $table) {
        //     $table->renameColumn('id_respueta', 'id_respuesta');
        // });

        Schema::table('ca_evaluacion_iniciada', function($table)
        {
            // $table->dateTime('fecha_terminada')->nullable()->change();
            // $table->decimal('resultado', 18, 2)->nullable()->change();
            $table->tinyInteger('last_approved')->default(0)->comment('0: No Ganó el módulo; 1:Si ganó el módulo;');
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
