<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cake\Subscriber\IndexSubscriberRequest;
use App\Http\Resources\SubscriberResource;
use App\Models\Cake;
use Illuminate\Http\JsonResponse;

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
}
