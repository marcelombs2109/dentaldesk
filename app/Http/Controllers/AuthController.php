<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * SDD-01: autenticación por correo y contraseña.
 */
class AuthController extends Controller
{
    /** Intentos fallidos permitidos antes del bloqueo temporal (SDD-01 R5). */
    private const MAX_INTENTOS = 5;

    /** Segundos de bloqueo tras agotar los intentos. */
    private const SEGUNDOS_BLOQUEO = 60;

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'Ingrese un correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $llave = Str::transliterate(Str::lower($credenciales['email'])) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($llave, self::MAX_INTENTOS)) {
            $segundos = RateLimiter::availableIn($llave);

            throw ValidationException::withMessages([
                'email' => "Demasiados intentos fallidos. Intente nuevamente en {$segundos} segundos.",
            ]);
        }

        if (! Auth::attempt($credenciales, $request->boolean('remember'))) {
            RateLimiter::hit($llave, self::SEGUNDOS_BLOQUEO);

            // Mensaje genérico: no revela si falló el correo o la contraseña.
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        RateLimiter::clear($llave);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
