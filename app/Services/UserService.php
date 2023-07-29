<?php

namespace App\Services;

use App\Interfaces\UserServiceInterface;
use App\Models\User;

class UserService implements UserServiceInterface
{
    public function findUserByEmail(string $email): User|null
    {
        $user = User::query()->where('email', $email)->get()->first();

        return $user;
    }
}