<?php

namespace Tests\Unit;

use App\Enums\HttpStatusCodes;
use App\Exceptions\HttpCustomException;
use App\Helpers\JWTAuthHelper;
use App\Services\SelfProfileService;
use PHPUnit\Framework\TestCase;

class SelfProfileServiceTest extends TestCase
{
    private $jwtAuthHelperMock;
    private $selfProfileService;
    protected function setUp(): void
    {
        parent::setUp();

        $this->jwtAuthHelperMock = $this->createMock(JWTAuthHelper::class);

        $this->selfProfileService = new SelfProfileService($this->jwtAuthHelperMock);
    }
    public function testSelfProfile()
    {
        $token = 'valid_jwt_token';

        $user = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ];
        $this->jwtAuthHelperMock->expects($this->once())->method('getUserFromToken')->with($token)
            ->willReturn($user);

        $result = $this->selfProfileService->self($token);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals($user, $result['user']);
    }

    public function testSelfProfileUnauthorized()
    {
        $token = 'invalid_jwt_token';

        $this->jwtAuthHelperMock->expects($this->once())->method('getUserFromToken')->with($token)
            ->willReturn(null);

        $this->expectException(HttpCustomException::class);
        $this->expectExceptionCode(HttpStatusCodes::UNAUTHORIZED);
        $this->expectExceptionMessage('Unauthorized');
        $this->selfProfileService->self($token);
    }
}