<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cake\CreateCakeRequest;
use App\Http\Resources\CakeResource;
use App\Models\Cake;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CakeController extends Controller
{
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
}
