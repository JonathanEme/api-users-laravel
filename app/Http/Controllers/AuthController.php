<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    // Registro
    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($v->fails()) return response()->json($v->errors(), 422);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);
        return response()->json(['user' => $user->only(['id','name','email']), 'token' => $token], 201);
    }

    // Login
    public function login(Request $request)
    {
        $credentials = $request->only(['email','password']);
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        return response()->json(['token' => $token]);
    }

    // Usuario autenticado
    public function miusuario()
    {
        return response()->json(auth()->user()->only(['id','name','email','created_at','updated_at']));
    }

    // Cerrar sesión: invalidar el token actual
    public function logout()
    {
        try {
            JWTAuth::parseToken()->invalidate();
            return response()->json(null, 204);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not invalidate token'], 500);
        }
    }

    // Refrescar token
    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh();
            return response()->json(['token' => $newToken]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not refresh token'], 500);
        }
    }
}
