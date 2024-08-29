<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            DB::statement("ALTER TABLE usuarios MODIFY COLUMN ultimo_acceso DATETIME NULL");

            DB::statement("SET sql_mode = ''");
            DB::statement("UPDATE usuarios SET ultimo_acceso = NULL WHERE ultimo_acceso = '0000-00-00 00:00:00'");
            DB::statement("SET sql_mode = 'STRICT_ALL_TABLES'");

            $table->bigInteger('id_seccion')->nullable()->unsigned();
            $table->foreign('id_seccion')->references('id')->on('savk_secciones')->onDelete('restrict');
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
