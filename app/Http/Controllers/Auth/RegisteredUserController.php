<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Muestra la vista del formulario de registro.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Procesa una solicitud de registro de usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validar los datos del formulario
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:' . User::class,  // Verifica que el username no esté repetido
                'alpha_dash:ascii',       // Permite letras, números, guiones y guiones bajos
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:' . User::class,  // Verifica que el correo no esté registrado
            ],
            'password' => [
                'required',
                'confirmed',               // Verifica que coincida con password_confirmation
                Rules\Password::defaults() // Mínimo 8 caracteres, combinación segura
            ],
        ]);

        // Crear nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Encriptar la contraseña
        ]);

        // Disparar evento de usuario registrado (puede enviar correo, etc.)
        event(new Registered($user));

        // Autenticar al nuevo usuario
        Auth::login($user);

        // Redirigir a la página principal o dashboard
        return redirect(RouteServiceProvider::HOME);
    }
}
