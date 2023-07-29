@extends('layouts.layout')

@section('title', 'Pembelian Barang')

@section('content')
    <div class="container px-4 py-8">
        <h2 class="text-3xl font-semibold mb-8">Pembelian Barang: {{ $barang->nama }}</h2>
        <form action="{{ route('checkout-barang', ['id' => $barang->id]) }}" method="POST" style="max-width: 20rem;">
            @csrf
            <div class="form-group mb-4">
                <label for="jumlah" class="label-text">Jumlah</label>
                <input type="number" id="jumlah" name="jumlah" value="{{ Session::get('jumlah') }}" readonly
                    class="form-control @error('jumlah') is-invalid @enderror" required>
                @error('jumlah')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <p>Harga satuan: {{ $barang->harga }}</p>
            <p>Total harga: {{ $barang->harga * Session::get('jumlah') }}</p>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Beli Barang</button>
            </div>
        </form>
    </div>
@endsection
