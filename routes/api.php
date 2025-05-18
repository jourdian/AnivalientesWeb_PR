<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\MobileReportController;
use App\Http\Controllers\Api\MobileAdministrationController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ExpoTokenController;
use App\Http\Controllers\Admin\ReportNotificationController;

/*
|--------------------------------------------------------------------------
| Rutas de la API (aplicación móvil AniValientes)
|--------------------------------------------------------------------------
|
| Este archivo define las rutas disponibles para la app móvil.
| Algunas rutas son públicas (como el login) y otras están protegidas
| mediante autenticación Sanctum.
|
*/

/**
 * Ruta pública para iniciar sesión desde la app móvil.
 * Devuelve un token de acceso si las credenciales son válidas.
 * Método: POST
 * URL: /api/login
 */
Route::post('/login', [LoginController::class, 'login']);
Route::get('/ping', function () {
    return response()->json(['ok' => true]);
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas por Sanctum (requieren usuario autenticado)
|--------------------------------------------------------------------------
|
| Estas rutas solo están disponibles si el usuario ha iniciado sesión
| correctamente y posee un token válido.
|
*/
Route::middleware('auth:sanctum')->group(function () {

    /**
     * Devuelve las denuncias del usuario autenticado.
     * Método: GET
     * URL: /api/reports
     */
    Route::get('/reports', [MobileReportController::class, 'index']);

    /**
     * Devuelve el listado de administraciones disponibles.
     * Se utiliza para asignar una denuncia a la administración adecuada.
     * Método: GET
     * URL: /api/administrations
     */
    Route::get('/administrations', [MobileAdministrationController::class, 'index']);

    /**
     * Envía una nueva denuncia.
     * Incluye imagen, geolocalización, texto y administración.
     * Método: POST
     * URL: /api/reports
     */
    Route::post('/reports', [MobileReportController::class, 'store']);

    /**
     * Devuelve los datos completos del usuario autenticado.
     * Incluye nombre, apellidos, email, teléfono, dirección y administración.
     * Método: GET
     * URL: /api/user
     */
    Route::get('/user', function (Request $request) {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => trim(collect([$user->street, $user->city, $user->province])->filter()->implode(', ')),
            'administration_name' => optional($user->administration)->name,
        ]);
    });



        /**
     * Guarda o actualiza el Expo Push Token del usuario autenticado.
     * Método: POST
     * URL: /api/expo-token
     */
    Route::post('/expo-token', [ExpoTokenController::class, 'store']);


});

    // Notificaciones enviadas a ciudadanos desde una denuncia
    Route::post('/reports/{report}/notifications', [ReportNotificationController::class, 'store']);
