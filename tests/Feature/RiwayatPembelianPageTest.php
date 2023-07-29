<?php

namespace Tests\Feature;

use App\Helpers\JWTAuthHelper;
use App\Models\RiwayatPembelian;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cookie;
use Tests\TestCase;

class RiwayatPembelianPageTest extends TestCase
{
    use DatabaseTransactions;

    protected function formatDate($date)
    {
        return \Carbon\Carbon::parse($date)->setTimezone('Asia/Jakarta')->formatLocalized('%e %b %Y, %H:%M:%S');
    }
    public function testRiwayatPembelianPage(): void
    {
        $user = User::factory()->create(['email' => "kimjisoo@gmail.com"]);

        $createdRiwayatPembelian = collect([]);

        for ($i = 0; $i < 2; $i++) {
            $createdRiwayatPembelian->add(RiwayatPembelian::factory()->create([
                'user_id' => $user->id,
                'barang_id' => fake()->uuid(),
                'nama_barang' => fake()->company(),
                'kode_barang' => fake()->countryCode(),
                'perusahaan_id' => fake()->uuid(),
                'nama_perusahaan' => fake()->company(),
                'harga_barang' => fake()->numberBetween(1000, 100000),
                'jumlah_barang' => fake()->numberBetween(1, 5),
            ]));
        }

        $token = (new JWTAuthHelper())->getToken($user);
        $cookie = Cookie::make('access_token', $token, 24 * 60);

        $this->withSession(['access_token' => $cookie->getValue(), 'user' => 'Kim Jisoo'])
            ->visit('/riwayat-pembelian')
            ->see($createdRiwayatPembelian[0]->nama_barang)
            ->see('Total harga: ' . $createdRiwayatPembelian[0]->harga_barang * $createdRiwayatPembelian[0]->jumlah_barang)
            ->see('Jumlah: ' . $createdRiwayatPembelian[0]->jumlah_barang)
            ->see($this->formatDate($createdRiwayatPembelian[0]->created_at))
            ->see($createdRiwayatPembelian[1]->nama_barang)
            ->see('Total harga: ' . $createdRiwayatPembelian[1]->harga_barang * $createdRiwayatPembelian[1]->jumlah_barang)
            ->see('Jumlah: ' . $createdRiwayatPembelian[1]->jumlah_barang)
            ->see($this->formatDate($createdRiwayatPembelian[1]->created_at));
    }
    public function testRiwayatPembelianPagePagination(): void
    {
        $user = User::factory()->create(['email' => "kimjisoo@gmail.com"]);

        $createdRiwayatPembelian = collect([]);

        for ($i = 0; $i < 20; $i++) {
            $createdRiwayatPembelian->add(RiwayatPembelian::factory()->create([
                'user_id' => $user->id,
                'barang_id' => fake()->uuid(),
                'nama_barang' => fake()->company(),
                'kode_barang' => fake()->countryCode(),
                'perusahaan_id' => fake()->uuid(),
                'nama_perusahaan' => fake()->company(),
                'harga_barang' => fake()->numberBetween(1000, 100000),
                'jumlah_barang' => fake()->numberBetween(1, 5),
            ]));
        }

        $token = (new JWTAuthHelper())->getToken($user);
        $cookie = Cookie::make('access_token', $token, 24 * 60);

        $this->withSession(['access_token' => $cookie->getValue(), 'user' => 'Kim Jisoo'])
            ->visit('/riwayat-pembelian')
            ->click('›')
            ->seePageIs('/riwayat-pembelian?page=2')
            ->see($createdRiwayatPembelian[15]->nama_barang)
            ->see('Total harga: ' . $createdRiwayatPembelian[15]->harga_barang * $createdRiwayatPembelian[15]->jumlah_barang)
            ->see('Jumlah: ' . $createdRiwayatPembelian[15]->jumlah_barang)
            ->see($this->formatDate($createdRiwayatPembelian[15]->created_at))
            ->click('‹')
            ->seePageIs('/riwayat-pembelian?page=1')
            ->see($createdRiwayatPembelian[0]->nama_barang)
            ->see('Total harga: ' . $createdRiwayatPembelian[0]->harga_barang * $createdRiwayatPembelian[0]->jumlah_barang)
            ->see('Jumlah: ' . $createdRiwayatPembelian[0]->jumlah_barang)
            ->see($this->formatDate($createdRiwayatPembelian[0]->created_at));
    }
    public function testRiwayatPembelianPageUnauthorized(): void
    {
        $this->visit('/riwayat-pembelian')
            ->seePageIs('/auth/login');
    }
}