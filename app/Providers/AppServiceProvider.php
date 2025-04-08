<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bindings de servicios personalizados (si los necesitas)
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ======================
        // POLÍTICAS DE CONTRASEÑA
        // ======================
        Password::defaults(function () {
            return Password::min(12)                   // 12 caracteres mínimo
                ->letters()                            // Requiere letras
                ->mixedCase()                          // Mayúsculas + minúsculas
                ->numbers()                            // Requiere números
                ->symbols()                            // Símbolos obligatorios
                ->uncompromised(3);                    // Revisa en 3 brechas conocidas
        });

        // ======================
        // RATE LIMITER PARA LOGIN (BLOQUEO PROGRESIVO)
        // ======================
        RateLimiter::for('login', function (Request $request) {
    $key = 'login_attempts:' . Str::transliterate(Str::lower($request->input('email'))) . '|' . $request->ip();
    
    $blockCount = Cache::get($key . ':block_count', 0);
    $baseMinutes = 5;
    
    return Limit::perMinutes($baseMinutes * ($blockCount + 1), 5)
        ->by($key)
        ->response(function () use ($key, $baseMinutes) {
            $newBlockCount = Cache::increment($key . ':block_count');
            Cache::put($key . ':block_count', $newBlockCount, now()->addHours(24));
            
            $waitMinutes = min($baseMinutes * pow(2, $newBlockCount - 1), 120);
            
            return back()
                ->withErrors([
                    'email' => "Demasiados intentos. Cuenta bloqueada por {$waitMinutes} minutos."
                ])
                ->withInput($request->only('email', 'remember'));
        });
});
    }
}