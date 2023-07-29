<?php

namespace App\Interfaces;

use App\Models\User;

interface UserServiceInterface
{
    public function findUserByEmail(string $email): User|null;
}