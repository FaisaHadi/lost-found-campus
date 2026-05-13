<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $payload = $this->authService->register($request->validated());

        return $this->successResponse($this->tokenPayload($payload), 'Registrasi berhasil.', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $payload = $this->authService->login($request->validated());

        return $this->successResponse($this->tokenPayload($payload), 'Masuk berhasil.');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user(), $request->bearerToken());

        return $this->successResponse([], 'Keluar berhasil.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse([
            'user' => UserResource::make($request->user()),
        ], 'Data pengguna yang masuk berhasil diambil.');
    }

    /**
     * @param  array{user: User, token: string, token_type: string}  $payload
     * @return array<string, mixed>
     */
    private function tokenPayload(array $payload): array
    {
        return [
            'user' => UserResource::make($payload['user']),
            'token' => $payload['token'],
            'token_type' => $payload['token_type'],
        ];
    }
}
