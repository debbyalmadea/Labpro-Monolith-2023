<?php

namespace App\Services;

use App\Interfaces\AuthServiceInterface;
use App\Interfaces\LoginServiceInterface;
use App\Interfaces\LogoutServiceInterface;
use App\Interfaces\RegisterServiceInterface;
use App\Interfaces\SelfProfileServiceInterface;

class AuthService implements AuthServiceInterface
{
    private $loginService;
    private $registrationService;
    private $selfProfileService;
    private $logoutService;

    public function __construct(
        LoginServiceInterface $loginService,
        RegisterServiceInterface $registrationService,
        SelfProfileServiceInterface $selfProfileService,
        LogoutServiceInterface $logoutService
    ) {
        $this->loginService = $loginService;
        $this->registrationService = $registrationService;
        $this->selfProfileService = $selfProfileService;
        $this->logoutService = $logoutService;
    }

    public function login(string $email, string $password): array|null
    {
        return $this->loginService->login($email, $password);
    }

    public function register(string $nama_depan, string $nama_belakang, string $username, string $email, string $password): array
    {
        return $this->registrationService->register($nama_depan, $nama_belakang, $username, $email, $password);
    }

    public function self(string $token): array
    {
        return $this->selfProfileService->self($token);
    }

    public function logout(): array
    {
        return $this->logoutService->logout();
    }
}