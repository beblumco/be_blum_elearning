<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavkLideresGrupoEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savk_lideres_grupo_empresas', function (Blueprint $table) {
            $table->id();
            $table->integer('id_grupo_empresa')->unsigned();
            $table->foreign('id_grupo_empresa')->references('id')->on('centro_operacion');
            $table->integer('id_usuario')->unsigned();
            $table->foreign('id_usuario')->references('id')->on('usuarios');                    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('savk_lideres_grupo_empresas');
    }
}
