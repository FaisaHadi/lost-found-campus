<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    public function __construct(
        private readonly AuthRepository $authRepository
    ) {}

    public function register(array $data): array
    {
        $user = $this->authRepository->createUser([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => UserRole::User->value,
        ]);

        return $this->tokenPayload($user);
    }

    public function login(array $credentials): array
    {
        $user = $this->authRepository->findUserByEmail($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau kata sandi tidak valid.'],
            ]);
        }

        return $this->tokenPayload($user);
    }

    public function logout(User $user, ?string $plainTextToken = null): void
    {
        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();

            return;
        }

        if ($plainTextToken) {
            PersonalAccessToken::findToken($plainTextToken)?->delete();
        }
    }

    private function tokenPayload(User $user): array
    {
        return [
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
            'token_type' => 'Bearer',
        ];
    }
}
