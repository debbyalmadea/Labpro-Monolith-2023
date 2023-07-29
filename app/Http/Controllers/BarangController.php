<?php

namespace App\Http\Controllers;

use App\Helpers\JWTAuthHelper;
use App\Http\Requests\CheckoutBarangRequest;
use App\Interfaces\BarangServiceInterface;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    protected $barangService;
    protected $riwayatPembelianService;
    protected $jwtAuthHelper;
    function __construct(
        BarangServiceInterface $barangService,
        JWTAuthHelper $jwtAuthHelper
    ) {
        $this->barangService = $barangService;
        $this->jwtAuthHelper = $jwtAuthHelper;
    }
    function viewManyBarang(Request $request)
    {
        return view('barang.index', [
            'data' => $this->barangService->getManyBarangWithPerusahaan(15, request('search'))
        ]);
    }

    function viewOneBarang($id)
    {
        return view('barang.[id].index', [
            'barang' => $this->barangService->getBarangByIdWithPerusahaan($id)
        ]);
    }

    function viewCheckoutBarang(Request $request, $id)
    {
        if (url()->previous()) {
            return view('barang.[id].checkout.index', [
                'barang' => $this->barangService->getBarangById($id)
            ]);
        }

        return view('barang.index', [
            'data' => $this->barangService->getManyBarangWithPerusahaan(15, request('search'))
        ]);
    }

    function checkoutBarang(CheckoutBarangRequest $request, $id)
    {
        $user = $this->jwtAuthHelper->getUser();
        $jumlah = $request->input('jumlah');

        $this->barangService->checkoutBarangById($user, $id, $jumlah);

        return redirect('/riwayat-pembelian')->with('success', 'Berhasil membeli barang');
    }
}