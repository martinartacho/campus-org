<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        Log::info('[AuthController] Intento de login', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'email' => $request->email
        ]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'Debe ser un email válido',
            'password.required' => 'La contraseña es obligatoria',
        ]);

        if ($validator->fails()) {
            Log::warning('[AuthController] Validación fallida', [
                'errors' => $validator->errors(),
                'input' => $request->all()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            Log::notice('[AuthController] Credenciales inválidas', [
                'email' => $request->email,
                'ip' => $request->ip()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $user = Auth::user();
        Log::info('[AuthController] Login exitoso', [
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => substr($token, -10) // Log parcial del token por seguridad
        ]);

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => $user->only('id', 'name', 'email')
        ]);
    }

    public function logout()
    {
        Log::info('Dentro de logout');

        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    /* public function me()
    {
        Log::info('[AuthController] Obteniendo información de usuario', [
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'token' => (string) $token,
            'created_at' => $user->created_at->toDateTimeString(),
        ]);
    } */

    public function me()
    {
        $user = Auth::user();
        $token = JWTAuth::getToken();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'token' => (string) $token,
            'created_at' => $user->created_at->toDateTimeString(),
        ]);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

    public function register(Request $request)
    {
        Log::info('[AuthController] Nuevo registro', [
            'ip' => $request->ip(),
            'name' => $request->name,
            'email' => $request->email
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'Las contraseñas no coinciden'
        ]);

        if ($validator->fails()) {
            Log::warning('[AuthController] Error en registro', [
                'errors' => $validator->errors(),
                'input' => $request->all()
            ]);
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = JWTAuth::fromUser($user);

            Log::info('[AuthController] Registro exitoso', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Usuario registrado correctamente',
                'token' => $token,
                'token_type' => 'bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('[AuthController] Error al crear usuario', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        Log::info('Dentro de upadatProfile');

        $user = $request->user();

        Log::info('user: '.$user);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json(['message' => 'Perfil actualizado con éxito'], 200);
    }

    public function changePassword(Request $request)
    {
        Log::info('Dentro de changePassword');

        $user = $request->user();
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Contraseña actual incorrecta'], 400);
        }

        $user->update([
            'password' => bcrypt($request->new_password),
        ]);

        return response()->json(['message' => 'Contraseña actualizada'], 200);
    }

    public function deleteAccount(Request $request)
    {
        Log::info('Dentro de deleteAccount');

        $user = $request->user();
        $user->delete();
        return response()->json(['message' => 'Cuenta eliminada'], 200);
    }
}
