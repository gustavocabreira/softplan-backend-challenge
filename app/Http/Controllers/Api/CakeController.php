<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cake;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CakeController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0.0'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $cake = Cake::query()->create($validated);

        return response()->json($cake, Response::HTTP_CREATED);
    }
}
