<?php

namespace App\Providers;

use App\Interfaces\AuthServiceInterface;
use App\Interfaces\BarangServiceInterface;
use App\Interfaces\KeranjangServiceInterface;
use App\Interfaces\LoginServiceInterface;
use App\Interfaces\LogoutServiceInterface;
use App\Interfaces\RegisterServiceInterface;
use App\Interfaces\RiwayatPembelianServiceInterface;
use App\Interfaces\SelfProfileServiceInterface;

use App\Interfaces\UserServiceInterface;
use App\Services\AuthService;
use App\Services\BarangService;
use App\Services\KeranjangService;
use App\Services\LoginService;
use App\Services\LogoutService;
use App\Services\RegisterService;
use App\Services\RiwayatPembelianService;
use App\Services\SelfProfileService;
use App\Services\UserService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LoginServiceInterface::class, LoginService::class);
        $this->app->bind(RegisterServiceInterface::class, RegisterService::class);
        $this->app->bind(SelfProfileServiceInterface::class, SelfProfileService::class);
        $this->app->bind(LogoutServiceInterface::class, LogoutService::class);

        $this->app->bind(UserServiceInterface::class, UserService::class);

        $this->app->bind(BarangServiceInterface::class, BarangService::class);
        $this->app->bind(KeranjangServiceInterface::class, KeranjangService::class);
        $this->app->bind(RiwayatPembelianServiceInterface::class, RiwayatPembelianService::class);

        $this->app->bind(LoginServiceInterface::class, LoginService::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Paginator::useBootstrap();
    }
}