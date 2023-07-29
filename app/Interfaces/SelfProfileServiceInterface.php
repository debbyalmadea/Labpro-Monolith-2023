<?php

namespace App\Interfaces;

interface SelfProfileServiceInterface
{
    public function self(string $token): array;
}