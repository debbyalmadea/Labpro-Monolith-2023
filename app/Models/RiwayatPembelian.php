<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPembelian extends Model
{
    use HasFactory;
    protected $table = 'riwayat_pembelian';

    public static function create(User $user, Barang $barang, $jumlah)
    {
        $riwayatPembelian = new static();

        $riwayatPembelian->user_id = $user->id;
        $riwayatPembelian->barang_id = $barang->id;
        $riwayatPembelian->nama_barang = $barang->nama;
        $riwayatPembelian->kode_barang = $barang->kode;
        $riwayatPembelian->perusahaan_id = $barang->perusahaan_id;
        $riwayatPembelian->nama_perusahaan = $barang->nama_perusahaan;
        $riwayatPembelian->harga_barang = $barang->harga;
        $riwayatPembelian->jumlah_barang = $jumlah;

        $riwayatPembelian->save();
        return $riwayatPembelian;
    }

    public function scopeFilter($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }
}