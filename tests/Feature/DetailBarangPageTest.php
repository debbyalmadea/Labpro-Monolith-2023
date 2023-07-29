<?php

namespace Tests\Feature;

use App\Helpers\JWTAuthHelper;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cookie;
use Tests\TestCase;

class DetailBarangPageTest extends TestCase
{
    use DatabaseTransactions;

    protected $barangBuilderMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->barangBuilderMock = Barang::with('perusahaan', 'perusahaan_id', 'id');
    }
    public function testDetailBarangPage(): void
    {
        $user = User::factory()->create(['email' => 'kimjisoo@gmail.com', 'nama_depan' => 'Kim', 'nama_belakang' => 'Jisoo']);
        $token = (new JWTAuthHelper())->getToken($user);
        $cookie = Cookie::make('access_token', $token, 24 * 60);

        $barang = $this->barangBuilderMock->get()->first();
        $this->withSession(['access_token' => $cookie->getValue(), 'user' => 'Kim Jisoo'])
            ->visit('/barang/' . $barang->id)
            ->see($barang->id)
            ->see($barang->nama)
            ->see($barang->harga)
            ->see($barang->stok)
            ->see($barang->nama_perusahaan)
            ->see($barang->kode);
    }
    public function testAddToCartStory(): void
    {
        $user = User::factory()->create(['email' => 'kimjisoo@gmail.com', 'nama_depan' => 'Kim', 'nama_belakang' => 'Jisoo']);
        $token = (new JWTAuthHelper())->getToken($user);
        $cookie = Cookie::make('access_token', $token, 24 * 60);

        $barang = $this->barangBuilderMock->get()->first();
        $this->withSession(['access_token' => $cookie->getValue(), 'user' => 'Kim Jisoo'])
            ->visit('/barang/' . $barang->id)
            ->type($barang->stok + 1, 'jumlah')
            ->seePageIs('/barang/' . $barang->id)
            ->type(2, 'jumlah')
            ->press('Keranjang')
            ->seePageIs('/keranjang')
            ->see($barang->nama)
            ->see('Jumlah: ' . 2)
            ->see('Total harga: ' . $barang->harga * 2)
            ->see('Checkout')
            ->see('Delete')
            ->see('-')
            ->see('+')
            ->press('-')
            ->see('Jumlah: ' . 1)
            ->press('+')
            ->see('Jumlah: ' . 2)
            ->press('-')
            ->press('-')
            ->dontSee($barang->nama)
            ->see('Keranjang Kosong');
    }
    public function testCheckoutStory(): void
    {
        $user = User::factory()->create(['email' => 'kimjisoo@gmail.com', 'nama_depan' => 'Kim', 'nama_belakang' => 'Jisoo']);
        $token = (new JWTAuthHelper())->getToken($user);
        $cookie = Cookie::make('access_token', $token, 24 * 60);

        $barang = $this->barangBuilderMock->get()->first();
        $jumlah = 2;

        $this->withSession(['access_token' => $cookie->getValue(), 'user' => 'Kim Jisoo'])
            ->visit('/barang/' . $barang->id)
            ->type($barang->stok + 1, 'jumlah')
            ->seePageIs('/barang/' . $barang->id)
            ->type($jumlah, 'jumlah')
            ->press('Beli Barang')
            ->see('Pembelian Barang: ' . $barang->nama)
            ->seeInField('jumlah', $jumlah)
            ->see('Harga satuan: ' . $barang->harga)
            ->see('Total harga: ' . $barang->harga * $jumlah)
            ->press('Beli Barang')
            ->see('Riwayat Pembelian')
            ->see($barang->nama)
            ->see('Jumlah: ' . $jumlah)
            ->see('Total harga: ' . $barang->harga * $jumlah);
    }
    public function testDetailBarangPageUnauthorized(): void
    {
        $barang = $this->barangBuilderMock->get()->first();

        $this->visit('/barang/' . $barang->id)
            ->seePageIs('/auth/login');
    }
}