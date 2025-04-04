<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use App\Models\User;


class PasswordResetController extends Controller
{
    public function sendResetCode(Request $request)
    {
        // 1️⃣ Verificar el formato y la existencia del buzón
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        // 2️⃣ Generar código aleatorio de 5 dígitos
        $code = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);

        // 3️⃣ Almacenado en caché (5 minutos)
        Cache::put('password_reset_' . $request->email, $code, now()->addMinutes(5));

        // 4️⃣ enviar el corrro
        Mail::to($request->email)->send(new PasswordResetMail($code));


        return response()->json(['message' => 'Código enviado al correo.'], 200);
    }

    public function verifyCode(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string'
        ]);

        // 2️⃣ Obtener código en la caché
        $cachedCode = Cache::get('password_reset_' . $request->email);

        // 3️⃣ Verificar código
        if (!$cachedCode || $cachedCode !== $request->code) {
            return response()->json(['message' => 'Código incorrecto o expirado'], 400);
        }

        return response()->json(['message' => 'Código verificado correctamente'], 200);
    }

    public function resetPassword(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        // 2️⃣Obtener código en la caché
        $cachedCode = Cache::get('password_reset_' . $request->email);

        // 3️⃣ Verificar código
        if (!$cachedCode || $cachedCode !== $request->code) {
            return response()->json(['message' => 'Código incorrecto o expirado'], 400);
        }

        // 4️⃣ Actualizar contraseña
        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        // 5️⃣ Borrar codigo en la cache
        Cache::forget('password_reset_' . $request->email);

        return response()->json(['message' => 'Contraseña restablecida exitosamente'], 200);
    }
}
