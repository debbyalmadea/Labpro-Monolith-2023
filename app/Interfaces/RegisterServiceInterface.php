<?php

namespace App\Interfaces;

interface RegisterServiceInterface
{
    public function register(string $nama_depan, string $nama_belakang, string $username, string $email, string $password): array;
}