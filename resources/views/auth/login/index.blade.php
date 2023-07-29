@extends('layouts.auth-layout')

@section('title', 'Login')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6">
                <div class="card p-4 rounded">
                    <h1 class="text-center mb-4 display-4 text-primary">Login</h1>
                    @if (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible fade show mt-0 mb-3" role="alert">
                            <span class="align-middle">{{ Session::get('error') }}</span>
                        </div>
                    @endif
                    <form action="{{ route('post-login') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email"
                                class="form-control @error('email') is-invalid @enderror" required>
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
                            <button type="submit" class="btn btn-primary btn-block">Log in</button>
                            <div class="mt-4 text-center">
                                <span>Belum memiliki akun? </span>
                                <a href="/auth/register" class="btn btn-link">
                                    Daftar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
