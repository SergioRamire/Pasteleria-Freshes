<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    /**
     * Muestra la vista para confirmar la contraseña.
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Verifica que la contraseña ingresada sea correcta.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validar que la contraseña ingresada sea la del usuario actual
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            // Si es incorrecta, lanzar error
            throw ValidationException::withMessages([
                'password' => 'La contraseña ingresada no es correcta.',
            ]);
        }

        // Guardar confirmación en la sesión
        $request->session()->put('auth.password_confirmed_at', time());

        // Redirigir a la página deseada
        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
