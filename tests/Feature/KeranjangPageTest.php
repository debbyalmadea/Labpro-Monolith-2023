<?php

namespace Tests\Feature;

use App\Helpers\JWTAuthHelper;
use App\Models\Barang;
use App\Models\Keranjang;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cookie;
use Tests\TestCase;

class KeranjangPageTest extends TestCase
{
    use DatabaseTransactions;

    protected $barangBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->barangBuilder = Barang::with('perusahaan', 'perusahaan_id', 'id');
    }
    protected function formatDate($date)
    {
        return \Carbon\Carbon::parse($date)->setTimezone('Asia/Jakarta')->formatLocalized('%e %b %Y, %H:%M:%S');
    }
    public function testKeranjangPage(): void
    {
        $user = User::factory()->create(['email' => "kimjisoo@gmail.com"]);

        $createdKeranjang = collect([]);

        for ($i = 0; $i < 2; $i++) {
            $createdKeranjang->add(Keranjang::create([
                'user_id' => $user->id,
                'barang_id' => ($this->barangBuilder->get()->values())[$i]->id,
                'jumlah_barang' => fake()->numberBetween(1, 5),
            ]));
        }

        $token = (new JWTAuthHelper())->getToken($user);
        $cookie = Cookie::make('access_token', $token, 24 * 60);

        $this->withSession(['access_token' => $cookie->getValue(), 'user' => 'Kim Jisoo'])
            ->visit('/keranjang')
            ->see(($this->barangBuilder->get()->values())[0]->nama)
            ->see('Total harga: ' . ($this->barangBuilder->get()->values())[0]->harga * $createdKeranjang[0]->jumlah_barang)
            ->see('Jumlah: ' . $createdKeranjang[0]->jumlah_barang)
            ->see($this->formatDate($createdKeranjang[0]->created_at))
            ->see(($this->barangBuilder->get()->values())[0]->nama)
            ->see('Total harga: ' . ($this->barangBuilder->get()->values())[1]->harga * $createdKeranjang[1]->jumlah_barang)
            ->see('Jumlah: ' . $createdKeranjang[1]->jumlah_barang)
            ->see($this->formatDate($createdKeranjang[1]->created_at));
    }
    public function testKeranjangPagePagination(): void
    {
        $user = User::factory()->create(['email' => "kimjisoo@gmail.com"]);

        $createdKeranjang = collect([]);
        $barang = $this->barangBuilder->get()->first();
        for ($i = 0; $i < 20; $i++) {
            $createdKeranjang->add(Keranjang::create([
                'user_id' => $user->id,
                'barang_id' => $barang->id,
                'jumlah_barang' => fake()->numberBetween(1, 5),
            ]));
        }

        $token = (new JWTAuthHelper())->getToken($user);
        $cookie = Cookie::make('access_token', $token, 24 * 60);

        $this->withSession(['access_token' => $cookie->getValue(), 'user' => 'Kim Jisoo'])
            ->visit('/keranjang')
            ->click('›')
            ->seePageIs('/keranjang?page=2')
            ->see($barang->nama)
            ->see('Total harga: ' . $barang->harga * $createdKeranjang[15]->jumlah_barang)
            ->see('Jumlah: ' . $createdKeranjang[15]->jumlah_barang)
            ->see($this->formatDate($createdKeranjang[15]->created_at))
            ->click('‹')
            ->seePageIs('/keranjang?page=1')
            ->see('Total harga: ' . $barang->harga * $createdKeranjang[0]->jumlah_barang)
            ->see('Jumlah: ' . $createdKeranjang[0]->jumlah_barang)
            ->see($this->formatDate($createdKeranjang[0]->created_at));
    }

    public function testChangeJumlahBarangStory(): void
    {
        $user = User::factory()->create(['email' => "kimjisoo@gmail.com"]);

        $barang = $this->barangBuilder->get()->first();
        $createdKeranjang = Keranjang::create([
            'user_id' => $user->id,
            'barang_id' => $barang->id,
            'jumlah_barang' => 2,
        ]);

        $token = (new JWTAuthHelper())->getToken($user);
        $cookie = Cookie::make('access_token', $token, 24 * 60);

        $this->withSession(['access_token' => $cookie->getValue(), 'user' => 'Kim Jisoo'])
            ->visit('/keranjang')
            ->see($barang->nama)
            ->see('Total harga: ' . $barang->harga * $createdKeranjang->jumlah_barang)
            ->see('Jumlah: ' . $createdKeranjang->jumlah_barang)
            ->see($this->formatDate($createdKeranjang->created_at))
            ->press('-')
            ->see('Jumlah: ' . 1)
            ->see('Total harga: ' . $barang->harga * 1)
            ->press('+')
            ->see('Jumlah: ' . 2)
            ->see('Total harga: ' . $barang->harga * 2)
            ->press('-')
            ->press('-')
            ->dontSee($barang->nama)
            ->see('Keranjang Kosong');
    }

    public function testCheckoutBarangStory(): void
    {
        $user = User::factory()->create(['email' => "kimjisoo@gmail.com"]);

        $barang = $this->barangBuilder->get()->first();
        $createdKeranjang = Keranjang::create([
            'user_id' => $user->id,
            'barang_id' => $barang->id,
            'jumlah_barang' => 2,
        ]);

        $token = (new JWTAuthHelper())->getToken($user);
        $cookie = Cookie::make('access_token', $token, 24 * 60);

        $this->withSession(['access_token' => $cookie->getValue(), 'user' => 'Kim Jisoo'])
            ->visit('/keranjang')
            ->see($barang->nama)
            ->see('Total harga: ' . $barang->harga * $createdKeranjang->jumlah_barang)
            ->see('Jumlah: ' . $createdKeranjang->jumlah_barang)
            ->see($this->formatDate($createdKeranjang->created_at))
            ->press('Checkout')
            ->see('Riwayat Pembelian')
            ->see($barang->nama)
            ->see('Total harga: ' . $barang->harga * $createdKeranjang->jumlah_barang)
            ->see('Jumlah: ' . $createdKeranjang->jumlah_barang);
    }

    public function testDeleteBarangStory(): void
    {
        $user = User::factory()->create(['email' => "kimjisoo@gmail.com"]);

        $barang = $this->barangBuilder->get()->first();
        $createdKeranjang = Keranjang::create([
            'user_id' => $user->id,
            'barang_id' => $barang->id,
            'jumlah_barang' => 2,
        ]);

        $token = (new JWTAuthHelper())->getToken($user);
        $cookie = Cookie::make('access_token', $token, 24 * 60);

        $this->withSession(['access_token' => $cookie->getValue(), 'user' => 'Kim Jisoo'])
            ->visit('/keranjang')
            ->see($barang->nama)
            ->see('Total harga: ' . $barang->harga * $createdKeranjang->jumlah_barang)
            ->see('Jumlah: ' . $createdKeranjang->jumlah_barang)
            ->see($this->formatDate($createdKeranjang->created_at))
            ->press('Delete')
            ->see('Keranjang Kosong');
    }
    public function testKeranjangPageUnauthorized(): void
    {
        $this->visit('/keranjang')
            ->seePageIs('/auth/login');
    }
}