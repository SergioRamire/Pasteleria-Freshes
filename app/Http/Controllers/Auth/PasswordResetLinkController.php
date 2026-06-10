<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    /**
     * Muestra la vista del formulario para solicitar el restablecimiento de contraseña.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Procesa la solicitud del enlace para restablecer la contraseña.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Validar que el campo de correo electrónico no esté vacío y tenga formato válido
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Buscar usuario por correo electrónico
        $user = User::where('email', $request->email)->first();

        // Si el usuario no existe, mostrar un mensaje de error
        if (!$user) {
            return back()->withErrors([
                'email' => 'No se encontró ningún usuario con ese correo electrónico.',
            ])->withInput($request->only('email'));
        }

        // Verificar si el usuario está activo (ajusta el nombre del campo si es diferente)
        if (!$user->estado) {
            return back()->withErrors([
                'email' => 'El usuario no está activo. No puedes restablecer la contraseña.',
            ])->withInput($request->only('email'));
        }

        // Enviar el enlace de restablecimiento de contraseña
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Verificar si el enlace fue enviado correctamente
        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', '📧 Correo de recuperación enviado correctamente. Revisa tu bandeja de entrada o la carpeta de spam.')
            : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
    }
}
