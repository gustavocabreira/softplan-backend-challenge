<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cake\CreateCakeRequest;
use App\Http\Requests\Cake\IndexCakeRequest;
use App\Http\Requests\Cake\UpdateCakeRequest;
use App\Http\Resources\CakeResource;
use App\Models\Cake;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CakeController extends Controller
{
    public function index(IndexCakeRequest $request): JsonResponse
    {
        $request->validated();

        $cakesQuery = Cake::search($request->input('name'));

        if ($request->input('order_by')) {
            $cakesQuery->orderBy($request->input('order_by'), $request->input('direction') ?? 'asc');
        }

        $cakes = $cakesQuery->paginate($request->input('per_page') ?? 10);

        return CakeResource::collection($cakes)->response();
    }

    public function store(CreateCakeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $cake = Cake::query()->create($validated);

        if ($request->hasFile('file') && $cake->quantity > 0) {
            $filePath = $request->file('file')->store('email_lists');
            $cake->uploadedLists()->create([
                'file_path' => $filePath,
                'status' => 'pending',
            ]);
            $cake->load('uploadedLists');
        }

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
