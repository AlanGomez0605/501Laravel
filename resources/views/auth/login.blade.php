<x-guest-layout>
    <div class="mb-4 text-sm">
        <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            {{ __('← Regresar al Inicio') }}
        </a>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Message from registration redirect -->
    @if (session('message'))
        <div class="mb-4 p-3 text-sm text-blue-700 bg-blue-100 border border-blue-300 rounded">
            {{ session('message') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="session('email', old('email'))" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a id="forgot-password-link" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Iniciar Sesión') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-4">
        <div class="flex items-center justify-center">
            <span class="w-full border-t"></span>
            <span class="px-4 text-gray-500">{{ __('O continuar con') }}</span>
            <span class="w-full border-t"></span>
        </div>

        <div class="flex flex-col space-y-4 mt-4">
            <!-- Resaltar el botón de Google si hay un error relacionado -->
            @php
                $hasGoogleError = $errors->has('email') && str_contains($errors->first('email'), 'Google');
            @endphp
            
            <a href="{{ route('google.login') }}" 
               class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 {{ $hasGoogleError ? 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 animate-pulse' : 'bg-red-600 hover:bg-red-700 focus:ring-red-500' }}">
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                    <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                {{ $hasGoogleError ? __('¡Inicia Sesión con Google!') : __('Continuar con Google') }}
            </a>
        </div>
        
        @if($hasGoogleError)
        <div class="mt-2 text-center">
            <p class="text-xs text-blue-600 font-medium">👆 Usa este botón para acceder a tu cuenta de Google</p>
        </div>
        @endif
    </div>

    <script>
        // Mejorar la experiencia cuando hay errores relacionados con Google
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const forgotPasswordLink = document.getElementById('forgot-password-link');
            
            // Detectar si hay un error relacionado con Google
            @if($hasGoogleError)
                // Ocultar el link de olvidaste tu contraseña para cuentas de Google
                if (forgotPasswordLink) {
                    forgotPasswordLink.style.display = 'none';
                }
                
                // Enfocar en el botón de Google
                const googleButton = document.querySelector('a[href*="google"]');
                if (googleButton) {
                    setTimeout(() => {
                        googleButton.focus();
                    }, 500);
                }
            @endif
            
            // Verificar dinámicamente mientras el usuario escribe
            if (emailInput && forgotPasswordLink) {
                emailInput.addEventListener('input', async function() {
                    const email = this.value;
                    if (email.includes('@') && email.length > 5) {
                        // Aquí podrías hacer una verificación AJAX para determinar el tipo de cuenta
                        // Por ahora, mostrar siempre la opción
                        forgotPasswordLink.style.display = 'inline';
                    }
                });
            }
        });
    </script>
</x-guest-layout>
