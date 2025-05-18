<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class LoginController extends Controller
{
   /**
 * Iniciar sesión desde la app móvil
 *
 * Este endpoint permite a un ciudadano iniciar sesión y obtener un token de acceso.
 *
 * @group Autenticación
 * @unauthenticated
 *
 * @bodyParam email string requerido Email del usuario. Ejemplo: alumnouoc@email.com
 * @bodyParam password string requerido Contraseña del usuario. Ejemplo: password123
 *
 * @response 200 {
 *   "token": "eyJ0eXAiOiJKV1QiLCJh...",
 *   "user": {
 *     "id": 1,
 *     "first_name": "Alumno",
 *     "last_name": "Universitat",
 *     "email": "alumnouoc@email.com"
 *   }
 * }
 *
 * @response 401 {
 *   "message": "Credenciales no válidas"
 * }
 */

public function login(Request $request)
{
    Log::info('🔵 LoginController llamado', [
        'headers' => $request->headers->all(),
        'data' => $request->all(),
    ]);

    $credentials = $request->only('email', 'password');

    if (!Auth::attempt($credentials)) {
        return response()->json(['error' => 'Credenciales inválidas'], 401);
    }

    $user = Auth::user();

    if ($request->filled('expo_token')) {
        $user->expo_token = $request->expo_token;
        $user->save();
    }

    $token = $user->createToken('mobile')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name
        ]
    ]);
}


}
