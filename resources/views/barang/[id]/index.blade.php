@extends('layouts.layout')

@section('title', 'Detail Barang')

@section('content')
    <div class="container px-4 py-8">
        <div class="bg-light rounded-md p-4 text-dark">
            <h2 class="card-title mb-4">Detail {{ $barang->nama }}</h2>
            <div class="row">
                <div class="col-md-6">
                    <p>ID: <span class="font-weight-bold">{{ $barang->id }}</span></p>
                    <p>Nama: <span class="font-weight-bold">{{ $barang->nama }}</span></p>
                    <p>Harga: <span class="font-weight-bold">{{ $barang->harga }}</span></p>
                </div>
                <div class="col-md-6">
                    <p>Stok: <span class="font-weight-bold">{{ $barang->stok }}</span></p>
                    <p>Kode: <span class="font-weight-bold">{{ $barang->kode }}</span></p>
                    <p>Perusahaan: <span class="font-weight-bold">{{ $barang->nama_perusahaan }}</span></p>
                </div>
            </div>
        </div>
        <hr class="my-4">
        <form class="w-full max-w-sm" action="{{ route('checkout-barang', ['id' => $barang->id]) }}" method="GET">
            @csrf
            @if ($barang->stok > 0)
                <div class="form-group">
                    <label for="jumlah" class="label-text">Jumlah</label>
                    <input type="number" id="jumlah" name="jumlah"
                        class="form-control @error('jumlah') is-invalid @enderror" min="{{ 1 }}"
                        max="{{ $barang->stok }}" required>
                    @error('jumlah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <input type="text" id="barang_id" name="barang_id" value="{{ $barang->id }}" hidden>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" name="action" value="checkout">Beli Barang</button>
                    <button type="submit" class="btn btn-secondary" name="action" value="keranjang">Keranjang</button>
                </div>
            @else
                <div class="form-group">
                    <label for="jumlah" class="label-text">Jumlah</label>
                    <input type="number" id="jumlah" name="jumlah"
                        class="form-control @error('jumlah') is-invalid @enderror" min="{{ 1 }}"
                        max="{{ $barang->stok }}" required disabled>
                    @error('jumlah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <input type="text" id="barang_id" name="barang_id" value="{{ $barang->id }}" hidden>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" name="action" value="checkout" disabled>Beli
                        Barang</button>
                    <button type="submit" class="btn btn-secondary" name="action" value="keranjang"
                        disabled>Keranjang</button>
                </div>
            @endif
        </form>
    </div>
@endsection
