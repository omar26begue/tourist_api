<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComentariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tourist_comentarios', function (Blueprint $table) {
            $table->uuid('idcomentarios')->primary()->comment('Identificador comentario');
            $table->text('texto')->comment('Texto del comentario');
            $table->uuid('idusuario')->comment('identificador del usuario');
            $table->dateTime('fecha')->comment('Fecha de creación del comentario');

            $table->foreign('idusuario')->references('idusuario')
                ->on('tourist_usuarios')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tourist_comentarios');
    }
}
