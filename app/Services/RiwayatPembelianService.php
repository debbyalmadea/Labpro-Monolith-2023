<?php

namespace App\Services;

use App\Interfaces\RiwayatPembelianServiceInterface;
use App\Models\Barang;
use App\Models\RiwayatPembelian;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class RiwayatPembelianService implements RiwayatPembelianServiceInterface
{
    public function getRiwayatPembelian(User $user, ?int $perPage = null): LengthAwarePaginator|Collection
    {
        $riwayatPembelian = RiwayatPembelian::filter($user->id);

        if ($perPage) {
            $riwayatPembelian = $riwayatPembelian->paginate($perPage);
        } else {
            $riwayatPembelian = $riwayatPembelian->get();
        }

        return $riwayatPembelian;
    }

    public function createRiwayatPembelian(User $user, Barang $barang, int $jumlah): RiwayatPembelian
    {
        $riwayat_pembelian = RiwayatPembelian::create($user, $barang, $jumlah);

        return $riwayat_pembelian;
    }
}