<?php

namespace App\Http\Controllers;

use App\Helpers\JWTAuthHelper;
use App\Interfaces\RiwayatPembelianServiceInterface;
use Illuminate\Http\Request;

class RiwayatPembelianController extends Controller
{
    protected $riwayatPembelianService;
    private $jwtAuthHelper;
    function __construct(
        RiwayatPembelianServiceInterface $riwayatPembelianService,
        JWTAuthHelper $jwtAuthHelper
    ) {
        $this->riwayatPembelianService = $riwayatPembelianService;
        $this->jwtAuthHelper = $jwtAuthHelper;
    }
    public function viewRiwayatPembelian(Request $request)
    {
        $user = $this->jwtAuthHelper->getUser();

        return view('riwayat-pembelian.index', [
            'data' => $this->riwayatPembelianService->getRiwayatPembelian($user, 12)
        ]);
    }
}