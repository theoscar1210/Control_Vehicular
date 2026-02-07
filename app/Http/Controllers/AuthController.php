<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Máximo de intentos de login antes de bloquear
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Segundos de bloqueo tras superar el máximo de intentos
     */
    private const DECAY_SECONDS = 300;

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'usuario' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        // Verificar rate limiting (usuario + IP)
        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'usuario' => trans('auth.throttle', ['seconds' => $seconds]),
            ])->withInput($request->only('usuario'));
        }

        $loginField = filter_var($request->usuario, FILTER_VALIDATE_EMAIL) ? 'email' : 'usuario';

        // Verificar si el usuario existe y está activo antes de intentar login
        $usuario = \App\Models\Usuario::where($loginField, $request->usuario)->first();

        if ($usuario && !$usuario->activo) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);

            return back()->withErrors([
                'usuario' => 'Tu cuenta está desactivada. Contacta al administrador.',
            ])->withInput($request->only('usuario'));
        }

        if (Auth::attempt([$loginField => $request->usuario, 'password' => $request->password], $request->filled('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Redirigir según el rol del usuario
            $user = Auth::user();
            switch ($user->rol) {
                case 'PORTERIA':
                    return redirect()->route('porteria.index');
                case 'SST':
                case 'ADMIN':
                default:
                    return redirect()->intended(route('dashboard'));
            }
        }

        RateLimiter::hit($throttleKey, self::DECAY_SECONDS);

        $remaining = RateLimiter::remaining($throttleKey, self::MAX_ATTEMPTS);

        $mensaje = 'Credenciales inválidas.';
        if ($remaining <= 2 && $remaining > 0) {
            $mensaje .= " Le quedan {$remaining} intento(s) antes del bloqueo temporal.";
        }

        return back()->withErrors([
            'usuario' => $mensaje,
        ])->withInput($request->only('usuario'));
    }

    /**
     * Genera la clave de throttling combinando usuario + IP
     */
    private function throttleKey(Request $request): string
    {
        return Str::transliterate(
            Str::lower($request->string('usuario')) . '|' . $request->ip()
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function dashboard()
    {
        return view('dashboard');
    }
}
