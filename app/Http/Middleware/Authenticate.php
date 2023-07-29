<?php

namespace App\Http\Middleware;

use App\Helpers\JWTAuthHelper;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected $jwtAuthHelper;
    function __construct(JWTAuthHelper $jwtAuthHelper)
    {
        $this->jwtAuthHelper = $jwtAuthHelper;
    }
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        if (!$request->expectsJson()) {
            return route('login');
        }

        return null;
    }

    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $user = $this->jwtAuthHelper->getUser();

            if (!$user) {
                return redirect('/auth/login')->with('Unauthorized');
            }

            session(['user' => $user->nama_depan . ' ' . $user->nama_belakang]);
            return $next($request);
        } catch (\Throwable $th) {
            return redirect('/auth/login')->with('error', $th->getMessage());
        }
    }
}