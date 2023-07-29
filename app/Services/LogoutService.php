<?php

namespace App\Services;

use App\Interfaces\LogoutServiceInterface;
use Illuminate\Support\Facades\Cookie;

class LogoutService implements LogoutServiceInterface
{
    public function logout(): array
    {
        $cookie = Cookie::forget('access_token');

        return [
            'cookie' => $cookie
        ];
    }
}