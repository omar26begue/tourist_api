<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\Comentarios;
use App\Models\Usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ComentariosController extends ApiController
{
    public function __construct()
    {
    }

    /**
     * @OA\Get(
     *     path="/comentarios/list",
     *     summary="Listado de comentarios",
     *     tags={"Comentarios"},
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
    public function list_comentarios()
    {
        try {
            $dataComentarios = Comentarios::orderBy('fecha', 'desc')->get();

            for ($index = 0; $index < count($dataComentarios); $index++) {
                $dataUsuario = Usuarios::where(['idusuario' => $dataComentarios[$index]->idusuario])->get()->first();
                $dataComentarios[$index]['fullname'] = $dataUsuario->nombres . ' ' . $dataUsuario->apellido1 . ' ' . $dataUsuario->apellido2;
            }

            return $this->successResponse($dataComentarios);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/comentarios/add",
     *     summary="Adiciona un comentario",
     *     tags={"Comentarios"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *
     *                 @OA\Property(
     *                     property="texto",
     *                     type="string",
     *                 ),
     *
     *                 example={"texto": "comentario"}
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
    public function add_comentario(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'texto' => 'required',
            ], [
                'texto.required' => 'Comentario es un campo requerido'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->toJson(), 400);
            }

            $dataComentario = new Comentarios();
            $dataComentario->idcomentarios = Str::uuid();
            $dataComentario->texto = $request->get('texto');
            $dataComentario->idusuario = auth()->user()->idusuario;
            $dataComentario->fecha = Carbon::now();
            $dataComentario->save();

            return $this->successResponse([
                'status' => 200,
                'message' => 'Comentario adicionado satisfactoriamente.',
                'data' => $dataComentario
            ]);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
