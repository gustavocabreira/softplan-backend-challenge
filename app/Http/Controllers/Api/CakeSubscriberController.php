<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cake\Subscriber\IndexSubscriberRequest;
use App\Http\Resources\SubscriberResource;
use App\Models\Cake;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class CakeSubscriberController extends Controller
{
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

    public function store(Cake $cake, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('subscribers')->where(function ($query) use ($cake) {
                    return $query->where('cake_id', $cake->id);
                }),
            ],
        ], [
            'email.unique' => 'This email is already subscribed to this cake.',
        ]);

        $subscriber = $cake->subscribers()->create([
            'email' => $validated['email'],
            'status' => 'pending',
        ]);

        $subscriber->load('cake');

        return response()->json(new SubscriberResource($subscriber), Response::HTTP_CREATED);
    }

    public function destroy(Cake $cake, Subscriber $subscriber): JsonResponse
    {
        $subscriber->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
