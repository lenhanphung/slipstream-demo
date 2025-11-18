<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerCategoryResource;
use App\Models\CustomerCategory;
use Illuminate\Http\JsonResponse;

class CustomerCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = CustomerCategory::orderBy('name')->get();

        return response()->json(CustomerCategoryResource::collection($categories));
    }
}
