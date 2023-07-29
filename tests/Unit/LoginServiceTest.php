<?php

namespace Tests\Unit;

use App\Helpers\JWTAuthHelper;
use App\Interfaces\UserServiceInterface;
use App\Models\User;
use App\Services\LoginService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginServiceTest extends TestCase
{
    private $jwtAuthHelper;
    private $userService;
    private $loginService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jwtAuthHelper = $this->createMock(JWTAuthHelper::class);
        $this->userService = $this->createMock(UserServiceInterface::class);
        $this->loginService = new LoginService($this->userService, $this->jwtAuthHelper);
    }

    public function testLogin()
    {
        $user = new User([
            'id' => 1,
            'nama_depan' => 'John',
            'nama_belakang' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->userService->expects($this->once())
            ->method('findUserByEmail')
            ->with('john.doe@example.com')
            ->willReturn($user);

        $token = 'sample_token';
        $this->jwtAuthHelper->expects($this->once())
            ->method('getToken')
            ->with($user)
            ->willReturn($token);

        $result = $this->loginService->login('john.doe@example.com', 'password');

        $resultUser = $result['user'];
        $this->assertEquals($resultUser->id, $user->id);
        $this->assertEquals($resultUser->email, $user->email);
        $this->assertEquals($resultUser->username, $user->username);
        $this->assertEquals($resultUser->nama_depan, $user->nama_depan);
        $this->assertEquals($resultUser->nama_belakang, $user->nama_belakang);
        $this->assertArrayHasKey('token', $result);
        $this->assertEquals($result['cookie']->getValue(), $token);
    }

    public function testInvalidPassword()
    {
        $user = new User([
            'id' => 1,
            'nama_depan' => 'John',
            'nama_belakang' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->userService->expects($this->once())
            ->method('findUserByEmail')
            ->with('john.doe@example.com')
            ->willReturn($user);

        $invalidPassword = 'invalid_password';

        $result = $this->loginService->login('john.doe@example.com', $invalidPassword);

        $this->assertNull($result);

    }
}