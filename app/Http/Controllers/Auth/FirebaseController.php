<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FirebaseController extends Controller
{
    public function handleCallback(Request $request)
    {
        try {
            Log::info('Recibida solicitud de autenticación Firebase', [
                'request' => $request->all()
            ]);

            // Validar los datos recibidos
            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
                'user.name' => 'required|string',
                'user.email' => 'required|email',
                'user.uid' => 'required|string',
                'user.avatar' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                Log::error('Validación fallida', ['errors' => $validator->errors()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de usuario inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userData = $request->user;
            $token = $request->token;

            // Buscar usuario existente o crear uno nuevo
            $user = User::where('email', $userData['email'])->first();

            if (!$user) {
                Log::info('Creando nuevo usuario', ['email' => $userData['email']]);
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => bcrypt(uniqid()),
                    'social_id' => $userData['uid'],
                    'social_type' => 'facebook',
                    'social_avatar' => $userData['avatar'] ?? null
                ]);
            } else {
                Log::info('Usuario existente encontrado', ['email' => $userData['email']]);
                // Actualizar información del usuario si es necesario
                $user->update([
                    'social_id' => $userData['uid'],
                    'social_type' => 'facebook',
                    'social_avatar' => $userData['avatar'] ?? $user->social_avatar
                ]);
            }

            Auth::login($user);
            Log::info('Usuario autenticado exitosamente', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Autenticación exitosa',
                'redirect' => '/dashboard'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en autenticación Firebase', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la autenticación: ' . $e->getMessage()
            ], 500);
        }
    }
}