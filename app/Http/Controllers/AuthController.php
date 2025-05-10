<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TripCategory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $field = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'username' => 'required|unique:users',
            'phone' => 'string|nullable',
            'url_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:4096'
        ]);

        $image = null;
        if ($request->hasFile('url_photo')) {
            $image = $request->file('url_photo');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            // Crear la carpeta de trips images 
            $directory = public_path('uploads/users');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0775, true);
            }

            // Guandar en la carpeta
            $image->move($directory, $filename);
            $urlPhoto = '/uploads/users/' . $filename;
        }

        $user = User::create([
            'name' => $field['name'],
            'email' => $field['email'],
            'password' => Hash::make($field['password']),
            'username' => $field['username'],
            'phone' => $field['phone'] ?? null,
            'url_photo' => $urlPhoto,
        ]);
        //   $token = $user->createToken($request->name);

        return response()->json(
            [
                'data' => [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    //  "token" => $token->plainTextToken,
                    'url_photo' => url($urlPhoto) ?? null,
                ],

            ]
        );
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users',
                'password' => 'required'
            ]);
            $user = User::where('email', $request->email)->first();

            // If the user does not exist or password is wrong
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Email o password are incorrect',
                ], 404);
            }

            $token = $user->createToken($user->name);
            $photoUrl = $user->url_photo ? asset($user->url_photo) : "";
            return response()->json(
                [
                    'data' => [
                        "id" => $user->id,
                        "name" => $user->name,
                        "email" => $user->email,
                        "token" => $token->plainTextToken,
                        'url_photo' => $photoUrl,
                    ],
                    'message' => "login con éxito",
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al iniciar cuenta',
                'error' => $e->getMessage(),
                'user' => $request->user()->id
            ], 500); // Internal server error
        }
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
