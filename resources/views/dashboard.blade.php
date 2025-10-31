<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Message from login -->
            @if (session('message'))
                <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 border border-green-300 rounded">
                    {{ session('message') }}
                </div>
            @endif
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">¡Bienvenido, {{ Auth::user()->name }}!</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-gray-700">Información de la cuenta</h4>
                            <p class="text-sm text-gray-600 mt-2">Email: {{ Auth::user()->email }}</p>
                            
                            @if (Auth::user()->social_type)
                                <p class="text-sm text-gray-600">Conectado con: 
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                        {{ ucfirst(Auth::user()->social_type) }}
                                    </span>
                                </p>
                            @endif
                        </div>
                        
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-gray-700">Acciones rápidas</h4>
                            <div class="mt-2 space-y-2">
                                <a href="{{ route('profile.edit') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                    → Editar perfil
                                </a>
                                <br>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                        → Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
