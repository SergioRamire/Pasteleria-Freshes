<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Envía una nueva notificación para verificar el correo electrónico.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Si el usuario ya verificó su correo, redirigir al dashboard u otra ruta principal
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // Enviar el enlace de verificación nuevamente
        $request->user()->sendEmailVerificationNotification();

        // Redirigir de regreso con un estado de éxito (puede usarse en la vista para mostrar un mensaje)
        return back()->with('status', 'verification-link-sent');
    }
}
