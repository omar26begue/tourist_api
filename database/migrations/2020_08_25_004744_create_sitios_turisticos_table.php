<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitiosTuristicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tourist_sitios', function (Blueprint $table) {
            $table->uuid('idsitio')->primary()->comment('Identificador sitio turistico');
            $table->string('nombre')->comment('Nombre del sitio torustico');
            $table->string('ubicacion')->comment('Ubicación');
            $table->float('distancia')->comment('Distancia');
            $table->integer('tiempo')->comment('Tiempo e viaje');
            $table->integer('poblacion')->comment('Población');
            $table->text('descripcion')->comment('Descripcion');
            $table->time('hora_apertura')->comment('Hora de apertura');
            $table->time('hora_cierre')->comment('Hora de cierre');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tourist_sitios');
    }
}
