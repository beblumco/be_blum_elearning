<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->integer('can_to_approve')->default(0)->comment('1: tiene acceso, 0: No tiene acceso');
            $table->integer('can_ajust_pres')->default(0)->comment('1: tiene acceso, 0: No tiene acceso');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Revertir los cambios si es necesario
            $table->dropColumn('can_to_approve');
            $table->dropColumn('can_ajust_pres');
        });
    }
    
}
