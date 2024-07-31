<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Listado de todos los registros de categorias
     * @OA\Get (
     *     path="/api/v1/categories",
     *     tags={"Category"},
     *     @OA\Response(
     *         response=200,
     *         description="sucessfull",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="rows",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="cookies"
     *                     ),
     *                     @OA\Property(
     *                         property="icon",
     *                         type="string",
     *                         example="url"
     *                     ),
     *                     @OA\Property(
     *                         property="state",
     *                         type="int",
     *                         example="1"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $models = Category::active()
            ->paginate($perPage, ['*'], 'page', $page) ?? [];
        $data['models'] = $models->items();
        $data['total'] = $models->total();
        return ApiResponseHelper::sendResponse($data, '', 200);
    }

    /**
     * Crear una nueva categoria
     * @OA\Post(
     *     path="/api/v1/categories",
     *     tags={"Category"},
     *     summary="Crear una nueva categoria",
     *     description="Crear una nueva categoria",
     *     operationId="storeCategory",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Cookies"),
     *              @OA\Property(property="icon", type="integer", example="files/categories/84382974392.jpg"),
     *              @OA\Property(property="state", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Cookies"),
     *              @OA\Property(property="icon", type="integer", example="files/categories/84382974392.jpg"),
     *              @OA\Property(property="state", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object", example={"errors": {"Category saved failed."}})
     *         )
     *     )
     * )
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = [
            'name' => $request->name ?? '',
            'icon' => $request->icon ?? '',
            'state' => $request->state ?? 1,
        ];
        DB::beginTransaction();
        try {
            $model = Category::create($data);
            DB::commit();
            return ApiResponseHelper::sendResponse($model, 'Record create succesful', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    /**
     * Mostrar la información de una sola categoria
     * @OA\Get (
     *     path="/api/v1/categories/{id}",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucessfull",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Cookies"),
     *              @OA\Property(property="icon", type="integer", example="files/categories/84382974392.jpg"),
     *              @OA\Property(property="state", type="integer", example=1)
     *        )
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="NOT FOUND",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Cliente] #id"),
     *          )
     *      )
     * )
     */
    public function show(string $id)
    {
        try {
            $model = Category::visible()->find($id);
            return ApiResponseHelper::sendResponse($model, '', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex);
        }
    }

    /**
 * Actualizar una categoria existente
 * @OA\Put(
 *     path="/api/v1/categories/{id}",
 *     tags={"Category"},
 *     summary="Actualizar una categoria",
 *     description="Actualizar una categoria existente en la base de datos",
 *     operationId="updateCategory",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *              @OA\Property(property="name", type="string", example="Cookies"),
 *              @OA\Property(property="icon", type="integer", example="files/categories/84382974392.jpg"),
 *              @OA\Property(property="state", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *              @OA\Property(property="id", type="number", example=1),
 *              @OA\Property(property="name", type="string", example="Cookies"),
 *              @OA\Property(property="icon", type="integer", example="files/categories/84382974392.jpg"),
 *              @OA\Property(property="state", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request",
 *         @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="The given data was invalid."),
 *              @OA\Property(property="errors", type="object", example={"errors": {"Category errors"}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found",
 *         @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="Category not found.")
 *         )
 *     )
 * )
 */
    public function update(UpdateCategoryRequest $request, string $id)
    {
        $data = [
            'name' => $request->name ?? '',
            'icon' => $request->icon ?? '',
            'state' => $request->state ?? 1,
        ];
        DB::beginTransaction();
        try {
            $model = Category::visible()->find($id)->update($data);
            DB::commit();
            return ApiResponseHelper::sendResponse($model, 'Record updated succesful', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    /**
 * Eliminar una categoria existente
 * @OA\Delete(
 *     path="/api/v1/categories/{id}",
 *     tags={"Category"},
 *     summary="Eliminar una categoria",
 *     description="Eliminar una categoria existente de la base de datos",
 *     operationId="deleteCategory",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="Category deleted successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found",
 *         @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="Category not found.")
 *         )
 *     )
 * )
 */
    public function destroy(string $id)
    {
        try {
            $model = Category::visible()->find($id)->update(['state' => 3]);
            return ApiResponseHelper::sendResponse(null, 'Record deleted succesful', 204);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex);
        }
    }
}
