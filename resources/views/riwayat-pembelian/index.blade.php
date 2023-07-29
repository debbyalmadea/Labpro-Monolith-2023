@extends('layouts.layout')
@section('title', 'Riwayat Pembelian')
@section('content')
    <div class="max-w-screen py-8">
        <div class="lg:px-24 mb-4 flex flex-col justify-center items-center">
            <h1 class="text-center text-3xl font-semibold mb-8">Riwayat Pembelian</h1>
        </div>

        <div class="row justify-content-center">
            <div id="card-container" class="row justify-content-center" style="gap: 2rem;">
                @if (count($data) === 0)
                    <h3>Riwayat Pembelian Kosong</h3>
                @endif

                @foreach ($data as $riwayatPembelian)
                    <div id={{ $riwayatPembelian->updated_at }} class="card w-full md:w-96 bg-base-100 shadow-md"
                        style="width: 20rem;">
                        <div class="card-body">
                            <p class="text-xs">
                                {{ \Carbon\Carbon::parse($riwayatPembelian->created_at)->setTimezone('Asia/Jakarta')->formatLocalized('%e %b %Y, %H:%M:%S') }}
                            </p>
                            <h2 class="card-title">{{ $riwayatPembelian->nama_barang }}</h2>
                            <p>Jumlah: {{ $riwayatPembelian->jumlah_barang }}</p>
                            <p>Total harga: {{ $riwayatPembelian->harga_barang * $riwayatPembelian->jumlah_barang }}</p>
                        </div>
                    </div>
                @endforeach

            </div>

            <div class="row justify-content-center"">
                <div class="col-lg-10">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
