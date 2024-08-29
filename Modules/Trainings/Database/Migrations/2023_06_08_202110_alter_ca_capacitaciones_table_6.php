<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCaCapacitacionesTable6 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ca_capacitaciones', function (Blueprint $table) {
            $table->tinyInteger('estado_proceso')->unsigned()->after('precio')->nullable()->comment('1:Agendado; 2:Finalizado; Solo para webinars, de resto es null');
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
