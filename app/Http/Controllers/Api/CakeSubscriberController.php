<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriberResource;
use App\Models\Cake;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CakeSubscriberController extends Controller
{
    public function index(Cake $cake, Request $request): JsonResponse
    {
        $request->validate([
            'order_by' => ['sometimes', 'string', Rule::in((new Subscriber)->getFillable())],
            'direction' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
            'name' => ['sometimes', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1'],
        ]);

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
