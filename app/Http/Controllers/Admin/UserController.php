<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::query()
            ->orderByDesc('last_login_at')
            ->orderBy('id', 'asc')
            ->get(['id', 'name', 'phone']);

        return response()->json($users);
    }
}
