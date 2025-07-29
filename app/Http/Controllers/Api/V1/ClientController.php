<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterClientRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Services\ClientService;
use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClientController extends Controller
{
    /**
     * Register a new client.
     *
     * @param  RegisterClientRequest  $request  The request object containing client registration data.
     * @param  UserService  $userService  The service handling user-related operations.
     * @param  ClientService  $clientService  The service handling client-related operations.
     * @return JsonResponse The response object containing the result of the registration process.
     */
    public function register(RegisterClientRequest $request, UserService $userService, ClientService $clientService): JsonResponse
    {
        $validatedData = $request->validated();
        $user = $userService->createOrUpdateForClient($validatedData);
        $validatedData['user_id'] = $user->id;
        $clientService->createOrUpdate($validatedData);

        try {
            $token = JWTAuth::claims(['phone' => $user->phone])->fromUser($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token not created '.$e->__toString()], 500);
        }

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }
}
