<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        \Log::info('Redirigiendo a Google OAuth...');
        return Socialite::driver('google')->redirect();
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $user = Socialite::driver('facebook')->user();
            
            $findUser = User::where('social_id', $user->id)
                           ->where('social_type', 'facebook')
                           ->first();
            
            if ($findUser) {
                Auth::login($findUser);
                return redirect('/dashboard');
            }
            
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'social_id' => $user->id,
                'social_type' => 'facebook',
                'social_avatar' => $user->avatar,
                'password' => bcrypt('my-facebook')
            ]);
            
            Auth::login($newUser);
            return redirect('/dashboard');
            
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Error al iniciar sesión con Facebook');
        }
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            \Log::info('Callback de Google recibido para: ' . $googleUser->email);
            
            // Buscar usuario existente por email (cualquier tipo)
            $existingUser = User::where('email', $googleUser->email)->first();
            
            if ($existingUser) {
                // Usuario existe, verificar si ya tiene datos de Google
                if ($existingUser->social_id && $existingUser->social_type === 'google') {
                    // Ya está configurado con Google, asegurar que esté verificado
                    if (!$existingUser->email_verified_at) {
                        $existingUser->update(['email_verified_at' => now()]);
                    }
                    
                    Auth::login($existingUser);
                    \Log::info('Usuario logueado con Google existente: ' . $existingUser->email);
                    return redirect('/dashboard')->with('message', 'Bienvenido de vuelta!');
                } else {
                    // Usuario existe pero no tiene Google configurado, vincular cuenta
                    $existingUser->update([
                        'social_id' => $googleUser->id,
                        'social_type' => 'google',
                        'social_avatar' => $googleUser->avatar,
                        'email_verified_at' => $existingUser->email_verified_at ?: now(), // Verificar si no está verificado
                    ]);
                    
                    Auth::login($existingUser);
                    \Log::info('Cuenta vinculada con Google: ' . $existingUser->email);
                    return redirect('/dashboard')->with('message', 'Tu cuenta se ha vinculado exitosamente con Google!');
                }
            } else {
                // Usuario no existe, crear nuevo
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'social_id' => $googleUser->id,
                    'social_type' => 'google',
                    'social_avatar' => $googleUser->avatar,
                    'password' => bcrypt('google-' . str()->random(10)), // Password aleatorio
                    'email_verified_at' => now(), // Auto-verificado por Google
                ]);
                
                Auth::login($newUser);
                \Log::info('Nuevo usuario creado con Google: ' . $newUser->email);
                return redirect('/dashboard')->with('message', 'Cuenta creada exitosamente con Google!');
            }
            
        } catch (\Exception $e) {
            \Log::error('Error en Google callback: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // También mostrar el error en desarrollo
            if (app()->environment('local')) {
                return redirect('/login')->with('error', 'Error de desarrollo: ' . $e->getMessage());
            }
            
            return redirect('/login')->with('error', 'Hubo un problema al iniciar sesión con Google. Por favor, intenta de nuevo.');
        }
    }
}
