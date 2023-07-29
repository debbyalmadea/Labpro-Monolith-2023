<?php

namespace Tests\Unit;

use App\Enums\HttpStatusCodes;
use App\Exceptions\HttpCustomException;
use App\Models\Barang;
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

    public function testCreateKeranjang()
    {
        $user = User::factory()->create(['email' => "kimjisoo@gmail.com"]);

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

        $this->barangService->expects($this->once())
            ->method('getBarangByIdWithPerusahaan')
            ->with($barang_id)
            ->willReturn($barang);

        $keranjang = $this->keranjangService->createKeranjang($user, $jumlah, $barang_id);

        $this->assertEquals($keranjang->user_id, $user->id);
        $this->assertEquals($keranjang->barang_id, $barang->id);
        $this->assertEquals($keranjang->jumlah_barang, $jumlah);
    }

    public function testCreateKeranjangWhenJumlahLessThanOne()
    {
        $user = User::factory()->create(['email' => "kimjisoo@gmail.com"]);

        $stok = 20;
        $jumlah = 0;
        $barang_id = fake()->uuid();
        $perusahaan_id = fake()->uuid();

        $barang = new Barang([
            'id' => $barang_id,
            'nama' => fake()->company(),
            'kode' => fake()->countryCode(),
            'perusahaan_id' => $perusahaan_id,
            'nama_perusahaan' => fake()->company(),
            'kode_perusahaan' => fake()->countryCode(),
            'alamat_perusahaan' => fake()->address(),
            'harga' => fake()->numberBetween(1000, 100000),
            'stok' => $stok
        ]);

        $this->barangService->expects($this->once())
            ->method('getBarangByIdWithPerusahaan')
            ->with($barang_id)
            ->willReturn($barang);

        $this->expectException(HttpCustomException::class);
        $this->expectExceptionCode(HttpStatusCodes::BAD_REQUEST);
        $this->expectExceptionMessage('Jumlah minimal 1 buah');
        $this->keranjangService->createKeranjang($user, $jumlah, $barang_id);
    }

    public function testCreateKeranjangWhenJumlahMoreThanStok()
    {
        $user = User::factory()->create(['email' => "kimjisoo@gmail.com"]);

        $stok = 20;
        $jumlah = 21;
        $barang_id = fake()->uuid();
        $perusahaan_id = fake()->uuid();

        $barang = new Barang([
            'id' => $barang_id,
            'nama' => fake()->company(),
            'kode' => fake()->countryCode(),
            'perusahaan_id' => $perusahaan_id,
            'nama_perusahaan' => fake()->company(),
            'kode_perusahaan' => fake()->countryCode(),
            'alamat_perusahaan' => fake()->address(),
            'harga' => fake()->numberBetween(1000, 100000),
            'stok' => $stok
        ]);

        $this->barangService->expects($this->once())
            ->method('getBarangByIdWithPerusahaan')
            ->with($barang_id)
            ->willReturn($barang);

        $this->expectException(HttpCustomException::class);
        $this->expectExceptionCode(HttpStatusCodes::BAD_REQUEST);
        $this->expectExceptionMessage('Jumlah melebihi stok');
        $this->keranjangService->createKeranjang($user, $jumlah, $barang_id);
    }

    public function testDeleteKeranjangById()
    {
        $user = User::factory()->create(['email' => "kimjisoo@gmail.com"]);

        $jumlah = 2;
        $barang_id = fake()->uuid();

        $keranjang = (new Keranjang([
            'user_id' => $user->id,
            'barang_id' => $barang_id,
            'jumlah_barang' => $jumlah
        ]));

        $keranjang->save();

        $keranjang = $this->keranjangService->deleteKeranjangById($user, $keranjang->id);

        $this->assertEquals($keranjang->user_id, $user->id);
        $this->assertEquals($keranjang->barang_id, $barang_id);
        $this->assertEquals($keranjang->jumlah_barang, $jumlah);
    }

    public function testDecreaseJumlahBarang()
    {
        $user = User::factory()->create(['email' => "kimjisoo@gmail.com"]);

        $stok = 20;
        $jumlah = 2;
        $barang_id = fake()->uuid();
        $perusahaan_id = fake()->uuid();

        $barang = new Barang([
            'id' => $barang_id,
            'nama' => fake()->company(),
            'kode' => fake()->countryCode(),
            'perusahaan_id' => $perusahaan_id,
            'nama_perusahaan' => fake()->company(),
            'kode_perusahaan' => fake()->countryCode(),
            'alamat_perusahaan' => fake()->address(),
            'harga' => fake()->numberBetween(1000, 100000),
            'stok' => $stok
        ]);

        $keranjang = new Keranjang([
            'user_id' => $user->id,
            'barang_id' => $barang_id,
            'jumlah_barang' => $jumlah
        ]);

        $keranjang->save();

        $this->barangService->expects($this->once())
            ->method('getBarangById')
            ->with($barang_id)
            ->willReturn($barang);

        $keranjang = $this->keranjangService->decreaseJumlahBarang($user, $keranjang->id);

        $this->assertEquals($keranjang->jumlah_barang, $jumlah - 1);
    }

    public function testDecreaseJumlahBarangToZero()
    {
        $user = User::factory()->create(['email' => "kimjisoo@gmail.com"]);

        $stok = 20;
        $jumlah = 1;
        $barang_id = fake()->uuid();
        $perusahaan_id = fake()->uuid();

        $barang = new Barang([
            'id' => $barang_id,
            'nama' => fake()->company(),
            'kode' => fake()->countryCode(),
            'perusahaan_id' => $perusahaan_id,
            'nama_perusahaan' => fake()->company(),
            'kode_perusahaan' => fake()->countryCode(),
            'alamat_perusahaan' => fake()->address(),
            'harga' => fake()->numberBetween(1000, 100000),
            'stok' => $stok
        ]);

        $keranjang = new Keranjang([
            'user_id' => $user->id,
            'barang_id' => $barang_id,
            'jumlah_barang' => $jumlah
        ]);

        $keranjang->save();

        $this->barangService->expects($this->once())
            ->method('getBarangById')
            ->with($barang_id)
            ->willReturn($barang);

        $keranjang = $this->keranjangService->decreaseJumlahBarang($user, $keranjang->id);

        $this->assertEquals($keranjang->jumlah_barang, 0);

        $this->expectException(HttpCustomException::class);
        $this->expectExceptionCode(HttpStatusCodes::NOT_FOUND);
        $this->expectExceptionMessage('Keranjang not found');
        $this->keranjangService->decreaseJumlahBarang($user, $keranjang->id);
    }

    public function testIncreaseJumlahBarang()
    {
        $user = User::factory()->create(['email' => "kimjisoo@gmail.com"]);

        $stok = 3;
        $jumlah = 2;
        $barang_id = fake()->uuid();
        $perusahaan_id = fake()->uuid();

        $barang = new Barang([
            'id' => $barang_id,
            'nama' => fake()->company(),
            'kode' => fake()->countryCode(),
            'perusahaan_id' => $perusahaan_id,
            'nama_perusahaan' => fake()->company(),
            'kode_perusahaan' => fake()->countryCode(),
            'alamat_perusahaan' => fake()->address(),
            'harga' => fake()->numberBetween(1000, 100000),
            'stok' => $stok
        ]);

        $keranjang = new Keranjang([
            'user_id' => $user->id,
            'barang_id' => $barang_id,
            'jumlah_barang' => $jumlah
        ]);

        $keranjang->save();

        $this->barangService->expects($this->exactly(2))
            ->method('getBarangById')
            ->with($barang_id)
            ->willReturn($barang);

        $keranjang = $this->keranjangService->increaseJumlahBarang($user, $keranjang->id);

        $this->assertEquals($keranjang->jumlah_barang, $jumlah + 1);

        $this->expectException(HttpCustomException::class);
        $this->expectExceptionCode(HttpStatusCodes::BAD_REQUEST);
        $this->expectExceptionMessage('Jumlah barang melebihi stok');
        $this->keranjangService->increaseJumlahBarang($user, $keranjang->id);

    }
}