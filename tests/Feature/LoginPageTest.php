<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginPageTest extends TestCase
{
    public function testLoginForm(): void
    {
        $this->visit('/auth/login')
            ->type('berrygood@gmail.com', 'email')
            ->type('password123', 'password')
            ->press('Log in')
            ->seePageIs('/barang');
    }

    public function testRedirectRegister(): void
    {
        $this->visit('/auth/login')
            ->click('Daftar')
            ->seePageIs('/auth/register');
    }

    public function testLoginFormEmailNotFound(): void
    {
        $this->visit('/auth/login')
            ->type('bary@gmail.com', 'email')
            ->type('password123', 'password')
            ->press('Log in')
            ->see('Invalid Credentials')
            ->seePageIs('/auth/login');
    }

    public function testLoginFormPasswordInvalid(): void
    {
        $this->visit('/auth/login')
            ->type('berrygood@gmail.com', 'email')
            ->type('invalidpassword', 'password')
            ->press('Log in')
            ->see('Invalid Credentials')
            ->seePageIs('/auth/login');
    }

    public function testLoginEmptyForm(): void
    {
        $this->visit('/auth/login')
            ->type('', 'email')
            ->type('', 'password')
            ->press('Log in')
            ->seePageIs('/auth/login');
    }
}