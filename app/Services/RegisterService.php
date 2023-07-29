<?php

namespace App\Services;

use App\Helpers\JWTAuthHelper;
use App\Interfaces\RegisterServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;

class RegisterService implements RegisterServiceInterface
{
    private $jwtAuthHelper;
    public function __construct(
        JWTAuthHelper $jwtAuthHelper,
    ) {
        $this->jwtAuthHelper = $jwtAuthHelper;
    }
    public function register(string $nama_depan, string $nama_belakang, string $username, string $email, string $password): array
    {
        $user = User::create([
            'nama_depan' => $nama_depan,
            'nama_belakang' => $nama_belakang,
            'username' => $username,
            'email' => $email,
            'password' => $password
        ]);

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