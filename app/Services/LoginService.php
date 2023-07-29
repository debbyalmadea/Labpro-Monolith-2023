<?php

namespace App\Services;

use App\Helpers\JWTAuthHelper;
use App\Interfaces\LoginServiceInterface;
use App\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class LoginService implements LoginServiceInterface
{
    private $jwtAuthHelper;
    private $userService;
    public function __construct(
        UserServiceInterface $userService,
        JWTAuthHelper $jwtAuthHelper,
    ) {
        $this->userService = $userService;
        $this->jwtAuthHelper = $jwtAuthHelper;
    }

    public function login(string $email, string $password): array|null
    {
        $user = $this->userService->findUserByEmail($email);

        if (!$user) {
            return null;
        }

        if (!Hash::check($password, $user->password)) {
            return null;
        }

        $token = $this->jwtAuthHelper->getToken($user);
        $cookie = Cookie::make('access_token', $token, 24 * 60);

        session(['user' => $user->nama_depan . ' ' . $user->nama_belakang]);

        return [
            'user' => $user,
            'token' => $token,
            'cookie' => $cookie
        ];
    }
}