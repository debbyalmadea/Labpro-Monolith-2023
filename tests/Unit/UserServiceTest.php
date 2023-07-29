<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use DatabaseTransactions;

    private $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = new UserService();
    }

    public function testFindUserByEmail()
    {
        $user = User::all()->first();

        if (!$user) {
            $user = User::factory()->create()->first();
        }

        $foundUser = $this->userService->findUserByEmail($user->email);
        $this->assertEquals($foundUser->email, $user->email);
    }

    public function testFindUserByEmailNotFound()
    {
        $foundUser = $this->userService->findUserByEmail('iNvaLidEmaIl');
        $this->assertEquals($foundUser, null);
    }
}