<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Mail\UsersReset;
use App\Models\Usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @OA\Info(
 *     title="API Tourist",
 *     version="1.0.0"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="login",
 *     name="token",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="apiAuth"
 * )
 *
 * @OA\Server(
 *      url="http://localhost:8000",
 *      description="localhost",
 *
 * @OA\ServerVariable(
 *      serverVariable="schema",
 *      enum={"https", "http"},
 *      default="http"
 *  )
 * )
 * )
 */
class AuthController extends ApiController
{
    public function __construct()
    {
    }

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Autentificación de usuarios",
     *     tags={"Autentificación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"email": "example@gmail.com", "password": "password"}
     *             )
     *         )
     *     ),
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
     *     deprecated=false
     * )
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:5'
            ], [
                'email.required' => 'Usuario es un campo requerido',
                'email.email' => 'Correo electronico invalido',
                'password.required' => 'Contraseña es un campo requerido',
                'password.min' => 'Mínimo de caracteres no válido {5}',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->toJson(), 400);
            }

            $dataUsuario = Usuarios::where(['email' => $request->get('email')])->get()->first();

            if ($dataUsuario == null) {
                return $this->errorResponse('El usuario no está registrado', 400);
            }

            if ($dataUsuario->verificado == Usuarios::USUARIO_NO_VERIFICADO) {
                return $this->errorResponse('El usuario no está verificado', 400);
            }

            if ($dataUsuario->verificado == Usuarios::USUARIO_NOACTIVO) {
                return $this->errorResponse('El usuario no está activo en el sistema', 400);
            }

            $credencial = array('email' => $dataUsuario->email, 'password' => $request->get('password'));

            if (!$token = auth()->attempt($credencial)) {
                return $this->errorResponse('Usuario o contraseña incorrectos', 400);
            }

            return $this->respondWithToken($token);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    protected function respondWithToken($token)
    {
        return $this->successResponse([
            'idusuario' => auth()->user()->idusuario,
            'rol' => auth()->user()->rol,
            'fullname' => auth()->user()->nombres . ' ' . auth()->user()->apellido1 . ' ' . auth()->user()->apellido2,
            'nombres' => auth()->user()->nombres,
            'apellido1' => auth()->user()->apellido1,
            'apellido2' => auth()->user()->apellido2,
            'email' => auth()->user()->email,
            'photo' => auth()->user()->photo,
            'access_token' => $token,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/auth/info",
     *     summary="Información del usuario autenticado",
     *     tags={"Autentificación"},
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
    public function info()
    {
        try {
            return $this->successResponse([
                'idusuario' => auth()->user()->idusuario,
                'rol' => auth()->user()->rol,
                'fullname' => auth()->user()->nombres . ' ' . auth()->user()->apellido1 . ' ' . auth()->user()->apellido2,
                'nombres' => auth()->user()->nombres,
                'apellido1' => auth()->user()->apellido1,
                'apellido2' => auth()->user()->apellido2,
                'email' => auth()->user()->email,
                'photo' => auth()->user()->photo
            ]);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/auth/recuperar/{email}",
     *     summary="Recuperar la contraseña usuario",
     *     tags={"Autentificación"},
     *
     *     @OA\Parameter(
     *          name="email",
     *          description="Correo electrónico",
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
     *     deprecated=false
     * )
     */
    public function recuperarContrasena($email)
    {
        try {
            $valid = filter_var($email, FILTER_VALIDATE_EMAIL);

            if ($valid == false) {
                return $this->errorResponse('Correo electrónico no válido', 400);
            }

            $password = Str::random(10);

            DB::table('tourist_usuarios')
                ->where(['email' => $email])
                ->update([
                    'password' => Hash::make($password)
                ]);

            Mail::to($email)->queue(new UsersReset($email, $password));

            return $this->successResponse([
                'status' => 200,
                'message' => 'Su nueva contraseña ha sido enviada a su correo electrónico'
            ]);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
