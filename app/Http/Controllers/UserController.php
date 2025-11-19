<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\UserDetailsResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index(): JsonResponse
    {
        $users = User::query()->get();
        return ApiResponse::sendResponse(UserDetailsResource::collection($users));
    }

    public function receivers(): JsonResponse
    {
        $users = User::query()->where('id','!=',Auth::id())->get();
        return ApiResponse::sendResponse(UserDetailsResource::collection($users));
    }
}
