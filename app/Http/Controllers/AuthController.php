<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'usuario' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);


        // Aquí asumimos que en users table hay columna 'usuario' o usamos email.
        $loginField = filter_var($request->usuario, FILTER_VALIDATE_EMAIL) ? 'email' : 'usuario';

        if (Auth::attempt([$loginField => $request->usuario, 'password' => $request->password], $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'usuario' => 'Credenciales inválidas.',
        ])->withInput($request->only('usuario'));
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
        // $user = Auth::user(); -> para usar en la vista
        return view('dashboard');
    }
}
