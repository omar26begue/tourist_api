<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Mail\UsersCreate;
use App\Models\Usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UsuariosController extends ApiController
{
    public function __construct()
    {
    }

    /**
     * @OA\Get(
     *     path="/usuarios/list",
     *     summary="Listado de usuarios",
     *     tags={"Usuarios"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *
     *     @OA\Response(
     *         response="400",
     *         description="Failed",
     *     ),
     *
     *     security={{"apiAuth": {} }},
     *
     *     deprecated=false
     * )
     */
    public function list_usuarios() {
        try {
            if (auth()->user()->rol != Usuarios::ROL_ADMIN) {
                return $this->errorResponse('No tiene acceso a esta opción', 400);
            }

            $dataUsuarios = Usuarios::where('idusuario', '!=', auth()->user()->idusuario)->get();

            return $this->successResponse($dataUsuarios);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/usuarios/add",
     *     summary="Adiciona un usuario",
     *     tags={"Usuarios"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="rol",
     *                     type="string",
     *                     enum={"admin", "tourist"}
     *                 ),
     *                 @OA\Property(
     *                     property="nombres",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="apellido1",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="apellido2",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *
     *                 example={"email": "example@gmail.com", "rol": "tourist", "nombres": "Pepe", "apellido1": "de la Paz", "apellido2": "Garcia"}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *
     *     @OA\Response(
     *         response="400",
     *         description="Failed",
     *     ),
     *
     *     security={{"apiAuth": {} }},
     *
     *     deprecated=false
     * )
     */
    public function add_usuarios(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'rol' => 'required',
                'nombres' => 'required|min:3',
                'apellido1' => 'required|min:3',
                'apellido2' => 'nullable|min:3',
                'email' => 'required|email',
                'password' => 'nullable|string|min:5',
            ], [
                'rol.required' => 'Rol es un campo requerido',
                'nombres.required' => 'Nombre es un campo requerido',
                'nombres.min' => 'Mínimo de caracteres no válido {3}',
                'apellido1.required' => 'Apellidos es un campo requerido',
                'apellido1.min' => 'Mínimo de caracteres no válido {3}',
                'apellido2.min' => 'Mínimo de caracteres no válido {3}',
                'email.required' => 'Email es un campo requerido',
                'email.email' => 'Correo electronico invalido',
                'password.min' => 'Contraseña demasiado corta'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->toJson(), 400);
            }

            // verificando el correo electronico
            $dataUsuario = Usuarios::where(['email' => $request->get('email')])->get();
            if (count($dataUsuario) > 0) {
                return $this->errorResponse('La dirección de correo electrónico ya esta registrada', 400);
            }

            $dataUsuario = new Usuarios();
            $dataUsuario->idusuario = Str::uuid();
            $dataUsuario->rol = $request->get('rol');
            $dataUsuario->nombres = $request->get('nombres');
            $dataUsuario->apellido1 = $request->get('apellido1');
            if ($request->has('apellido2')) {
                $dataUsuario->apellido2 = $request->get('apellido2');
            }
            $dataUsuario->email = $request->get('email');
            if ($request->has('password')) {
                $dataUsuario->password = Hash::make($request->get('password'));
            } else {
                $dataUsuario->password = Hash::make('tourist');
            }
            $dataUsuario->fecha_creacion = Carbon::now();
            $dataUsuario->save();

            Mail::to($request->get('email'))->queue(new UsersCreate($dataUsuario));

            return $this->successResponse([
                'status' => 200,
                'message' => 'Usuario adicionado satisfactoriamente.',
                'data' => $dataUsuario
            ]);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    /**
     * @OA\Patch(
     *     path="/usuarios/update/{idusuario}",
     *     summary="Modificar un usuario",
     *     tags={"Usuarios"},
     *
     *     @OA\Parameter(
     *          name="idusuario",
     *          description="Identificador del usuario",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="rol",
     *                     type="string",
     *                     enum={"admin", "tourist"}
     *                 ),
     *                 @OA\Property(
     *                     property="nombres",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="apellido1",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="apellido2",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *
     *                 example={"email": "example@gmail.com", "rol": "tourist", "nombres": "Pepe", "apellido1": "de la Paz", "apellido2": "Garcia"}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *
     *     @OA\Response(
     *         response="400",
     *         description="Failed",
     *     ),
     *
     *     security={{"apiAuth": {} }},
     *
     *     deprecated=false
     * )
     */
    public function update_usuarios(Request $request, $idusuario) {
        try {
            $validator = Validator::make($request->all(), [
                'rol' => 'required',
                'nombres' => 'required|min:3',
                'apellido1' => 'required|min:3',
                'apellido2' => 'nullable|min:3',
                'email' => 'required|email',
                'password' => 'nullable|string|min:5',
            ], [
                'rol.required' => 'Rol es un campo requerido',
                'nombres.required' => 'Nombre es un campo requerido',
                'nombres.min' => 'Mínimo de caracteres no válido {3}',
                'apellido1.required' => 'Apellidos es un campo requerido',
                'apellido1.min' => 'Mínimo de caracteres no válido {3}',
                'apellido2.min' => 'Mínimo de caracteres no válido {3}',
                'email.required' => 'Email es un campo requerido',
                'email.email' => 'Correo electronico invalido',
                'password.min' => 'Contraseña demasiado corta'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->toJson(), 400);
            }

            if (Str::isUuid($idusuario) == false) {
                return $this->errorResponse('Identificador del usuario no válido', 400);
            }

            $dataUsuario = Usuarios::where(['idusuario' => $idusuario])->get();

            if (count($dataUsuario) == 0) {
                return $this->errorResponse('No existe el usuario solicitado', 400);
            }

            DB::table('tourist_usuarios')
                ->where(['idusuario' => $idusuario])
                ->update([
                    'rol' => $request->get('rol'),
                    'nombres' => $request->get('nombres'),
                    'apellido1' => $request->get('apellido1'),
                    'apellido2' => $request->get('apellido2'),
                    'email' => $request->get('email')
                ]);

            return $this->successResponse([
                'status' => 200,
                'message' => 'Usuario actualizado satisfactoriamente'
            ]);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/usuarios/delete/{idusuario}",
     *     summary="Eliminar un usuario",
     *     tags={"Usuarios"},
     *
     *     @OA\Parameter(
     *          name="idusuario",
     *          description="Identificador del usuario",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *
     *     @OA\Response(
     *         response="400",
     *         description="Failed",
     *     ),
     *
     *     security={{"apiAuth": {} }},
     *
     *     deprecated=false
     * )
     */
    public function delete_usuarios($idusuario) {
        try {
            if (auth()->user()->rol != Usuarios::ROL_ADMIN) {
                return $this->errorResponse('No tiene a acceso a esta opción', 400);
            }

            if (Str::isUuid($idusuario) == false) {
                return $this->errorResponse('Identificador del usuario no válido', 400);
            }

            DB::table('tourist_usuarios')
                ->where(['idusuario' => $idusuario])
                ->delete();

            return $this->successResponse([
                'status' => 200,
                'message' => 'Usuario eliminado satisfactoriamente'
            ]);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
