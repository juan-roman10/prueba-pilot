<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Registro de nuevo usuario.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'rol'      => 'nullable|in:admin,editor',
            'estado'   => 'nullable|in:activo,inactivo',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'rol'      => $request->input('rol', 'editor'),       // 'editor' por defecto
            'estado'   => $request->input('estado', 'activo'),    // 'activo' por defecto
        ]);

        $token = Auth::guard('api')->login($user);

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    /**
     * Iniciar sesión y obtener token JWT.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['estado'] = 'activo';

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales inválidas o usuario inactivo'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Obtener el usuario autenticado.
     */
    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    /**
     * Cerrar sesión (invalidar el token).
     */
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    /**
     * Refrescar token expirado.
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }

    /**
     * Formato de respuesta con el token.
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => Auth::guard('api')->factory()->getTTL() * 60,
        ]);
    }
}
