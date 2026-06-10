<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Actualiza la contraseña del usuario autenticado.
     */
    public function update(Request $request): RedirectResponse
    {
        // Validar campos: contraseña actual y nueva contraseña confirmada
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Actualizar la contraseña
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Redirigir con mensaje de éxito
        return Redirect::route('profile')->with('success', '✅ Contraseña actualizada correctamente.');
    }
}
