<?php

namespace App\Providers;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Minutos sin actividad tras los cuales un token de la API deja de ser valido.
     */
    private const MINUTOS_INACTIVIDAD = 5;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));
        CarbonImmutable::setLocale(config('app.locale'));

        // Sanctum actualiza last_used_at en cada request autenticado: si pasaron
        // mas de MINUTOS_INACTIVIDAD desde el ultimo uso, el token se rechaza (401).
        Sanctum::authenticateAccessTokensUsing(
            function (PersonalAccessToken $token, bool $isValid): bool {
                $ultimoUso = $token->last_used_at ?? $token->created_at;

                return $isValid && $ultimoUso->gt(now()->subMinutes(self::MINUTOS_INACTIVIDAD));
            }
        );
    }
}
