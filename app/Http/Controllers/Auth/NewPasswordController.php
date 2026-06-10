<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NewPasswordController extends Controller
{
    /**
     * Muestra la vista para restablecer la contraseña (formulario con token).
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Procesa la solicitud de nueva contraseña con token y correo.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validar los datos básicos del formulario
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Buscar al usuario por su email
        $user = \App\Models\User::where('email', $request->email)->first();

        // Validar que la nueva contraseña no sea igual a la anterior
        if ($user && Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('email'))
                        ->withErrors(['password' => 'La nueva contraseña no puede ser igual a la anterior.']);
        }

        // Restablecer la contraseña
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                Auth::login($user);
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('dashboard')->with('success', '🔐 ¡Contraseña restablecida correctamente! Has iniciado sesión. Buen día.')
            : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
    }
}
