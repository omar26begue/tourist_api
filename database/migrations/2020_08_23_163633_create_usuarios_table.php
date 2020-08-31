<?php

use App\Models\Usuarios;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tourist_usuarios', function (Blueprint $table) {
            $table->uuid('idusuario')->primary()->comment('Identificador del usuario');
            $table->string('rol')->default(Usuarios::ROL_TOURIST)->comment('Rol del usuario');
            $table->string('nombres')->comment('Nombre del usuario');
            $table->string('apellido1')->comment('Apellidos del usuario');
            $table->string('apellido2')->nullable()->comment('Apellido del usuario');
            $table->string('email')->unique()->comment('Dirección del correo electronico');
            $table->string('activo')->default(Usuarios::USUARIO_ACTIVO)->comment('Estado del usuario');
            $table->string('verificado')->default(Usuarios::USUARIO_VERIFICADO)->comment('Verifiación del usuario');
            $table->string('password')->comment('Contraseña');
            $table->date('fecha_creacion')->comment('Fecha de creación del usuario');
            $table->dateTime('fecha_ultimo_acceso')->nullable()->comment('Fecha del ultimo acceso del usuario');
            $table->string('photo')->default('perfil.png')->comment('Foto de perfil del usuario');
            $table->string('token')->nullable()->comment('Token del usuario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tourist_usuarios');
    }
}
