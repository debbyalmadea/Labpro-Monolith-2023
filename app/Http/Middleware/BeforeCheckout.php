<?php

namespace App\Http\Middleware;

use App\Helpers\JWTAuthHelper;
use App\Http\Controllers\KeranjangController;
use App\Interfaces\RiwayatPembelianServiceInterface;
use App\Models\Barang;
use App\Services\BarangService;
use App\Services\KeranjangService;
use App\Services\RiwayatPembelianService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BeforeCheckout
{
    protected $riwayatPembelianService;
    function __construct(RiwayatPembelianServiceInterface $riwayatPembelianService)
    {
        $this->riwayatPembelianService = $riwayatPembelianService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validateData = $request->validate([
            'jumlah' => 'required|min:1|integer',
        ]);

        $jumlah = (int) $validateData['jumlah'];

        $id = $request->route('id');

        $barang = Barang::find($id);

        if (!$barang) {
            return back()->with('error', 'Barang tidak ditemukan');
        }

        if ($request->input('action') === 'keranjang') {
            $barangService = new BarangService($this->riwayatPembelianService);
            return (
                new KeranjangController(
                    new KeranjangService(
                    $barangService,
                    ),
                    new RiwayatPembelianService(),
                    new JWTAuthHelper()
                )
            )->createKeranjang($request);
        }

        $request->session()->put('jumlah', $jumlah);
        return $next($request);
    }
}