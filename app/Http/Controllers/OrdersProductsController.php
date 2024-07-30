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

class OrdersProductsController extends Controller
{
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
     * Store a newly created resource in storage
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
     * Display the specified resource
     */
    public function show(string $id)
    {
        try {
            // Buscar el registro por ID, omitimos el scope 'visible' si no es necesario
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
     * Update the specified resource in storage
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
     * Remove the specified resource from storage
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
