<?php

namespace Tests\Unit;

use App\Helpers\JWTAuthHelper;
use App\Models\User;
use App\Services\RegisterService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cookie;
use Tests\TestCase;

class RegisterServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected $registerService;
    protected $jwtAuthHelper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jwtAuthHelper = $this->createMock(JWTAuthHelper::class);

        $this->registerService = new RegisterService($this->jwtAuthHelper);
    }

    public function testRegister()
    {
        $namaDepan = 'John';
        $namaBelakang = 'Doe';
        $username = 'johndoe';
        $email = 'john.doe@example.com';
        $password = 'secretpassword';

        $sampleToken = 'sample_token';
        $this->jwtAuthHelper->expects($this->once())
            ->method('getToken')
            ->willReturn($sampleToken);

        $result = $this->registerService->register($namaDepan, $namaBelakang, $username, $email, $password);

        $resultUser = $result['user'];
        $this->assertEquals($resultUser->nama_depan, $namaDepan);
        $this->assertEquals($resultUser->nama_belakang, $namaBelakang);
        $this->assertEquals($resultUser->username, $username);
        $this->assertEquals($resultUser->email, $email);
        $this->assertEquals($result['token'], $sampleToken);
        $this->assertEquals($result['cookie'], Cookie::make('access_token', $sampleToken, 24 * 60));
    }

    public function testRegisterEmailAlreadyTaken()
    {
        $existingUser = User::all()->first();

        if (!$existingUser) {
            $existingUser = User::factory()->create()->first();
        }

        $this->expectException(QueryException::class);
        $this->registerService->register(
            'John',
            'Doe',
            'johndoe',
            $existingUser->email,
            'secretpassword'
        );
    }

    public function testRegisterUsernameAlreadyTaken()
    {
        $existingUser = User::all()->first();

        if (!$existingUser) {
            $existingUser = User::factory()->create()->first();
        }

        $this->expectException(QueryException::class);
        $this->registerService->register(
            'John',
            'Doe',
            $existingUser->username,
            'johndoe@gmail.com',
            'secretpassword'
        );
    }
}