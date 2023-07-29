@extends('layouts.layout')

@section('title', 'Keranjang')

@section('content')
    <div class="container px-4 py-8">
        <div class="lg:px-24 mb-4 text-center">
            <h1 class="text-3xl font-semibold mb-8">Keranjang</h1>
        </div>

        <div class="row justify-content-center">
            <div id="card-container" class="row justify-content-center" style="gap: 2rem;">

                @if (count($data) === 0)
                    <h3 class="text-center">Keranjang Kosong</h3>
                @endif

                @foreach ($data as $keranjang)
                    <div id="{{ $keranjang->updated_at }}" class="card bg-base-100 shadow-md mb-4" style="width: 20rem;">
                        <div class="card-body">
                            <p class="text-xs">
                                {{ \Carbon\Carbon::parse($keranjang->created_at)->setTimezone('Asia/Jakarta')->formatLocalized('%e %b %Y, %H:%M:%S') }}
                            </p>
                            <h2 class="card-title">{{ $keranjang->nama_barang }}</h2>
                            <div class="d-flex justify-content-between align-items-center">
                                <p>Jumlah: {{ $keranjang->jumlah_barang }}</p>
                                <div class="d-flex" style="gap: 1rem;">
                                    <form action="{{ route('decrease-jumlah-keranjang', ['id' => $keranjang->id]) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-light">-</button>
                                    </form>
                                    <form action="{{ route('increase-jumlah-keranjang', ['id' => $keranjang->id]) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-light">+</button>
                                    </form>
                                </div>
                            </div>
                            <p>Total harga: {{ $keranjang->harga_barang * $keranjang->jumlah_barang }}</p>
                            <div class="d-flex flex-column flex-sm-row w-100 mt-4" style="gap: 1rem;">
                                <form method="POST" action="{{ route('checkout-keranjang', ['id' => $keranjang->id]) }}"
                                    class="w-100">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">Checkout</button>
                                </form>
                                <form method="POST" action="{{ route('delete-keranjang', ['id' => $keranjang->id]) }}"
                                    class="w-100 mt-2 mt-sm-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $data->links() }}
            </div>
        </div>
    @endsection
