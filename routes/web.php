<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\RiwayatPembelianController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [BarangController::class, 'viewManyBarang']);

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::get('/login', 'viewLogin')->name('login');
    Route::post('/login', 'login')->name('post-login');

    Route::get('/register', 'viewRegister')->name('register');
    Route::post('/register', 'register')->name('post-register');

    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(BarangController::class)->prefix('barang')->middleware('auth')->group(function () {
    Route::get('/', 'viewManyBarang')->name('barang');
    Route::get('/{id}', 'viewOneBarang');
    Route::middleware('protect.checkout')->get('/{id}/checkout', 'viewCheckoutBarang')->name('checkout-barang');
    Route::post('/{id}/checkout', 'checkoutBarang')->name('post-checkout-barang');
});

Route::controller(RiwayatPembelianController::class)->prefix('riwayat-pembelian')->middleware('auth')->group(function () {
    Route::get('/', 'viewRiwayatPembelian')->name('riwayat-pembelian');
});

Route::controller(KeranjangController::class)->prefix('keranjang')->middleware('auth')->group(function () {
    Route::get('/', 'viewKeranjang')->name('keranjang');

    Route::post('/', 'createKeranjang')->name('create-keranjang');
    Route::post('/{id}/checkout', 'checkoutKeranjang')->name('checkout-keranjang');

    Route::patch('/{id}/jumlah/decrease', 'decreaseJumlahBarang')->name('decrease-jumlah-keranjang');
    Route::patch('/{id}/jumlah/increase', 'increaseJumlahBarang')->name('increase-jumlah-keranjang');

    Route::delete('/{id}', 'deleteKeranjang')->name('delete-keranjang');
});