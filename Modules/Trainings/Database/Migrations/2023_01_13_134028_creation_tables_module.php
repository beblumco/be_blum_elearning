<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreationTablesModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ca_capacitaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->nullable();
            $table->string('descripcion', 400)->nullable();
            $table->string('imagen', 100)->nullable();
            $table->tinyInteger('estado')->default(1)->comment('0:Inactivo; 1:Activo;');
            $table->tinyInteger('tiempo_minutos')->default(0);
            $table->integer('id_usuario')->unsigned();
            $table->integer('assign')->default(1)->comment('0:Sector; 1:Centro operación;');

            $table->foreign('id_usuario')->references('id')->on('usuarios');

            $table->timestamps();
        });

        Schema::create('ca_asignacion_centro_operacion', function (Blueprint $table) {
            $table->id();

            $table->integer('id_centro_operacion')->unsigned();

            $table->foreign('id_centro_operacion')->references('id')->on('centro_operacion');
            $table->foreignId('id_capacitacion')->constrained('ca_capacitaciones')->onDelete('restrict');

            $table->timestamps();
        });

        Schema::create('ca_asignacion_sector', function (Blueprint $table) {
            $table->id();

            $table->integer('id_sector');

            $table->foreign('id_sector')->references('id')->on('sector');
            $table->foreignId('id_capacitacion')->constrained('ca_capacitaciones')->onDelete('restrict');

            $table->timestamps();
        });

        Schema::create('ca_modulos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('descripcion', 400)->nullable();
            $table->string('imagen', 100)->nullable();
            $table->integer('orden')->default(1);
            $table->decimal('porcentaje_aprueba', 18, 2)->default(75);
            $table->tinyInteger('estado')->default(1)->comment('0:Inactivo; 1:Activo;');

            $table->foreignId('id_capacitacion')->constrained('ca_capacitaciones')->onDelete('restrict');

            $table->timestamps();
        });

        Schema::create('ca_contenido', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('descripcion', 400)->nullable();
            $table->string('ruta_contenido', 700);
            $table->tinyInteger('tipo_contenido')->default(1)->comment('1:Imagen; 2:PDF; 3:Video;');
            $table->integer('orden')->default(1);
            $table->tinyInteger('estado')->default(1)->comment('0:Inactivo; 1:Activo;');

            $table->foreignId('id_modulo')->constrained('ca_modulos')->onDelete('restrict');

            $table->timestamps();
        });

        Schema::create('ca_preguntas', function (Blueprint $table) {
            $table->id();
            $table->string('pregunta', 100);
            $table->integer('orden')->default(1);
            $table->tinyInteger('estado')->default(1)->comment('0:Inactivo (calculable historico); 1:Activo;');

            $table->foreignId('id_modulo')->constrained('ca_modulos')->onDelete('restrict');

            $table->timestamps();
        });

        Schema::create('ca_tipos_respuesta', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('descripcion', 400)->nullable();
            $table->string('icono', 100);
            $table->tinyInteger('estado')->default(1)->comment('0:Inactivo; 1:Activo;');

            $table->timestamps();
        });

        Schema::create('ca_respuestas', function (Blueprint $table) {
            $table->id();
            $table->string('respuesta', 100);
            $table->decimal('ponderado', 18, 2)->default(100);
            $table->tinyInteger('estado')->default(1)->comment('0:Inactivo; 1:Activo;');

            $table->foreignId('id_tipo_respuesta')->constrained('ca_tipos_respuesta')->onDelete('restrict');
            $table->foreignId('id_pregunta')->constrained('ca_preguntas')->onDelete('restrict');

            $table->timestamps();
        });

        Schema::create('ca_evaluacion_iniciada', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_capacitacion')->constrained('ca_capacitaciones')->onDelete('restrict');
            $table->foreignId('id_modulo')->constrained('ca_modulos')->onDelete('restrict');
            $table->integer('id_usuario')->unsigned();
            $table->foreign('id_usuario')->references('id')->on('usuarios');

            $table->timestamp('fecha_inicio')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('fecha_terminada');
            $table->decimal('resultado', 18, 2);
            $table->tinyInteger('certificado')->default(0)->comment('0:No certificado con esta evaluacion; 1:Certificado con esta evaluacion;');
            $table->tinyInteger('certificado_manual')->default(0)->comment('0:No certificado; 1:Certificado;');

            $table->timestamps();
        });

        Schema::create('ca_evaluacion_iniciada_detalle', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_evaluacion_iniciada')->constrained('ca_evaluacion_iniciada')->onDelete('restrict');
            $table->foreignId('id_pregunta')->constrained('ca_preguntas')->onDelete('restrict');
            $table->foreignId('id_respueta')->constrained('ca_respuestas')->onDelete('restrict');

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
        //
    }
}
