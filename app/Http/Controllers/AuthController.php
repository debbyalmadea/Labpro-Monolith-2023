<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Interfaces\AuthServiceInterface;

class AuthController extends Controller
{
    protected $authService;
    function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    function viewLogin()
    {
        return view('auth.login.index');
    }

    function viewRegister()
    {
        return view('auth.register.index');
    }

    function login(LoginRequest $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $auth = $this->authService->login($email, $password);

        if (!$auth) {
            return redirect('/auth/login')->with('error', 'Invalid Credentials');
        }

        return redirect('/barang')->with('success', 'Login berhasil! Selamat datang!')->cookie($auth['cookie']);
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

        return redirect('/barang')->with('success', 'Registrasi berhasil! Selamat datang!')->cookie($auth['cookie']);
    }


    function logout()
    {
        $auth = $this->authService->logout();
        return redirect('/auth/login')->with('success', 'Log out berhasil')->cookie($auth['cookie']);
    }
}