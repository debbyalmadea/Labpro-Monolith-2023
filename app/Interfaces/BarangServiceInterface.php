<?php

namespace App\Interfaces;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BarangServiceInterface
{
    public function getManyBarang(?int $perPage = null, ?string $search = null): LengthAwarePaginator|Collection;
    public function getManyBarangWithPerusahaan(?int $perPage = null, ?string $search = null): LengthAwarePaginator|Collection;
    public function getBarangByIdWithPerusahaan(string|int $id): Barang;
    public function getBarangById(string|int $id): Barang;
    public function checkoutBarang(User $user, Barang $barang, int $jumlah);
    public function checkoutBarangById(User $user, string|int $barang_id, int $jumlah);

}