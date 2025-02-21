<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cake\CreateCakeRequest;
use App\Http\Requests\Cake\UpdateCakeRequest;
use App\Http\Resources\CakeResource;
use App\Models\Cake;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CakeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cakes = Cake::query()->paginate($request->input('per_page') ?? 10);

        return CakeResource::collection($cakes)->response();
    }

    public function store(CreateCakeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $cake = Cake::query()->create($validated);

        return response()->json(new CakeResource($cake), Response::HTTP_CREATED);
    }

    public function show(Cake $cake): JsonResponse
    {
        return response()->json(new CakeResource($cake), Response::HTTP_OK);
    }

    public function update(Cake $cake, UpdateCakeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $cake->update($validated);

        return response()->json(new CakeResource($cake), Response::HTTP_OK);
    }

    public function destroy(Cake $cake): JsonResponse
    {
        $cake->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
