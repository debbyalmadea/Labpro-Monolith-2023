<?php

namespace Tests\Unit;

use App\Models\Keranjang;
use App\Models\User;
use App\Services\BarangService;
use App\Services\KeranjangService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KeranjangServiceTest extends TestCase
{
    use DatabaseTransactions;

    private $barangService;
    private $keranjangService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->barangService = $this->createMock(BarangService::class);
        $this->keranjangService = new KeranjangService($this->barangService);
    }

    public function testGetKeranjang()
    {
        $user = User::factory()->create(['email' => "kimjisoo@gmail.com"]);

        $createdKeranjang = Keranjang::factory(5)->create([
            'user_id' => $user->id,
            'barang_id' => fake()->uuid(),
            'jumlah_barang' => fake()->numberBetween(1, 5),
        ]);

        $keranjang = $this->keranjangService->getKeranjang($user);

        $this->assertEquals($createdKeranjang->toArray(), $keranjang->toArray());
    }
}