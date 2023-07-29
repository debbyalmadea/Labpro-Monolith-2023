<?php

namespace App\Http\Controllers;

use App\Helpers\JWTAuthHelper;
use App\Interfaces\KeranjangServiceInterface;
use App\Interfaces\RiwayatPembelianServiceInterface;
use Illuminate\Http\Request;

class KeranjangController extends Controller
{
    protected $keranjangService;
    private $riwayatPemebelianService;
    private $jwtAuthHelper;
    function __construct(
        KeranjangServiceInterface $keranjangService, RiwayatPembelianServiceInterface $riwayatPemebelianService,
        JWTAuthHelper $jwtAuthHelper
    ) {
        $this->keranjangService = $keranjangService;
        $this->riwayatPemebelianService = $riwayatPemebelianService;
        $this->jwtAuthHelper = $jwtAuthHelper;
    }
    function viewKeranjang(Request $request)
    {
        $user = $this->jwtAuthHelper->getUser();
        return view(
            'keranjang.index',
            [
                "data" => $this->keranjangService->getKeranjang($user, 15)
            ]
        );
    }

    function createKeranjang(Request $request)
    {
        $request->validate([
            'jumlah' => 'required|min:1|integer'
        ]);

        $user = $this->jwtAuthHelper->getUser();
        $jumlah = $request->input('jumlah');
        $barang_id = $request->input('barang_id');

        $this->keranjangService->createKeranjang($user, $jumlah, $barang_id);
        return redirect('/keranjang')->with('success', 'Berhasil menambahkan barang ke keranjang');
    }

    function checkoutKeranjang(Request $request, $id)
    {
        $user = $this->jwtAuthHelper->getUser();
        $this->keranjangService->checkoutKeranjangById($user, $id);

        return redirect('/riwayat-pembelian')->with('success', 'Berhasil membeli barang');
    }

    function deleteKeranjang(Request $request, $id)
    {
        $this->keranjangService->deleteKeranjangById($this->jwtAuthHelper->getUser(), $id);
        return back()->with('success', 'Berhasil menghapus keranjang');
    }

    function decreaseJumlahBarang(Request $reques, $id)
    {
        $user = $this->jwtAuthHelper->getUser();
        $this->keranjangService->decreaseJumlahBarang($user, $id);

        return back();
    }

    function increaseJumlahBarang(Request $reques, $id)
    {
        $user = $this->jwtAuthHelper->getUser();
        $this->keranjangService->increaseJumlahBarang($user, $id);

        return back();
    }
}