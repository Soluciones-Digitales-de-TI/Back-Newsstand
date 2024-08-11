<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource
     */
    public function index(Request $request)
    {
        //$perPage = $request->input('per_page', 30);
        $perPage = $request->input('per_page', 59);
        $page = $request->input('page', 1);
        $models = Product::active()
            ->paginate($perPage, ['*'], 'page', $page) ?? [];
        $data['models'] = $models->items();
        $data['total'] = $models->total();
        return ApiResponseHelper::sendResponse($data, '', 200);
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(StoreProductRequest $request)
    {
        $data = [
            'name' => $request->name ?? '',
            'price' => $request->price ?? null,
            'image' => $request->image ?? '',
            'available' => $request->state ?? 1,
            'categorie_id' => $request->categorie_id ?? null,
        ];
        DB::beginTransaction();
        try {
            $model = Product::create($data);
            DB::commit();
            return ApiResponseHelper::sendResponse($model, 'Record create succesful', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    /**
     * Display the specified resource
     */
    public function show(string $id)
    {
        try {
            $model = Product::visible()->find($id);
            return ApiResponseHelper::sendResponse($model, '', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex);
        }
    }

    /**
     * Update the specified resource in storage
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $data = [
            'name' => $request->name ?? '',
            'price' => $request->price ?? null,
            'image' => $request->image ?? '',
            'available' => $request->state ?? 1,
            'categorie_id' => $request->categorie_id ?? null,
        ];
        DB::beginTransaction();
        try {
            $model = Product::visible()->find($id)->update($data);
            DB::commit();
            return ApiResponseHelper::sendResponse($model, 'Record updated succesful', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy(string $id)
    {
        try {
            $model = Product::visible()->find($id)->update(['available' => 3]);
            return ApiResponseHelper::sendResponse(null, 'Record deleted succesful', 204);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex);
        }
    }
}