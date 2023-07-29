<?php

namespace Tests\Unit;

use App\Models\Barang;
use App\Models\RiwayatPembelian;
use App\Models\User;
use App\Services\RiwayatPembelianService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RiwayatPembelianServiceTest extends TestCase
{
    use DatabaseTransactions;

    private $riwayatPembelianService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->riwayatPembelianService = new RiwayatPembelianService();
    }

    public function testGetRiwayatPembelian()
    {
        $user = User::factory()->create(['email' => "johndoe@gmail.com"]);

        $createdRiwayatPembelian = RiwayatPembelian::factory(5)->create([
            'user_id' => $user->id,
            'barang_id' => fake()->uuid(),
            'nama_barang' => fake()->company(),
            'kode_barang' => fake()->countryCode(),
            'perusahaan_id' => fake()->uuid(),
            'nama_perusahaan' => fake()->company(),
            'harga_barang' => fake()->numberBetween(1000, 100000),
            'jumlah_barang' => fake()->numberBetween(1, 5),
        ]);

        $riwayatPembelian = $this->riwayatPembelianService->getRiwayatPembelian($user);

        $this->assertEquals($createdRiwayatPembelian->toArray(), $riwayatPembelian->toArray());
    }

    public function testGetRiwayatPembelianWithPagination()
    {
        $user = User::factory()->create(['email' => "johndoe@gmail.com"]);

        $createdRiwayatPembelian = RiwayatPembelian::factory(5)->create([
            'user_id' => $user->id,
            'barang_id' => fake()->uuid(),
            'nama_barang' => fake()->company(),
            'kode_barang' => fake()->countryCode(),
            'perusahaan_id' => fake()->uuid(),
            'nama_perusahaan' => fake()->company(),
            'harga_barang' => fake()->numberBetween(1000, 100000),
            'jumlah_barang' => fake()->numberBetween(1, 5),
        ]);

        $perPage = 2;
        $riwayatPembelian = $this->riwayatPembelianService->getRiwayatPembelian($user, $perPage);

        $this->assertEquals($perPage, count($riwayatPembelian));
        $this->assertEquals($createdRiwayatPembelian->slice(0, $perPage)->values()->toArray(), $riwayatPembelian->values()->toArray());
    }

    public function testCreateRiwayatPembelian()
    {
        $user = User::factory()->create(['email' => "johndoe@gmail.com"]);

        $jumlah = 5;
        $barang_id = fake()->uuid();
        $stok = 12;

        $barang = new Barang([
            'id' => $barang_id,
            'nama' => fake()->company(),
            'kode' => fake()->countryCode(),
            'perusahaan_id' => fake()->uuid(),
            'nama_perusahaan' => fake()->company(),
            'harga' => fake()->numberBetween(1000, 100000),
            'stok' => $stok
        ]);

        $riwayatPembelian = $this->riwayatPembelianService->createRiwayatPembelian($user, $barang, $jumlah);
        $this->assertEquals($riwayatPembelian->user_id, $user->id);
        $this->assertEquals($riwayatPembelian->barang_id, $barang->id);
        $this->assertEquals($riwayatPembelian->nama_barang, $barang->nama);
        $this->assertEquals($riwayatPembelian->kode_barang, $barang->kode);
        $this->assertEquals($riwayatPembelian->perusahaan_id, $barang->perusahaan_id);
        $this->assertEquals($riwayatPembelian->nama_perusahaan, $barang->nama_perusahaan);
        $this->assertEquals($riwayatPembelian->harga_barang, $barang->harga);
        $this->assertEquals($riwayatPembelian->jumlah_barang, $jumlah);
    }
}