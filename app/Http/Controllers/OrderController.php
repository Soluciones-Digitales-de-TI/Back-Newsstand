<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Http\Requests\StoreOrderProductRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrdersProducts;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    /**
     * Listado de todos los registros de órdenes
     * @OA\Get (
     *     path="/api/v1/orders",
     *     tags={"Orders"},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de órdenes exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="rows",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="user_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="total",
     *                         type="number",
     *                         format="float",
     *                         example=100.50
     *                     ),
     *                     @OA\Property(
     *                         property="status",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2023-02-23T00:09:16.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2023-02-23T12:33:45.000000Z"
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="total",
     *                 type="double",
     *                 example=50.00
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 30);
        $page = $request->input('page', 1);


        $models = Order::active()->with('user')->with('products')->paginate($perPage, ['*'], 'page', $page);

        $data['models'] = $models->items();
        $data['total'] = $models->total();

        return ApiResponseHelper::sendResponse($data, '', 200);
    }

    /**
     * Crear una nueva orden
     * @OA\Post(
     *     path="/api/v1/orders",
     *     tags={"Orders"},
     *     summary="Crear una nueva orden",
     *     description="Crear una nueva orden en la base de datos",
     *     operationId="storeOrder",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"user_id",  "total", "status"},
     *              @OA\Property(property="user_id", type="integer", example=1),
     *              @OA\Property(property="total", type="number", format="float", example=100.50),
     *              @OA\Property(property="status", type="integer", example=1),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="user_id", type="integer", example=1),
     *              @OA\Property(property="total", type="number", format="float", example=100.50),
     *              @OA\Property(property="status", type="number", example=1),
     *              @OA\Property(property="created_at", type="string", format="date-time", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", format="date-time", example="2023-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object", example={"total": {"The total must be a number."}})
     *         )
     *     )
     * )
     */
    public function store(StoreOrderRequest $request)
    {
        $user_id = auth()->user()->id;
        $data = [
            'user_id' => $user_id,
            'total' => $request->total,
            'status' => $request->status
        ];

        DB::beginTransaction();
        try {
            $model = Order::create($data);
            DB::commit();
            return ApiResponseHelper::sendResponse($model, 'Record create succesful', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }


    /**
     * Obtener una orden específica
     * @OA\Get (
     *     path="/api/v1/orders/{id}",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description=" Sucessfull",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="user_id", type="integer", example=3),
     *              @OA\Property(property="total", type="number", format="float", example=100.50),
     *              @OA\Property(property="status", type="number", example=1),
     *              @OA\Property(property="created_at", type="string", format="date-time", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", format="date-time", example="2023-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Order] con id #id")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        try {
            $order = Order::findOrFail($id);
            return ApiResponseHelper::sendResponse($order, '', 200);
        } catch (\Exception $ex) {
            return ApiResponseHelper::rollback($ex);
        }
    }


    /**
     * Actualizar una orden existente
     * @OA\Put(
     *     path="/api/v1/orders/{id}",
     *     tags={"Orders"},
     *     summary="Actualizar una orden existente",
     *     description="Actualizar una orden existente en la base de datos",
     *     operationId="updateOrder",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"status"},
     *              @OA\Property(property="status", type="number", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="user_id", type="integer", example=1),
     *              @OA\Property(property="total", type="number", format="float", example=100.50),
     *              @OA\Property(property="status", type="number", example=2),
     *              @OA\Property(property="created_at", type="string", format="date-time", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", format="date-time", example="2023-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The data provided is invalid."),
     *              @OA\Property(property="errors", type="object", example={"status": {"The status field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Order] con id #id")
     *         )
     *     )
     * )
     */
    public function update(UpdateOrderRequest $request, string $id)
    {
        $data = $request->only(['status']);

        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);
            $order->update($data);
            DB::commit();
            return ApiResponseHelper::sendResponse($order, 'Record update successful', 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }


    /**
     * Eliminar una orden de pedido
     * @OA\Delete(
     *     path="/api/v1/orders/{id}",
     *     tags={"Orders"},
     *     summary="Eliminar una orden de pedido",
     *     description="Eliminar una orden existente de la base de datos",
     *     operationId="deleteOrder",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted succesful",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Record deleted succesful.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Order not found.")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $model = Order::visible()->find($id)->update(['state' => 3]);
            return ApiResponseHelper::sendResponse($model, 'Record delete succesful', 200);
        } catch (\Exception $ex) {
            return ApiResponseHelper::rollback($ex);
        }
    }
}
