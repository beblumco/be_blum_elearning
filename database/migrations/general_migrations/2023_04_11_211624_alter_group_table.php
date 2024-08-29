<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grupo', function (Blueprint $table) {
            $table->tinyInteger('acceso_savk')->default(0)->after('estado')->comment('0: No permitir acceso; 1: Permitir acceso');
            $table->tinyInteger('tipo_perfil')->default(0)->after('acceso_savk')->comment('0: Perfil interno; 1: Perfil externo');
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
