<?php

namespace App\Services;

use App\Enums\HttpStatusCodes;
use App\Exceptions\HttpCustomException;
use App\Interfaces\BarangServiceInterface;
use App\Interfaces\RiwayatPembelianServiceInterface;
use App\Libraries\FilterBuilderInterface;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BarangService implements BarangServiceInterface
{
    protected $riwayatPembelianService;
    public function __construct(RiwayatPembelianServiceInterface $riwayatPembelianService)
    {
        $this->riwayatPembelianService = $riwayatPembelianService;
    }

    protected function filter(FilterBuilderInterface $data, ?int $perPage = null, ?string $search = null)
    {
        if ($search) {
            $data = $data->filter(['search' => $search]);
        }

        if ($perPage != null) {
            $data = $data->paginate($perPage);
        } else {
            $data = $data->get();
        }

        return $data;
    }
    public function getManyBarang(?int $perPage = null, ?string $search = null): LengthAwarePaginator|Collection
    {
        $data = Barang::all();

        return $this->filter($data, $perPage, $search);
    }

    public function getManyBarangWithPerusahaan(?int $perPage = null, ?string $search = null): LengthAwarePaginator|Collection
    {
        $data = Barang::with('perusahaan', 'perusahaan_id', '/barang');
        return $this->filter($data, $perPage, $search);
    }

    public function getBarangByIdWithPerusahaan(string|int $id): Barang
    {
        $barang = Barang::findWith($id, 'perusahaan', 'perusahaan_id');

        if (!$barang) {
            throw new HttpCustomException(HttpStatusCodes::NOT_FOUND, 'Barang Not Found', '/barang');
        }

        return $barang;
    }

    public function getBarangById(string|int $id): Barang
    {
        $barang = Barang::find($id);

        if (!$barang) {
            throw new HttpCustomException(HttpStatusCodes::NOT_FOUND, 'Barang Not Found');
        }

        return $barang;
    }

    protected function decreaseStokBarang(Barang $barang, int $jumlah): Barang
    {
        if ($jumlah > $barang->stok) {
            throw new HttpCustomException(HttpStatusCodes::BAD_REQUEST, 'Jumlah melebihi stok');
        }
        return $barang->decrease('stok', $jumlah);
    }

    protected function decreaseStokBarangById(string|int $barang_id, int $jumlah): Barang
    {
        $barang = $this->getBarangById($barang_id);
        return $this->decreaseStokBarang($barang, $jumlah);
    }

    public function checkoutBarang(User $user, Barang $barang, int $jumlah)
    {
        if ($jumlah < 1) {
            throw new HttpCustomException(HttpStatusCodes::BAD_REQUEST, 'Jumlah minimal 1 buah');
        }
        $this->decreaseStokBarang($barang, $jumlah);
        return $this->riwayatPembelianService->createRiwayatPembelian($user, $barang, $jumlah);
    }

    public function checkoutBarangById(User $user, string|int $barang_id, int $jumlah)
    {
        $barang = $this->getBarangByIdWithPerusahaan($barang_id);
        return $this->checkoutBarang($user, $barang, $jumlah);
    }
}