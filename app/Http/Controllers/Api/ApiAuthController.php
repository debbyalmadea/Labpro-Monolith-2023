<?php

namespace App\Http\Controllers\Api;

use App\Enums\HttpStatusCodes;

use App\Http\Controllers\Controller;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Responses\JsonResponse;
use App\Interfaces\AuthServiceInterface;
use Illuminate\Http\Request;

class ApiAuthController extends Controller
{
    protected $authService;
    function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    function login(LoginRequest $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $auth = $this->authService->login($email, $password);

        if (!$auth) {
            return JsonResponse::error('Invalid Credentials', HttpStatusCodes::UNAUTHORIZED);
        }

        return JsonResponse::success([
            'user' => $auth['user'],
            'token' => $auth['token']
        ], 'Log in success', HttpStatusCodes::OK)->withCookie($auth['cookie']);
    }

    function register(RegisterRequest $request)
    {
        $auth = $this->authService->register(
            $request->input('nama_depan'),
            $request->input('nama_belakang'),
            $request->input('username'),
            $request->input('email'),
            $request->input('password')
        );

        return JsonResponse::success([
            'user' => $auth['user'],
            'token' => $auth['token']
        ], 'Register success', HttpStatusCodes::OK)->withCookie($auth['cookie']);
    }

    function self(Request $request)
    {
        $header = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $header);

        if (!$token) {
            $token = $request->cookie('access_token');
        }

        $auth = $this->authService->self($token);

        return JsonResponse::success($auth, 'Successfully retrieved data', HttpStatusCodes::OK);
    }
}