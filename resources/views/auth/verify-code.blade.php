<x-guest-layout>
    <div class="mb-4 text-sm">
        <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            {{ __('← Regresar al Inicio') }}
        </a>
    </div>

    <div class="mb-4 text-sm text-gray-600">
        {{ __('Por favor, introduce el código de verificación que hemos enviado a:') }}
        <strong class="text-gray-800">{{ $email }}</strong>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 p-3 text-sm text-green-700 bg-green-100 border border-green-300 rounded">
            {{ session('status') }}
        </div>
    @endif

    <!-- Error messages -->
    @if ($errors->has('email'))
        <div class="mb-4 p-3 text-sm text-red-700 bg-red-100 border border-red-300 rounded">
            {{ $errors->first('email') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register.verify-email') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">

        <!-- Verification Code -->
        <div>
            <x-input-label for="token" :value="__('Código de verificación')" />
            
            <x-text-input id="token"
                         class="block mt-1 w-full"
                         type="text"
                         name="token"
                         required
                         autofocus
                         autocomplete="off" />

            <x-input-error :messages="$errors->get('token')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Verificar') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Formulario separado para reenviar código -->
    <div class="mt-4 text-center">
        <form method="POST" action="{{ route('register.resend-verification') }}" class="inline" id="resendForm">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button type="submit" id="resendBtn" class="text-sm text-gray-600 hover:text-gray-900 underline disabled:opacity-50 disabled:cursor-not-allowed">
                {{ __('Reenviar código') }}
            </button>
        </form>
        
        <div id="countdown" class="mt-2 text-xs text-gray-500" style="display: none;">
            Podrás reenviar otro código en <span id="timer">60</span> segundos
        </div>
    </div>

    <script>
        let resendTimeout = null;
        let countdownInterval = null;
        
        document.getElementById('resendForm').addEventListener('submit', function() {
            const btn = document.getElementById('resendBtn');
            const countdown = document.getElementById('countdown');
            const timer = document.getElementById('timer');
            
            // Deshabilitar botón temporalmente
            btn.disabled = true;
            btn.textContent = 'Enviando...';
            
            // Después del envío, iniciar countdown
            setTimeout(() => {
                btn.textContent = 'Reenviar código';
                countdown.style.display = 'block';
                
                let timeLeft = 60;
                timer.textContent = timeLeft;
                
                countdownInterval = setInterval(() => {
                    timeLeft--;
                    timer.textContent = timeLeft;
                    
                    if (timeLeft <= 0) {
                        clearInterval(countdownInterval);
                        btn.disabled = false;
                        countdown.style.display = 'none';
                    }
                }, 1000);
            }, 2000);
        });
    </script>
</x-guest-layout>