<?php

namespace App\Interfaces;

use App\Models\Barang;
use App\Models\RiwayatPembelian;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface RiwayatPembelianServiceInterface
{
    public function getRiwayatPembelian(User $user, ?int $perPage = null): LengthAwarePaginator|Collection;
    public function createRiwayatPembelian(User $user, Barang $barang, int $jumlah): RiwayatPembelian;
}