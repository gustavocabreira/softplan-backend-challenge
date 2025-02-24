<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cake\Subscriber\IndexSubscriberRequest;
use App\Http\Requests\Cake\Subscriber\StoreSubscriberRequest;
use App\Http\Resources\SubscriberResource;
use App\Models\Cake;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CakeSubscriberController extends Controller
{
    /**
     * Display a listing of the cake's subscribers.
     */
    public function index(Cake $cake, IndexSubscriberRequest $request): JsonResponse
    {
        $request->validated();

        $subscribers = $cake->subscribers();

        if ($request->has('email')) {
            $subscribers->where('email', 'like', '%'.$request->input('email').'%');
        }

        if ($request->has('order_by')) {
            $orderBy = $request->input('order_by');
            $direction = $request->input('direction', 'asc');
            $subscribers->orderBy($orderBy, $direction);
        }

        $perPage = $request->input('per_page', 10);
        $subscribers = $subscribers->paginate($perPage);

        return SubscriberResource::collection($subscribers)->response();
    }

    /**
     * Store a new subscriber for the cake.
     */
    public function store(Cake $cake, StoreSubscriberRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $subscriber = $cake->subscribers()->create([
            'email' => $validated['email'],
            'status' => 'pending',
        ]);

        $subscriber->load('cake');

        return response()->json(new SubscriberResource($subscriber), Response::HTTP_CREATED);
    }

    /**
     * Unsubscribe from the cake.
     */
    public function destroy(Cake $cake, Subscriber $subscriber): JsonResponse
    {
        $subscriber->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
