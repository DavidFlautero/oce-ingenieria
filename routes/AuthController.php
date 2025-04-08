<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\SecurityLog;

class AuthController extends Controller
{
    // Método para manejar el login con todas las protecciones
    public function login(Request $request)
    {
        // 1. Rate Limiting (protección contra fuerza bruta)
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Demasiados intentos. Por favor espere $seconds segundos."
            ]);
        }

        // 2. Validación reforzada
        $credentials = $request->validate([
            'email' => 'required|email:strict,dns,spoof',
            'password' => [
                'required',
                'string',
                'min:12',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/'
            ],
        ]);

        // 3. Intento de autenticación
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Registrar acceso exitoso
            SecurityLog::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'event' => 'login_success'
            ]);
            
            RateLimiter::clear($throttleKey);
            return redirect()->intended('dashboard');
        }

        // 4. Manejo de fallos
        RateLimiter::hit($throttleKey);
        
        // Registrar intento fallido
        SecurityLog::create([
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'event' => 'login_failed'
        ]);

        // Invalidar sesión existente si hay fallo
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->withErrors([
            'email' => 'Credenciales incorrectas o cuenta bloqueada temporalmente.'
        ]);
    }

    // Método para cerrar sesión con protección completa
    public function logout(Request $request)
    {
        // Registrar cierre de sesión
        if (Auth::check()) {
            SecurityLog::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'event' => 'logout'
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache'
        ]);
    }
}