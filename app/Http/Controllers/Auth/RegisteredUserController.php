<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerificationToken;
use App\Mail\EmailVerification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register-email');
    }

    /**
     * Check if email exists and show profile form
     */
    public function checkEmail(Request $request): RedirectResponse|View
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Por favor, introduce un correo electrónico válido.',
        ]);

        $existingUser = User::where('email', $request->email)->first();
        
        if ($existingUser) {
            if ($existingUser->social_type === 'google') {
                // Mostrar vista de redirección a Google
                return view('auth.redirect-google');
            } else {
                // Redirigir al login normal
                return redirect()->route('login')
                    ->with('email', $request->email)
                    ->with('message', 'Este correo ya está registrado. Por favor, inicia sesión con tu contraseña.');
            }
        }

        try {
            $token = mt_rand(100000, 999999);
            
            EmailVerificationToken::create([
                'email' => $request->email,
                'token' => $token,
                'expires_at' => now()->addMinutes(5)
            ]);

            Mail::to($request->email)->send(new EmailVerification($token));

            return redirect()->route('register.verify-code', ['email' => $request->email]);
        } catch (\Exception $e) {
            \Log::error('Error de envío de correo: ' . $e->getMessage());
            return back()->withInput()
                ->withErrors(['email' => 'No se pudo enviar el código de verificación. Por favor, intenta de nuevo.']);
        }
    }

    /**
     * Show the verification code form
     */
    public function showVerifyForm(Request $request): View
    {
        $email = $request->query('email');
        
        if (!$email) {
            abort(404);
        }

        return view('auth.verify-code', ['email' => $email]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function verifyEmail(Request $request): RedirectResponse|View
    {
        $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string', 'size:6'],
        ], [
            'token.required' => 'El código de verificación es obligatorio.',
            'token.size' => 'El código debe tener 6 dígitos.',
        ]);

        // Buscar el token sin restricción de tiempo primero para dar mensaje específico
        $tokenRecord = EmailVerificationToken::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$tokenRecord) {
            return back()->withErrors(['token' => 'El código de verificación es incorrecto.']);
        }

        // Verificar si está expirado
        if ($tokenRecord->isExpired()) {
            $tokenRecord->delete(); // Limpiar el token expirado
            return back()->withErrors(['token' => 'El código de verificación ha expirado. Por favor, solicita uno nuevo.']);
        }

        // Eliminar el token usado
        $tokenRecord->delete();

        \Log::info("Email verificado correctamente para: {$request->email}");

        return view('auth.register-profile', ['email' => $request->email]);
    }

    public function resendVerification(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            // Verificar que el email existe en la tabla de tokens (que se haya iniciado el proceso)
            $existingTokenCount = EmailVerificationToken::where('email', $request->email)->count();
            
            if ($existingTokenCount === 0) {
                return back()->withErrors(['email' => 'No se encontró una solicitud de verificación para este correo.']);
            }

            // Eliminar todos los tokens anteriores para este email
            EmailVerificationToken::where('email', $request->email)->delete();

            // Generar nuevo token único
            $token = mt_rand(100000, 999999);
            
            // Crear el nuevo token con nueva fecha de expiración
            EmailVerificationToken::create([
                'email' => $request->email,
                'token' => $token,
                'expires_at' => now()->addMinutes(5)
            ]);

            // Enviar el nuevo código por correo
            Mail::to($request->email)->send(new EmailVerification($token));

            \Log::info("Código de verificación reenviado para: {$request->email}, nuevo token: {$token}");

            return back()->with('status', 'Se ha enviado un nuevo código de verificación a tu correo. El código anterior ya no es válido.');
        } catch (\Exception $e) {
            \Log::error('Error al reenviar código de verificación: ' . $e->getMessage());
            return back()->withErrors(['email' => 'No se pudo enviar el código de verificación. Por favor, intenta de nuevo.']);
        }
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
