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
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
