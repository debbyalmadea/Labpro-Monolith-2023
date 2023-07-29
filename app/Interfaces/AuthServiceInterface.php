<?php

namespace App\Interfaces;

interface AuthServiceInterface extends LoginServiceInterface, RegisterServiceInterface, SelfProfileServiceInterface, LogoutServiceInterface
{
}