<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegisterPageTest extends TestCase
{
    use DatabaseTransactions;

    public function testRegisterForm(): void
    {
        $this->visit('/auth/register')
            ->type('Kim', 'nama_depan')
            ->type('Jisoo', 'nama_belakang')
            ->type('kimjisoo', 'username')
            ->type('kimjisoo@gmail.com', 'email')
            ->type('password123', 'password')
            ->press('Register')
            ->seePageIs('/barang');
    }

    public function testRedirectRegister(): void
    {
        $this->visit('/auth/register')
            ->click('Masuk')
            ->seePageIs('/auth/login');
    }

    public function testRegisterFormEmailTaken(): void
    {
        User::factory()->create(['email' => "kimjisoo@gmail.com"]);
        $this->visit('/auth/register')
            ->type('Kim', 'nama_depan')
            ->type('Jisoo', 'nama_belakang')
            ->type('kimjisoo', 'username')
            ->type('kimjisoo@gmail.com', 'email')
            ->type('password123', 'password')
            ->press('Register')
            ->see('The email has already been taken.')
            ->seePageIs('/auth/register');
    }

    public function testRegisterFormUsernameTaken(): void
    {
        User::factory()->create(['email' => "kimjisoo@gmail.com", "username" => "kimjisoo"]);
        $this->visit('/auth/register')
            ->type('Kim', 'nama_depan')
            ->type('Jisoo', 'nama_belakang')
            ->type('kimjisoo', 'username')
            ->type('kimjisoo@gmail.com', 'email')
            ->type('password123', 'password')
            ->press('Register')
            ->see('The username has already been taken.')
            ->seePageIs('/auth/register');
    }

    public function testRegisterEmptyForm(): void
    {
        $this->visit('/auth/register')
            ->type('', 'nama_depan')
            ->type('', 'nama_belakang')
            ->type('', 'username')
            ->type('', 'email')
            ->type('', 'password')
            ->press('Register')
            ->seePageIs('/auth/register');
    }
}