<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Generar token de acceso API
     *
     * POST /api/auth/login
     * Body: { "email": "usuario@example.com", "password": "contraseña", "device_name": "app-movil" }
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'required|string|max:255',
        ]);

        $user = Usuario::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        if (!$user->activo) {
            throw ValidationException::withMessages([
                'email' => ['Esta cuenta está desactivada.'],
            ]);
        }

        // Crear token con abilities basadas en el rol
        $abilities = $this->getAbilitiesForRole($user->rol);
        $token = $user->createToken($request->device_name, $abilities);

        return response()->json([
            'message' => 'Autenticación exitosa',
            'user' => [
                'id' => $user->id_usuario,
                'nombre' => $user->nombre_completo,
                'email' => $user->email,
                'rol' => $user->rol,
            ],
            'token' => $token->plainTextToken,
            'abilities' => $abilities,
        ]);
    }

    /**
     * Revocar token actual
     *
     * POST /api/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente',
        ]);
    }

    /**
     * Revocar todos los tokens del usuario
     *
     * POST /api/auth/logout-all
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Todas las sesiones han sido cerradas',
        ]);
    }

    /**
     * Obtener información del usuario autenticado
     *
     * GET /api/auth/user
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id_usuario,
            'nombre' => $user->nombre_completo,
            'email' => $user->email,
            'rol' => $user->rol,
            'activo' => $user->activo,
        ]);
    }

    /**
     * Definir abilities (permisos) según el rol del usuario
     */
    private function getAbilitiesForRole(string $rol): array
    {
        return match ($rol) {
            'ADMIN' => ['*'], // Acceso total
            'SST' => [
                'vehiculos:read', 'vehiculos:write',
                'conductores:read', 'conductores:write',
                'documentos:read', 'documentos:write',
                'alertas:read', 'alertas:write',
                'propietarios:read', 'propietarios:write',
            ],
            'PORTERIA' => [
                'vehiculos:read',
                'conductores:read',
                'documentos:read',
                'alertas:read',
            ],
            default => ['read'], // Solo lectura básica
        };
    }
}
