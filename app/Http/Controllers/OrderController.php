<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 30);
        $page = $request->input('page', 1);


        $models = Order::active()->paginate($perPage, ['*'], 'page', $page);

        $data['models'] = $models->items();
        $data['total'] = $models->total();

        return ApiResponseHelper::sendResponse($data, '', 200);
    }

    /**
     * Store a newly created resource in storage
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
     * Display the specified resource
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */

    public function show(Order $order)
    {
        try {
            return ApiResponseHelper::sendResponse($order, '', 200);
        } catch (\Exception $ex) {
            return ApiResponseHelper::rollback($ex);
        }
    }


    /**
     * Update the specified resource in storage
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $data = $request->only(['status']);

        DB::beginTransaction();
        try {
            $order->update($data);
            DB::commit();
            return ApiResponseHelper::sendResponse($order, 'Record update successful', 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }


    /**
     * Remove the specified resource from storage
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        try {
            $model = $order->delete();
            return ApiResponseHelper::sendResponse($model, 'Record delete succesful', 200);
        } catch (\Exception $ex) {
            return ApiResponseHelper::rollback($ex);
        }
    }
}
