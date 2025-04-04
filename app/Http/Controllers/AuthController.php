<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $field = $request->validate([
            // name -> como nickname
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'username' => 'required|unique:users',
            'phone' => 'string',
            'url_photo' =>'string'
        ]);
        $user = User::create($field);
        $token = $user->createToken($request->name);
        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();

        // If the user does not exist or password is wrong
        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                'message' => 'Email o password are incorrect'
            ];
        }

        $token = $user->createToken($user->name);
        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function logout(Request $request)
    {
        // Si instala Sanctum, user() puede devolver el usuario actual y esta linea para eliminar todos los tokens del usuario
        // But if I login with different device and I logout in one of them. Delete all tokens maybe is wrong.
        // Because it means I must login in others devices one more time.
        // Maybe for the seguridad, is a good option. Jejeje
        $request->user()->tokens()->delete();
        return [
            'message' => 'You are logged out'
        ];
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $exists = DB::table('users')->whereRaw('BINARY email = ?', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function checkUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|string'
        ]);
        // Realizamos una consulta a la base de datos para verificar si el nombre de usuario ya existe.
        // Usamos "BINARY" para que la comparación sea sensible a mayúsculas y minúsculas.

        $exists = DB::table('users')
            ->whereRaw('BINARY name = ?', [$request->username])
            ->exists();
        return response()->json(['exists' => $exists]);
    }

    public function checkNickname(Request $request)
    {
        $request->validate([
            'nickname' => 'required|string'
        ]);

        
        $exists = DB::table('users')
            ->whereRaw('BINARY nickname = ?', [$request->nickname])
            ->exists();
        return response()->json(['exists' => $exists]);

    }
}
