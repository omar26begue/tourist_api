<?php

use App\Models\Usuarios;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tourist_usuarios')->insert(array(
            'idusuario' => Str::uuid(),
            'rol' => Usuarios::ROL_ADMIN,
            'nombres' => 'administrador',
            'apellido1' => 'sistema',
            'apellido2' => 'tourist',
            'email' => 'omar26begue@gmail.com',
            'activo' => Usuarios::USUARIO_ACTIVO,
            'verificado' => Usuarios::USUARIO_VERIFICADO,
            'password' => Hash::make('123456789'),
            'fecha_creacion' => Carbon::now()
        ));

        DB::table('tourist_usuarios')->insert(array(
            'idusuario' => Str::uuid(),
            'rol' => Usuarios::ROL_ADMIN,
            'nombres' => 'erika',
            'apellido1' => 'barcia',
            'email' => 'erikabarcia5@gmail.com',
            'activo' => Usuarios::USUARIO_ACTIVO,
            'verificado' => Usuarios::USUARIO_VERIFICADO,
            'password' => Hash::make('erika5'),
            'fecha_creacion' => Carbon::now()
        ));
    }
}
