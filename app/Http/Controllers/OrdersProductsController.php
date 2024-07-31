<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Http\Requests\StoreOrderProductRequest;
use App\Http\Requests\UpdateOrderProductRequest;
use App\Models\OrdersProducts;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
/**
* @OA\Info(
*             title="API OrdersProducts", 
*             version="1.0",
*             description="Registros de datos intermedia de orders y pedidos"
* )
*
* @OA\Server(url="http://127.0.0.1:8000/")
*/
class OrdersProductsController extends Controller
{
    /**
     * listado de todos los registros de ordersproducts
     * @OA\Get (
     *     path="/api/v1/ordersproducts",
     *     tags={"OrdersProducts"},
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
     *                         property="order_id",
     *                         type="BIGINT",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="product_id",
     *                         type="BIGINT",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="cantidad",
     *                         type="int",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2023-02-23T00:09:16.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2023-02-23T12:33:45.000000Z"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 30); // Número de elementos por página
        $page = $request->input('page', 1); // Página actual

        try {
            $models = OrdersProducts::paginate($perPage, ['*'], 'page', $page);

            $data = [
                'models' => $models->items(),
                'total' => $models->total(),
                'current_page' => $models->currentPage(),
                'last_page' => $models->lastPage(),
                'per_page' => $models->perPage(),
            ];

            return ApiResponseHelper::sendResponse($data, 'Records retrieved successfully', 200);
        } catch (\Exception $ex) {
            return ApiResponseHelper::rollback($ex);
        }
    }

    /**
 * Crear una nueva orden de producto
 * @OA\Post(
 *     path="/api/v1/ordersproducts",
 *     tags={"OrdersProducts"},
 *     summary="Crear una nueva orden de producto",
 *     description="Crear una nueva orden de producto en la base de datos",
 *     operationId="storeOrderProduct",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *              required={"order_id", "product_id", "cantidad"},
 *              @OA\Property(property="order_id", type="integer", example=1),
 *              @OA\Property(property="product_id", type="integer", example=2),
 *              @OA\Property(property="cantidad", type="integer", example=10)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Created",
 *         @OA\JsonContent(
 *              @OA\Property(property="id", type="number", example=1),
 *              @OA\Property(property="order_id", type="bigint", example=1),
 *              @OA\Property(property="product_id", type="bigint", example=2),
 *              @OA\Property(property="cantidad", type="int", example=10),
 *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
 *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request",
 *         @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="The given data was invalid."),
 *              @OA\Property(property="errors", type="object", example={"order_id": {"The order id field is required."}})
 *         )
 *     )
 * )
 */
    public function store(StoreOrderProductRequest $request)
    {
        // Extraer los datos del request
        $data = $request->only(['order_id', 'product_id', 'cantidad']);

        // Iniciar una transacción de base de datos
        DB::beginTransaction();

        try {
            // Crear un nuevo registro en la tabla 'orders_products'
            $orderProduct = OrdersProducts::create($data);

            // Confirmar la transacción
            DB::commit();

            // Devolver una respuesta exitosa con el modelo creado
            return ApiResponseHelper::sendResponse($orderProduct, 'Record created successfully', 201);
        } catch (\Exception $ex) {
            // Revertir la transacción en caso de error
            DB::rollBack();

            // Devolver una respuesta de error
            return ApiResponseHelper::rollback($ex);
        }
    }

    /**
     * Mostrar la información de una sola ordersproducts
     * @OA\Get (
     *     path="/api/v1/ordersproducts/{id}",
     *     tags={"OrdersProducts"},
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
     *              @OA\Property(property="order_id", type="bigint", example="2"),
     *              @OA\Property(property="product_id", type="bigint", example="3"),
     *              @OA\Property(property="cantidad", type="bigint", example="3"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *         )
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
            // Buscar el registro por ID
            $model = OrdersProducts::findOrFail($id);

            // Devolver la respuesta con el modelo encontrado
            return ApiResponseHelper::sendResponse($model, 'Record retrieved successfully', 200);
        } catch (ModelNotFoundException $ex) {
            // Manejo específico para el caso en que el registro no se encuentre
            return ApiResponseHelper::sendResponse(null, 'Record not found', 404);
        } catch (\Exception $ex) {
            // Manejo general para cualquier otra excepción
            return ApiResponseHelper::rollback($ex);
        }
    }


    /**
 * Actualizar una orden de producto existente
 * @OA\Put(
 *     path="/api/v1/ordersproducts/{id}",
 *     tags={"OrdersProducts"},
 *     summary="Actualizar una orden de producto",
 *     description="Actualizar una orden de producto existente en la base de datos",
 *     operationId="updateOrderProduct",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *              required={"order_id", "product_id", "cantidad"},
 *              @OA\Property(property="order_id", type="integer", example=1),
 *              @OA\Property(property="product_id", type="integer", example=2),
 *              @OA\Property(property="cantidad", type="integer", example=10)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *              @OA\Property(property="id", type="number", example=1),
 *              @OA\Property(property="order_id", type="bigint", example=1),
 *              @OA\Property(property="product_id", type="bigint", example=2),
 *              @OA\Property(property="cantidad", type="int", example=10),
 *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
 *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request",
 *         @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="The given data was invalid."),
 *              @OA\Property(property="errors", type="object", example={"order_id": {"The order id field is required."}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found",
 *         @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="OrderProduct not found.")
 *         )
 *     )
 * )
 */
    public function update(UpdateOrderProductRequest $request, string $id)
    {
        // Obtener los datos del request
        $data = $request->only(['order_id', 'product_id', 'cantidad']);

        DB::beginTransaction();
        try {
            // Buscar el registro por ID
            $orderProduct = OrdersProducts::findOrFail($id);

            // Actualizar el registro
            $orderProduct->update($data);

            DB::commit();

            // Devolver la respuesta con el modelo actualizado
            return ApiResponseHelper::sendResponse($orderProduct, 'Record updated successfully', 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            // Manejo de excepciones
            return ApiResponseHelper::rollback($ex);
        }
    }


    /**
 * Eliminar una orden de producto existente
 * @OA\Delete(
 *     path="/api/v1/ordersproducts/{id}",
 *     tags={"OrdersProducts"},
 *     summary="Eliminar una orden de producto",
 *     description="Eliminar una orden de producto existente de la base de datos",
 *     operationId="deleteOrderProduct",
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
 *              @OA\Property(property="message", type="string", example="OrderProduct deleted successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found",
 *         @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="OrderProduct not found.")
 *         )
 *     )
 * )
 */
    public function destroy(string $id)
    {
        try {
            // Buscar el registro por ID
            $orderProduct = OrdersProducts::findOrFail($id);

            // Eliminar el registro
            $orderProduct->delete();

            // Responder con éxito
            return ApiResponseHelper::sendResponse(null, 'Record deleted successfully', 204);
        } catch (\Exception $ex) {
            // Manejo de excepciones
            return ApiResponseHelper::rollback($ex);
        }
    }
}
