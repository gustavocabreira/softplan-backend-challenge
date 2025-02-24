<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmailListResource;
use App\Models\Cake;
use App\Models\EmailList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CakeEmailListController extends Controller
{
    public function index(Cake $cake, Request $request): JsonResponse
    {
        $request->validate([
            'order_by' => ['sometimes', 'string', Rule::in((new EmailList)->getFillable())],
            'direction' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $emailLists = $cake->emailLists();

        if ($request->has('order_by')) {
            $orderBy = $request->input('order_by');
            $direction = $request->input('direction', 'asc');
            $emailLists->orderBy($orderBy, $direction);
        }

        $perPage = $request->input('per_page', 10);
        $emailLists = $emailLists->paginate($perPage);

        return EmailListResource::collection($emailLists)->response();
    }
}
