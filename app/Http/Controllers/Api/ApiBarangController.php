<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\JsonResponse;
use App\Interfaces\BarangServiceInterface;
use Illuminate\Http\Request;

class ApiBarangController extends Controller
{
    protected $barangService;
    function __construct(
        BarangServiceInterface $barangService,
    ) {
        $this->barangService = $barangService;
    }
    public function getManyBarang(Request $request)
    {
        $data = $this->barangService->getManyBarangWithPerusahaan(15, request('search'))->collect()->values()->toArray();
        return JsonResponse::success($data, 'Successfully retrieved data');
    }

    public function getOneBarang($id)
    {
        $barang = $this->barangService->getBarangByIdWithPerusahaan($id);
        return JsonResponse::success($barang, 'Successfully retrieved data');
    }
}