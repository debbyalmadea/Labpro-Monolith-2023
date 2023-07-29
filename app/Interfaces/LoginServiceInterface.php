<?php

namespace App\Interfaces;

interface LoginServiceInterface
{
    public function login(string $email, string $password): array|null;
}