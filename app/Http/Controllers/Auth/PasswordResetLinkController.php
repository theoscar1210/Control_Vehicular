<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Mostrar la vista de solicitud del enlace para restablecer la contraseña.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * 
     *Gestionar una solicitud entrante de enlace para restablecer la contraseña.
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Enviaremos el enlace para restablecer la contraseña a este usuario. Una vez que hayamos intentado
        // enviar el enlace, examinaremos la respuesta y veremos el mensaje que
        // debemos mostrar al usuario. Por último, enviaremos una respuesta adecuada.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
