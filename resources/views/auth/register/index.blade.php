@extends('layouts.auth-layout')

@section('title', 'Register')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6">
                <div class="card p-4 rounded">
                    <h1 class="text-center mb-4 display-4 text-primary">Register</h1>
                    @if (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible fade show mt-0 mb-3" role="alert">
                            <span class="align-middle">{{ Session::get('error') }}</span>
                        </div>
                    @endif
                    <form action="{{ route('post-register') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nama_depan">Nama Depan</label>
                            <input type="text" id="nama_depan" name="nama_depan"
                                class="form-control @error('nama_depan') is-invalid @enderror" required
                                value="{{ old('nama_depan') }}">
                            @error('nama_depan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mt-4">
                            <label for="nama_belakang">Nama Belakang</label>
                            <input type="text" id="nama_belakang" name="nama_belakang"
                                class="form-control @error('nama_belakang') is-invalid @enderror" required
                                value="{{ old('nama_belakang') }}">
                            @error('nama_belakang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mt-4">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username"
                                class="form-control @error('username') is-invalid @enderror" required
                                value="{{ old('username') }}">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mt-4">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email"
                                class="form-control @error('email') is-invalid @enderror" required
                                value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mt-4">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                            <div class="mt-4 text-center">
                                <span>Sudah memiliki akun? </span>
                                <a href="/auth/login" class="btn btn-link">
                                    Masuk
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
