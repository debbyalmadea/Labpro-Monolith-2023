<?php

namespace App\Services;

use App\Enums\HttpStatusCodes;
use App\Events\UpdateKeranjangNotification;
use App\Exceptions\HttpCustomException;
use App\Interfaces\BarangServiceInterface;
use App\Interfaces\KeranjangServiceInterface;
use App\Models\Keranjang;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class KeranjangService implements KeranjangServiceInterface
{
    protected $barangService;
    public function __construct(
        BarangServiceInterface $barangService,
    ) {
        $this->barangService = $barangService;
    }

    public function getKeranjang(User $user, ?int $perPage = null): LengthAwarePaginator|Collection
    {
        $keranjangList = $this->getKeranjangWithBarang()->filter(['user_id' => $user->id]);

        if ($perPage) {
            $keranjangList = $keranjangList->paginate(15);
        } else {
            $keranjangList = $keranjangList->get();
        }

        return $keranjangList;
    }

    protected function getKeranjangWithBarang()
    {
        $keranjangList = Keranjang::all();
        $barangList = $this->barangService->getManyBarang();

        $keranjangList = $keranjangList->map(function ($keranjang) use ($barangList) {
            $barang = $barangList[$keranjang->{'barang_id'}];

            if ($barang) {
                foreach ($barang->toArray() as $key => $value) {
                    if ($key != 'id') {
                        $keranjang->{$key . '_barang'} = $value;
                    }
                }
            }

            return $keranjang;
        });


        return Keranjang::builder($keranjangList);
    }

    public function getTotalCount(User $user): int
    {
        if (!$user) {
            throw new HttpCustomException(HttpStatusCodes::UNAUTHORIZED, 'Unauthorized', '/auth/login');
        }

        $keranjangList = Keranjang::query()->where('user_id', $user->id)->get();

        $totalCount = $keranjangList->reduce(function ($carry, $keranjang) {
            return $carry + $keranjang->jumlah_barang;
        }, 0);

        return $totalCount;
    }

    public function createKeranjang(User $user, int $jumlah, string|int $barang_id): Keranjang
    {
        $barang = $this->barangService->getBarangByIdWithPerusahaan($barang_id);

        if ($jumlah > $barang->stok) {
            throw new HttpCustomException(HttpStatusCodes::BAD_REQUEST, 'Jumlah melebihi stok');
        }

        $keranjang = Keranjang::create([
            'user_id' => $user->id,
            'barang_id' => $barang_id,
            'jumlah_barang' => $jumlah
        ]);

        $this->notifyUpdate($user, $this->getTotalCount($user));
        return $keranjang;
    }

    public function deleteKeranjang(User $user, Keranjang $keranjang): Keranjang
    {
        $keranjang->delete();
        $this->notifyUpdate($user, $this->getTotalCount($user));
        return $keranjang;
    }

    public function deleteKeranjangById(User $user, string|int $keranjang_id): Keranjang
    {
        $keranjang = Keranjang::find($keranjang_id);
        if (!$keranjang) {
            throw new HttpCustomException(HttpStatusCodes::NOT_FOUND, 'Keranjang not found');
        }

        return $this->deleteKeranjang($user, $keranjang);
    }

    public function checkoutKeranjang(User $user, Keranjang $keranjang): Keranjang
    {
        $this->barangService->checkoutBarangById($user, $keranjang->barang_id, $keranjang->jumlah_barang);
        return $this->deleteKeranjang($user, $keranjang);
    }

    public function checkoutKeranjangById(User $user, string|int $keranjang_id): Keranjang
    {
        $keranjang = Keranjang::find($keranjang_id);
        if (!$keranjang) {
            throw new HttpCustomException(HttpStatusCodes::NOT_FOUND, 'Keranjang not found');
        }

        return $this->checkoutKeranjang($user, $keranjang);
    }

    public function decreaseJumlahBarang(User $user, string|int $keranjang_id): Keranjang
    {
        $keranjang = $this->changeJumlahBarang($user, $keranjang_id, -1);

        return $keranjang;
    }

    public function increaseJumlahBarang(User $user, string|int $keranjang_id): Keranjang
    {
        $keranjang = $this->changeJumlahBarang($user, $keranjang_id, 1);

        return $keranjang;
    }

    protected function changeJumlahBarang($user, $keranjang_id, $amount)
    {
        $keranjang = Keranjang::find($keranjang_id);
        if (!$keranjang) {
            throw new HttpCustomException(HttpStatusCodes::NOT_FOUND, 'Keranjang not found');
        }

        $barang = $this->barangService->getBarangById($keranjang->barang_id);
        if (!$barang) {
            throw new HttpCustomException(HttpStatusCodes::NOT_FOUND, 'Barang not found');
        }

        $keranjang->jumlah_barang = $keranjang->jumlah_barang + $amount;

        if ($keranjang->jumlah_barang > $barang->stok) {
            throw new HttpCustomException(HttpStatusCodes::BAD_REQUEST, 'Jumlah barang melebihi stok');
        }

        if ($keranjang->jumlah_barang < 0) {
            throw new HttpCustomException(HttpStatusCodes::BAD_REQUEST, 'Jumlah barang kurang dari 0');
        }

        if ($keranjang->jumlah_barang === 0) {
            $this->deleteKeranjang($user, $keranjang);
        } else {
            $keranjang->save();
        }

        $this->notifyUpdate($user, $this->getTotalCount($user));
        return $keranjang;
    }

    protected function notifyUpdate($user, $totalCount)
    {
        event(
            new UpdateKeranjangNotification(
                [
                    "user_id" => $user->id,
                    "count" => $totalCount
                ]
            )
        );
    }
}