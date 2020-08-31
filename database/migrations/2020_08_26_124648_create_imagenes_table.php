<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tourist_imagenes', function (Blueprint $table) {
            $table->uuid('idimagenes')->primary()->comment('Identificador');
            $table->uuid('idsitio');
            $table->string('nombre');
            $table->integer('calificacion')->default(0)->comment('Calificacion');
            $table->float('lat')->default(-7.94);
            $table->float('lon')->default(-76.82);
            $table->integer('zoom')->default(8);

            $table->foreign('idsitio')->references('idsitio')
                ->on('tourist_sitios')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tourist_imagenes');
    }
}
