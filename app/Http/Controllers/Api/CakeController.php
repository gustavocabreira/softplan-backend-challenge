<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cake\CreateCakeRequest;
use App\Http\Requests\Cake\IndexCakeRequest;
use App\Http\Requests\Cake\UpdateCakeRequest;
use App\Http\Resources\CakeResource;
use App\Jobs\MarkSubscribersAsPending;
use App\Models\Cake;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CakeController extends Controller
{
    /**
     * Display a listing of the cakes.
     */
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

    /**
     * Store a new cake
     */
    public function store(CreateCakeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $cake = Cake::query()->create($validated);

        if ($request->hasFile('file')) {
            $file = $request->file('file')->storeAs('uploads', uniqid().'.csv');
            $cake->emailLists()->create(['file_path' => $file, 'status' => 'pending']);
            $cake->load('emailLists');
        }

        return response()->json(new CakeResource($cake), Response::HTTP_CREATED);
    }

    /**
     * Display the specified cake.
     */
    public function show(Cake $cake): JsonResponse
    {
        return response()->json(new CakeResource($cake), Response::HTTP_OK);
    }

    /**
     * Update the specified cake.
     */
    public function update(Cake $cake, UpdateCakeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $cakeIsAvailable = $cake->quantity == 0 && $request->input('quantity') > 0;

        $cake->update($validated);

        if ($cakeIsAvailable) {
            MarkSubscribersAsPending::dispatch($cake->id);
        }

        return response()->json(new CakeResource($cake), Response::HTTP_OK);
    }

    /**
     * Remove the specified cake.
     */
    public function destroy(Cake $cake): JsonResponse
    {
        $cake->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
