<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\Imagenes;
use App\Models\SitiosTuristicos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SitiosController extends ApiController
{
    public function __construct()
    {
    }

    /**
     * @OA\Get(
     *     path="/sitios/list",
     *     summary="Listado de sitios turisticos",
     *     tags={"Sitios turísticos"},
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
    public function list_sitios()
    {
        try {
            $dataSitios = SitiosTuristicos::orderBy('nombre', 'asc')->get();

            for ($index = 0; $index < count($dataSitios); $index++) {
                $dataImagenes = Imagenes::where(['idsitio' => $dataSitios[$index]->idsitio])->get();
                $dataSitios[$index]->imagenes = $dataImagenes;
            }

            return $this->successResponse($dataSitios);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/sitios/add",
     *     summary="Adiciona un sitio turístico",
     *     tags={"Sitios turísticos"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *
     *                 @OA\Property(
     *                     property="nombre",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="ubicacion",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="distancia",
     *                     type="float",
     *                 ),
     *                 @OA\Property(
     *                     property="tiempo",
     *                     type="int",
     *                 ),
     *                 @OA\Property(
     *                     property="poblacion",
     *                     type="int",
     *                 ),
     *
     *                 example={"nombre": "nombre del sitio turistico", "ubicacion": "localización", "distancia": 0, "tiempo": 0, "poblacion": 1000}
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
    public function add_sitio(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string',
                'ubicacion' => 'required|string',
                'distancia' => 'required',
                'tiempo' => 'required',
                'poblacion' => 'required',
                'descripcion' => 'required',
                'hora_apertura' => 'required',
                'hora_cierre' => 'required'
            ], [
                'nombre.required' => 'Nombre del sitio turístico es un campo requerido',
                'ubicacion.required' => 'Ubicación es un campo requerido',
                'distancia.required' => 'Distancia es un campo requerido',
                'tiempo.required' => 'Tiempo es un campo requerido',
                'poblacion.required' => 'Población es un campo requerido',
                'descripcion.required' => 'Descripción es un campo requerido',
                'hora_apertura.required' => 'Hora de apertura es un campo requerido',
                'hora_cierre.required' => 'Hora de cierre es un campo requerido'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->toJson(), 400);
            }

            $dataSitios = new SitiosTuristicos();
            $dataSitios->idsitio = Str::uuid();
            $dataSitios->nombre = $request->get('nombre');
            $dataSitios->ubicacion = $request->get('ubicacion');
            $dataSitios->distancia = $request->get('distancia');
            $dataSitios->tiempo = $request->get('tiempo');
            $dataSitios->poblacion = $request->get('poblacion');
            $dataSitios->descripcion = $request->get('descripcion');
            $dataSitios->hora_apertura = $request->get('hora_apertura');
            $dataSitios->hora_cierre = $request->get('hora_cierre');
            $dataSitios->save();

            return $this->successResponse([
                'status' => 200,
                'message' => 'Sitio turístico adicionado satisfactoriamente.',
                'data' => $dataSitios
            ]);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/sitios/uploadphoto/{idsitio}",
     *     summary="Sube al servidor la foto del sitio",
     *     tags={"Sitios turísticos"},
     *
     *     @OA\Parameter(
     *          name="idsitio",
     *          description="Identificador del sitio",
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
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="image_sitio",
     *                     type="file"
     *                 )
     *             ),
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
    public function subir_imagen(Request $request, $idsitio)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image_sitios' => 'required|image|mimes:jpeg,png,jpg',
            ], [
                'image_sitio.required' => 'Imagen es un campo requerido',
                'image_sitio.image' => 'La imagen no es válida',
                'image_sitio.mimes' => 'Archivo no soportado',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->toJson(), 400);
            }

            $nameFile = $request->file('image_sitios')->store('image_sitios');

            $dataImage = new Imagenes();
            $dataImage->idimagenes = Str::uuid();
            $dataImage->idsitio = $idsitio;
            $dataImage->nombre = $nameFile;
            $dataImage->save();

            return $this->successResponse([
                'status' => 200,
                'message' => 'Imagen subida al servidor satisfactoriamente',
                'data' => $dataImage
            ]);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/sitios/calificar/{idsitio}/{idimagen}/{calificacion}",
     *     summary="Califica una imagen",
     *     tags={"Sitios turísticos"},
     *
     *     @OA\Parameter(
     *          name="idsitio",
     *          description="Identificador del sitio",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *     @OA\Parameter(
     *          name="idimagen",
     *          description="Identificador de la imagen",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *     @OA\Parameter(
     *          name="calificacion",
     *          description="Calificacion",
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
    public function calificar_imagen($idsitio, $idimagen, $calificacionp) {
        try {
            $dataSitio = SitiosTuristicos::where(['idsitio' => $idsitio])->get()->first();

            $dataArrayImagenes = Imagenes::where(['idsitio' => $idsitio])->get();

            $dataImagen = Imagenes::where(['idimagenes' => $idimagen])->get()->first();

            $calificacion = (($calificacionp + $dataImagen->calificacion) / 2);

            DB::table('tourist_imagenes')
                ->where(['idimagenes' => $idimagen])
                ->update([
                    'calificacion' => round($calificacion)
                ]);

            return $this->successResponse([
                'status' => 200,
                'message' => 'Nueva calificación de '.$calificacion,
                'data' => $calificacion
            ]);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/sitios/imagen_position/{idimagen}",
     *     summary="Salvar posicion imagen",
     *     tags={"Sitios turísticos"},
     *
     *     @OA\Parameter(
     *          name="idimagen",
     *          description="Identificador de la imagen",
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
     *                     property="lat",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="lon",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="zoom",
     *                     type="string",
     *                 ),
     *
     *                 example={"lat": "lat", "lon": "lon", "zoom": "zoom"}
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
    public function save_position(Request $request, $idimagen) {
        try {
            $validator = Validator::make($request->all(), [
                'lat' => 'required',
                'lon' => 'required',
                'zoom' => 'required',
            ], [
                'lat.required' => 'Latitud es un campo requerido',
                'lon.required' => 'longitud es un campo requerido',
                'zoom.required' => 'Zoom es un campo requerido',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->toJson(), 400);
            }

            DB::table('tourist_imagenes')
                ->where(['idimagenes' => $idimagen])
                ->update([
                    'lat' => $request->get('lat'),
                    'lon' => $request->get('lon'),
                    'zoom' => $request->get('zoom')
                ]);

            return $this->successResponse([
                'status' => 200,
                'message' => 'Posición guardada',
            ]);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/sitios/delete/{idsitio}",
     *     summary="Elimina un sitio turístico",
     *     tags={"Sitios turísticos"},
     *
     *     @OA\Parameter(
     *          name="idsitio",
     *          description="Identificador del sitio",
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
    public function delele_sitio($idsitio) {
        try {
            if (Str::isUuid($idsitio) == false) {
                return $this->errorResponse('Identificador no válido', 400);
            }

            DB::table('tourist_sitios')
                ->where(['idsitio' => $idsitio])
                ->delete();

            return $this->successResponse([
                'status' => 200,
                'message' => 'Sitio eliminado correctamente',
            ]);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
    }
}
