<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Middleware\EnsureInstitutionalUser;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReportNotificationController;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Página de inicio
|--------------------------------------------------------------------------
|
| Esta ruta maneja la entrada inicial a la aplicación web.
| Redirige a los usuarios autenticados institucionales al dashboard,
| o cierra sesión si el usuario no tiene rol autorizado.
|
*/

Route::get('/', function () {
    $user = Auth::user();

    if (! $user) {
        // Si no hay sesión iniciada, redirigir al login
        return redirect()->route('login');
    }

    if ($user->role === 'institutional') {
        // Si el usuario es institucional, ir al dashboard
        return redirect()->route('dashboard');
    }

    // Si no tiene permiso, cerrar sesión y mostrar error
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login')->withErrors([
        'email' => 'Tu cuenta no tiene acceso al panel institucional.',
    ]);
})->name('home');

/*
|--------------------------------------------------------------------------
| Rutas de prueba forzada de errores
|--------------------------------------------------------------------------
|
| Rutas utilizadas para probar el comportamiento de errores 404 y 403.
|
*/

Route::get('/forzar404', function () {
    abort(404);
});

Route::get('/forzar403', function () {
    abort(403, 'Acceso denegado de prueba.');
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas (panel institucional)
|--------------------------------------------------------------------------
|
| Estas rutas están protegidas por middleware de autenticación y verificación,
| y además filtran que el usuario tenga rol 'institutional'.
| Se utiliza EnsureInstitutionalUser como middleware adicional.
|
*/

Route::middleware(['auth', 'verified', EnsureInstitutionalUser::class])->group(function () {

    // API: Datos del dashboard (estadísticas)
    Route::get('/admin/api/dashboard', [DashboardController::class, 'data'])->name('dashboard.data');

    // Vistas principales del panel
    Route::get('/dashboard', fn () => Inertia::render('admin/Dashboard'))->name('dashboard');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');

    // Actualización y detalle de denuncias
    Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');



    // Vista de configuración de la administración
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');

    // Perfil del usuario actual (institucional)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Subida del logotipo institucional
    Route::post('/settings/logo', [SettingsController::class, 'updateLogo'])->name('settings.logo');

    // API: Coordenadas para mapa y datos para heatmap
    Route::get('/admin/api/reports/positions', [ReportController::class, 'mapPositions']);
    Route::get('/admin/api/reports/heatmap', [ReportController::class, 'mapHeatmap'])->middleware('auth');



    // Notificación manual desde el panel institucional
    Route::post('/api/reports/{report}/notifications', [ReportNotificationController::class, 'store'])->name('reports.notifications');

});




/*
|--------------------------------------------------------------------------
| Autenticación (Laravel Breeze)
|--------------------------------------------------------------------------
|
| Carga las rutas de autenticación proporcionadas por Breeze.
|
*/
require __DIR__.'/auth.php';
