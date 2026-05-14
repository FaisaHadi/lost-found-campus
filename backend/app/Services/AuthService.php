<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(
        private readonly AuthRepository $authRepository,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    /**
     * @param array{name:string,email:string,password:string} $data
     * @return array{user:User,token:string,token_type:string}
     */
    public function register(array $data): array
    {
        $user = $this->authRepository->createUser([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => UserRole::User->value,
        ]);

        $this->activityLogService->record('register', 'Pengguna baru melakukan registrasi.', $user, $user);

        return $this->tokenPayload($user);
    }

    /**
     * @param array{email:string,password:string} $credentials
     * @return array{user:User,token:string,token_type:string}
     */
    public function login(array $credentials): array
    {
        $user = $this->authRepository->findUserByEmail($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau kata sandi tidak valid.'],
            ]);
        }

        $this->activityLogService->record('login', 'Pengguna berhasil masuk.', $user, $user);

        return $this->tokenPayload($user);
    }

    public function logout(User $user, ?string $plainTextToken = null): void
    {
        if ($plainTextToken) {
            JWTAuth::setToken($plainTextToken)->invalidate();
        }

        $this->activityLogService->record('logout', 'Pengguna keluar dari sistem.', $user, $user);
    }

    /**
     * @return array{user:User,token:string,token_type:string}
     */
    private function tokenPayload(User $user): array
    {
        return [
            'user' => $user,
            'token' => JWTAuth::fromUser($user),
            'token_type' => 'Bearer',
        ];
    }
}
