<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cake\EmailList\IndexEmailListRequest;
use App\Http\Resources\EmailListResource;
use App\Models\Cake;
use Illuminate\Http\JsonResponse;

class CakeEmailListController extends Controller
{
    /**
     * Display a listing of the email lists.
     */
    public function index(Cake $cake, IndexEmailListRequest $request): JsonResponse
    {
        $request->validated();

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
