@extends('seo.document')

@section('body')
    <nav class="navbar bg-body-tertiary mb-4">
        <div class="container-fluid">
            <a href="/barang" class="navbar-brand">meShop</a>
            <ul class="navbar-nav ml-auto" style="display: flex; flex-direction: row; align-items: center; gap: 2rem;">
                <li class="nav-item"><a class="nav-link" href="/barang">Barang</a></li>
                <li class="nav-item"><a class="nav-link" href="/riwayat-pembelian">Riwayat</a></li>
                <li class="nav-item">
                    <a href="/auth/logout" class="btn btn-sm btn-dark">Log out</a>
                </li>
            </ul>
        </div>
    </nav>

    <div id="main-container" class="container py-8">
        @if (Session::has('success'))
            <div class="alert alert-success animate-slideInOut fixed-top mx-auto right-0 left-0 mt-4"
                style="max-width: 500px;">
                <span>{{ Session::get('success') }}</span>
            </div>
        @endif
        @if (Session::has('error'))
            <div class="alert alert-danger animate-slideInOut fixed-top mx-auto right-0 left-0 max-w-lg mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-x-circle-fill mr-2" width="1em" height="1em"
                    viewBox="0 0 16 16" fill="currentColor">
                    <path
                        d="M8 0a8 8 0 100 16A8 8 0 008 0zm4.354 4.354a.5.5 0 10-.708-.708L8 7.293l-3.646-3.647a.5.5 0 10-.708.708L7.293 8l-3.647 3.646a.5.5 0 10.708.708L8 8.707l3.646 3.647a.5.5 0 10.708-.708L8.707 8l3.647-3.646z" />
                </svg>
                <span>{{ Session::get('error') }}</span>
            </div>
        @endif
        @if (Session::has('user'))
            <h4 class="text-base md:text-xl">Hi, {{ Session::get('user') }}</h4>
        @endif
        <div style="padding: 2rem 0;">
            @yield('content')

        </div>
        <x-cart-button />
    </div>
@endsection
