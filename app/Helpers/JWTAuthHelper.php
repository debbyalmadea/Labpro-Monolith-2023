<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class JWTAuthHelper
{
    public function getToken(User $user)
    {
        return JWTAuth::encode(JWTFactory::sub($user->id)
            ->user($user)
            ->make())->get();
    }

    public function parseToken(string $token)
    {
        return JWTAuth::setToken($token)->getPayload()->toArray();
    }

    public function getUserFromToken(string $token)
    {
        try {
            $parsedToken = $this->parseToken($token);
            $data = $parsedToken['user'];
            if (User::query()->where('id', $data['id'])) {
                return (new User())->fill($data);
            }

            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function getUser()
    {
        try {
            $token = Cookie::get('access_token');
            if (!$token && Session::has('access_token')) {
                $token = Session::get('access_token');
            }
            return $this->getUserFromToken($token);
        } catch (\Throwable $th) {
            return null;
        }
    }
}