<?php

namespace Tests\Feature;

use App\Helpers\JWTAuthHelper;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cookie;
use Tests\TestCase;

class BarangPageTest extends TestCase
{
    use DatabaseTransactions;

    protected $barangBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->barangBuilder = Barang::with('perusahaan', 'perusahaan_id', 'id');
    }
    public function testBarangPage(): void
    {
        $user = User::factory()->create(['email' => 'kimjisoo@gmail.com', 'nama_depan' => 'Kim', 'nama_belakang' => 'Jisoo']);
        $token = (new JWTAuthHelper())->getToken($user);
        $cookie = Cookie::make('access_token', $token, 24 * 60);

        $barang = $this->barangBuilder->get()->first();
        $this->withSession(['access_token' => $cookie->getValue(), 'user' => 'Kim Jisoo'])
            ->visit('/barang')
            ->see('Kim Jisoo')
            ->click('Detail')
            ->seePageIs('/barang/' . $barang->id);
    }
    public function testBarangPageRedirectToRiwayat(): void
    {
        $user = User::factory()->create(['email' => 'kimjisoo@gmail.com', 'nama_depan' => 'Kim', 'nama_belakang' => 'Jisoo']);
        $token = (new JWTAuthHelper())->getToken($user);
        $cookie = Cookie::make('access_token', $token, 24 * 60);

        $this->withSession(['access_token' => $cookie->getValue(), 'user' => 'Kim Jisoo'])
            ->visit('/barang')
            ->click('Riwayat')
            ->seePageIs('/riwayat-pembelian')
            ->see('Riwayat Pembelian Kosong');
    }
    public function testLogOut(): void
    {
        $user = User::factory()->create(['email' => 'kimjisoo@gmail.com', 'nama_depan' => 'Kim', 'nama_belakang' => 'Jisoo']);
        $token = (new JWTAuthHelper())->getToken($user);
        $cookie = Cookie::make('access_token', $token, 24 * 60);

        $this->withSession(['access_token' => $cookie->getValue(), 'user' => 'Kim Jisoo'])
            ->visit('/barang')
            ->click('Log out')
            ->seePageIs('/auth/login');
    }
    public function testBarangPageUnauthorized(): void
    {
        $this
            ->visit('/barang')
            ->seePageIs('/auth/login');
    }
}