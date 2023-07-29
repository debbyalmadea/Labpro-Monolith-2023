<?php

namespace App\Interfaces;

use App\Models\Keranjang;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface KeranjangServiceInterface
{
    public function getKeranjang(User $user, ?int $perPage = null): LengthAwarePaginator|Collection;
    public function getTotalCount(User $user): int;
    public function createKeranjang(User $user, int $jumlah, string|int $barang_id): Keranjang;
    public function deleteKeranjang(User $user, Keranjang $keranjang): Keranjang;
    public function deleteKeranjangById(User $user, string|int $keranjang_id): Keranjang;
    public function decreaseJumlahBarang(User $user, string|int $keranjang_id): Keranjang;
    public function increaseJumlahBarang(User $user, string|int $keranjang_id): Keranjang;
    public function checkoutKeranjang(User $user, Keranjang $keranjang): Keranjang;
    public function checkoutKeranjangById(User $user, string|int $keranjang_id): Keranjang;
}