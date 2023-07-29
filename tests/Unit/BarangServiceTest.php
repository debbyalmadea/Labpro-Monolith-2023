<?php

namespace Tests\Unit;

use App\Enums\HttpStatusCodes;
use App\Exceptions\HttpCustomException;
use App\Models\Barang;
use App\Models\RiwayatPembelian;
use App\Models\User;
use App\Services\BarangService;
use App\Services\RiwayatPembelianService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class BarangServiceTest extends TestCase
{
    use DatabaseTransactions;
    protected $riwayatPembelianService;
    protected $barangService;
    protected $apiMock;
    protected function setUp(): void
    {
        parent::setUp();

        $this->apiMock = \Mockery::mock('overload:App\Libraries\Api');

        $this->riwayatPembelianService = $this->createMock(RiwayatPembelianService::class);
        $this->barangService = new BarangService($this->riwayatPembelianService);
    }
    public function testGetManyBarang()
    {
        $barangList = collect([]);
        for ($i = 0; $i < 10; $i++) {
            $barangList->add([
                'id' => fake()->uuid(),
                'nama' => fake()->company(),
                'kode' => fake()->countryCode(),
                'perusahaan_id' => fake()->uuid(),
                'harga' => fake()->numberBetween(1000, 100000),
                'stok' => 20
            ]);
        }

        $this->apiMock->shouldReceive('connection')->andReturn($this->apiMock);
        $this->apiMock->shouldReceive('get')->with('barang')->andReturn($barangList);


        $barangList = $this->barangService->getManyBarang();

        $this->assertCount(10, $barangList);
    }

    public function testGetManyBaranWithPerusahaan()
    {
        $perusahaanIds = collect([]);
        $barangList = collect([]);
        for ($i = 0; $i < 10; $i++) {
            $perusahaan_id = fake()->uuid();
            $perusahaanIds->add($perusahaan_id);
            $barangList->add([
                'id' => fake()->uuid(),
                'nama' => fake()->company(),
                'kode' => fake()->countryCode(),
                'perusahaan_id' => $perusahaan_id,
                'harga' => fake()->numberBetween(1000, 100000),
                'stok' => 20
            ]);
        }

        $perusahaanList = collect([]);
        foreach ($perusahaanIds as $id) {
            $perusahaanList->add([
                'id' => $id,
                'nama' => fake()->company(),
                'kode' => fake()->countryCode(),
                'alamat' => fake()->address(),
            ]);
        }

        $this->apiMock->shouldReceive('connection')->andReturn($this->apiMock);
        $this->apiMock->shouldReceive('get')->with('barang')->andReturn($barangList);
        $this->apiMock->shouldReceive('get')->with('perusahaan')->andReturn($perusahaanList);

        $barangList = $this->barangService->getManyBarangWithPerusahaan();

        $this->assertCount(10, $barangList);
        foreach ($barangList as $barang) {
            $this->assertArrayHasKey('nama_perusahaan', $barang->toArray());
        }
    }

    public function testGetBarangById()
    {
        $barang_id = fake()->uuid();
        $barang = [
            'id' => $barang_id,
            'nama' => fake()->company(),
            'kode' => fake()->countryCode(),
            'perusahaan_id' => fake()->uuid(),
            'harga' => fake()->numberBetween(1000, 100000),
            'stok' => 20
        ];

        $this->apiMock->shouldReceive('connection')->andReturn($this->apiMock);
        $this->apiMock->shouldReceive('get')->with('barang/' . $barang_id)->andReturn($barang);

        $fetchedBarang = $this->barangService->getBarangById($barang_id);
        $this->assertEquals($barang, $fetchedBarang->toArray());
    }

    public function testGetBarangByIdNotFound()
    {
        $barang_id = fake()->uuid();

        $this->apiMock->shouldReceive('connection')->andReturn($this->apiMock);
        $this->apiMock->shouldReceive('get')->with('barang/' . $barang_id)->andThrow(new HttpCustomException(HttpStatusCodes::NOT_FOUND, 'Not Found'));

        $this->expectException(HttpCustomException::class);
        $this->expectExceptionCode(HttpStatusCodes::NOT_FOUND);
        $this->expectExceptionMessage('Barang Not Found');
        $this->barangService->getBarangById($barang_id);
    }

    public function testGetBarangByIdWithPerusahaan()
    {
        $barang_id = fake()->uuid();
        $perusahaan_id = fake()->uuid();
        $barang = [
            'id' => $barang_id,
            'nama' => fake()->company(),
            'kode' => fake()->countryCode(),
            'perusahaan_id' => $perusahaan_id,
            'harga' => fake()->numberBetween(1000, 100000),
            'stok' => 20
        ];

        $perusahaan = [
            'id' => $perusahaan_id,
            'nama' => fake()->company(),
            'kode' => fake()->countryCode(),
            'alamat' => fake()->address(),
        ];

        $this->apiMock->shouldReceive('connection')->andReturn($this->apiMock);
        $this->apiMock->shouldReceive('get')->with('barang/' . $barang_id)->andReturn($barang);

        $this->apiMock->shouldReceive('get')->with('perusahaan/' . $perusahaan_id)->andReturn($perusahaan);

        $fetchedBarang = $this->barangService->getBarangByIdWithPerusahaan($barang_id);
        $this->assertArrayHasKey('nama_perusahaan', $fetchedBarang->toArray());
    }

    public function testGetBarangByIdWithPerusahaanWherePerusahaanNotFound()
    {
        $barang_id = fake()->uuid();
        $perusahaan_id = fake()->uuid();
        $barang = [
            'id' => $barang_id,
            'nama' => fake()->company(),
            'kode' => fake()->countryCode(),
            'perusahaan_id' => $perusahaan_id,
            'harga' => fake()->numberBetween(1000, 100000),
            'stok' => 20
        ];

        $this->apiMock->shouldReceive('connection')->andReturn($this->apiMock);
        $this->apiMock->shouldReceive('get')->with('barang/' . $barang_id)->andReturn($barang);

        $this->apiMock->shouldReceive('get')->with('perusahaan/' . $perusahaan_id)->andThrow(new HttpCustomException(HttpStatusCodes::NOT_FOUND, 'Not Found'));

        $this->expectException(HttpCustomException::class);
        $this->expectExceptionCode(HttpStatusCodes::NOT_FOUND);
        $this->expectExceptionMessage('Barang Not Found');
        $this->barangService->getBarangByIdWithPerusahaan($barang_id);
    }

    public function testCheckoutBarang()
    {
        $user = User::factory()->create(['email' => "johndoe@gmail.com"]);

        $stok = 20;
        $jumlah = 5;
        $barang_id = fake()->uuid();
        $perusahaan_id = fake()->uuid();
        $barang = [
            'id' => $barang_id,
            'nama' => fake()->company(),
            'kode' => fake()->countryCode(),
            'perusahaan_id' => $perusahaan_id,
            'nama_perusahaan' => fake()->company(),
            'kode_perusahaan' => fake()->countryCode(),
            'alamat_perusahaan' => fake()->address(),
            'harga' => fake()->numberBetween(1000, 100000),
            'stok' => $stok
        ];

        $updatedBarang = [
            'id' => $barang_id,
            'nama' => $barang['nama'],
            'kode' => $barang['kode'],
            'perusahaan_id' => $perusahaan_id,
            'nama_perusahaan' => $barang['nama_perusahaan'],
            'kode_perusahaan' => $barang['kode_perusahaan'],
            'alamat_perusahaan' => $barang['alamat_perusahaan'],
            'harga' => $barang['harga'],
            'stok' => $stok - $jumlah
        ];

        $this->apiMock->shouldReceive('connection')->andReturn($this->apiMock);
        $this->apiMock->shouldReceive('patch')->with('barang/' . $barang_id . '/stok/decrease', ['decrease_by' => $jumlah])->andReturn($updatedBarang);

        $barangModel = new Barang($barang);
        $updatedBarangModel = new Barang($updatedBarang);
        $riwayatPembelianMock = RiwayatPembelian::create($user, $updatedBarangModel, $jumlah);
        $this->riwayatPembelianService->expects($this->once())
            ->method('createRiwayatPembelian')
            ->with($user, $barangModel, $jumlah)
            ->willReturn($riwayatPembelianMock);

        $riwayatPembelian = $this->barangService->checkoutBarang($user, $barangModel, $jumlah);
        $this->assertEquals($riwayatPembelian->toArray(), $riwayatPembelianMock->toArray());
    }

    public function testCheckoutBarangWhenJumlahLessThanOne()
    {
        $user = User::factory()->create(['email' => "johndoe@gmail.com"]);

        $stok = 20;
        $jumlah = 0;
        $barang_id = fake()->uuid();
        $perusahaan_id = fake()->uuid();
        $barang = [
            'id' => $barang_id,
            'nama' => fake()->company(),
            'kode' => fake()->countryCode(),
            'perusahaan_id' => $perusahaan_id,
            'nama_perusahaan' => fake()->company(),
            'kode_perusahaan' => fake()->countryCode(),
            'alamat_perusahaan' => fake()->address(),
            'harga' => fake()->numberBetween(1000, 100000),
            'stok' => $stok
        ];

        $barangModel = new Barang($barang);

        $this->expectException(HttpCustomException::class);
        $this->expectExceptionCode(HttpStatusCodes::BAD_REQUEST);
        $this->expectExceptionMessage('Jumlah minimal 1 buah');
        $this->barangService->checkoutBarang($user, $barangModel, $jumlah);
    }

    public function testCheckoutBarangWhenJumlahMoreThanStock()
    {
        $user = User::factory()->create(['email' => "johndoe@gmail.com"]);

        $stok = 20;
        $jumlah = 21;
        $barang_id = fake()->uuid();
        $perusahaan_id = fake()->uuid();
        $barang = [
            'id' => $barang_id,
            'nama' => fake()->company(),
            'kode' => fake()->countryCode(),
            'perusahaan_id' => $perusahaan_id,
            'nama_perusahaan' => fake()->company(),
            'kode_perusahaan' => fake()->countryCode(),
            'alamat_perusahaan' => fake()->address(),
            'harga' => fake()->numberBetween(1000, 100000),
            'stok' => $stok
        ];

        $barangModel = new Barang($barang);

        $this->expectException(HttpCustomException::class);
        $this->expectExceptionCode(HttpStatusCodes::BAD_REQUEST);
        $this->expectExceptionMessage('Jumlah melebihi stok');
        $this->barangService->checkoutBarang($user, $barangModel, $jumlah);
    }

    public function testCheckoutBarangById()
    {
        $user = User::factory()->create(['email' => "johndoe@gmail.com"]);

        $stok = 20;
        $jumlah = 5;
        $barang_id = fake()->uuid();
        $perusahaan_id = fake()->uuid();
        $perusahaan = [
            'id' => $perusahaan_id,
            'nama' => fake()->company(),
            'kode' => fake()->countryCode(),
            'alamat' => fake()->address(),
        ];

        $barang = [
            'id' => $barang_id,
            'nama' => fake()->company(),
            'kode' => fake()->countryCode(),
            'perusahaan_id' => $perusahaan_id,
            'harga' => fake()->numberBetween(1000, 100000),
            'stok' => $stok
        ];

        $updatedBarang = [
            'id' => $barang_id,
            'nama' => $barang['nama'],
            'kode' => $barang['kode'],
            'perusahaan_id' => $perusahaan_id,
            'nama_perusahaan' => $perusahaan['nama'],
            'kode_perusahaan' => $perusahaan['kode'],
            'alamat_perusahaan' => $perusahaan['alamat'],
            'harga' => $barang['harga'],
            'stok' => $stok - $jumlah
        ];


        $this->apiMock->shouldReceive('connection')->andReturn($this->apiMock);
        $this->apiMock->shouldReceive('get')->with('barang/' . $barang_id)->andReturn($barang);
        $this->apiMock->shouldReceive('get')->with('perusahaan/' . $perusahaan_id)->andReturn($perusahaan);

        $this->apiMock->shouldReceive('patch')->with('barang/' . $barang_id . '/stok/decrease', ['decrease_by' => $jumlah])->andReturn($updatedBarang);

        $updatedBarangModel = new Barang($updatedBarang, true);
        $riwayatPembelianMock = RiwayatPembelian::create($user, $updatedBarangModel, $jumlah);
        $this->riwayatPembelianService->expects($this->once())
            ->method('createRiwayatPembelian')
            ->with($user, $updatedBarangModel, $jumlah)
            ->willReturn($riwayatPembelianMock);

        $riwayatPembelian = $this->barangService->checkoutBarangById($user, $barang_id, $jumlah);
        $this->assertEquals($riwayatPembelian->toArray(), $riwayatPembelianMock->toArray());
    }
}