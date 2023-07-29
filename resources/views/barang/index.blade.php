@extends('layouts.layout')

@section('title', 'Katalog Barang')

@section('content')
    <div class="container py-8">
        <div class="row mb-4" style="display: flex; justify-content: center;">
            <h1 class="text-center text-3xl font-semibold mb-8">Katalog Barang</h1>

            <form action="/barang" class="d-flex justify-content-center my-4" style="max-width: 48rem;">
                <input name="search" type="text" placeholder="Search..."
                    class="form-control rounded-start w-full max-w-lg" />
                <button type="submit" class="btn btn-primary rounded-end">Search</button>
            </form>
        </div>

        <div class="row justify-content-center">
            <div id="card-container" class="row justify-content-center" style="gap: 2rem;">
                @if (count($data) === 0)
                    <h3 class="text-center">Katalog Barang Kosong</h3>
                @endif

                @foreach ($data as $barang)
                    <div id="{{ $barang->id }}" class="card bg-base-100 shadow-md mb-4" style="width: 20rem;">
                        <div class="card-body">
                            <h2 class="card-title">{{ $barang->nama }}</h2>
                            <p>Harga: {{ $barang->harga }}</p>
                            <p>Stok: {{ $barang->stok }}</p>
                            <p>Perusahaan: {{ $barang->nama_perusahaan }}</p>
                            <div class="card-actions d-flex justify-content-end">
                                <a href="/barang/{{ $barang->id }}" class="btn btn-primary">Detail</a>
                            </div>
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

        <script>
            function polling() {
                fetchBarang();
            }

            function fetchBarang() {
                const searchParams = new URLSearchParams(window.location.search);
                var xhr = new XMLHttpRequest();
                xhr.open('GET', `/api/barang?${searchParams.toString()}`);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var data = JSON.parse(xhr.responseText);
                        if (data.status === 'success') {
                            const newData = data.data;
                            updateCardContainer(newData);
                        }
                    } else {
                        console.error('Error while fetching data:', xhr.status);
                    }
                    setTimeout(fetchBarang, 5000);
                };
                xhr.onerror = function() {
                    console.error('Error while fetching data:', xhr.status);
                    setTimeout(fetchBarang, 5000);
                };
                xhr.send();
            }

            function updateCardContainer(newData) {
                const container = document.getElementById('card-container');
                if (container) {
                    container.innerHTML = '';
                    newData.forEach(item => {
                        const card = createCard(item);
                        container.appendChild(card);
                    });
                }
            }

            function createCard(item) {
                const card = document.createElement('div');
                card.id = item.id;
                card.className = 'card bg-base-100 shadow-md no-animation mb-4';
                card.style.width = '20rem';
                card.innerHTML = `
        <div class="card-body">
            <h2 class="card-title">${item.nama}</h2>
            <p>Harga: ${item.harga}</p>
            <p>Stok: ${item.stok}</p>
            <p>Perusahaan: ${item.nama_perusahaan}</p>
            <div class="card-actions d-flex justify-content-end">
                <a href="/barang/${item.id}" class="btn btn-primary">Detail</a>
            </div>
        </div>
    `;
                return card;
            }

            document.addEventListener('DOMContentLoaded', function() {
                polling();
            });
        </script>
    @endsection
