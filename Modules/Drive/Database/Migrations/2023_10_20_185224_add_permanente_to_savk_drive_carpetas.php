<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermanenteToSavkDriveCarpetas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('savk_drive_carpetas', function (Blueprint $table) {
            $table->tinyInteger('permanente')->default(2)->comment('1 = Permanente, 2 = No permanente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('savk_drive_carpetas', function (Blueprint $table) {
        });
    }
}
