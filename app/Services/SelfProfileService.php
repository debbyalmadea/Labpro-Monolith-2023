<?php

namespace App\Services;

use App\Enums\HttpStatusCodes;
use App\Exceptions\HttpCustomException;
use App\Helpers\JWTAuthHelper;
use App\Interfaces\SelfProfileServiceInterface;

class SelfProfileService implements SelfProfileServiceInterface
{
    private $jwtAuthHelper;
    public function __construct(
        JWTAuthHelper $jwtAuthHelper,
    ) {
        $this->jwtAuthHelper = $jwtAuthHelper;
    }
    public function self(string $token): array
    {
        $user = $this->jwtAuthHelper->getUserFromToken($token);

        if (!$user) {
            throw new HttpCustomException(HttpStatusCodes::UNAUTHORIZED, 'Unauthorized', '/auth/login');
        }

        return [
            'user' => $user
        ];
    }
}