<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

/**
 * Clase RouteServiceProvider
 *
 * Define y registra todos los grupos de rutas de la aplicación. Separa las rutas por contexto
 * (API, web, admin, etc.) y aplica los middlewares correspondientes.
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap para la definición de rutas.
     *
     * Registra los distintos grupos de rutas con sus respectivos middlewares y prefijos.
     * Incluye rutas específicas para entorno local (como Firebase) y separa el acceso administrativo.
     *
     * @return void
     */
    public function boot(): void
    {
        // Rutas web principales (institucional)
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        // Rutas de autenticación Breeze
        Route::middleware('web')
            ->group(base_path('routes/auth.php'));

        // ✅ Rutas API públicas (por ejemplo: login móvil)
        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));

        // Rutas API específicas de denuncias
/*         Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api/reports.php')); */

        // Rutas administrativas (panel web)
/*         Route::middleware(['web', 'auth'])
            ->prefix('admin')
            ->group(function () {
                Route::group([], base_path('routes/admin/dashboard.php'));
                Route::group([], base_path('routes/admin/settings.php'));
            }); */

        // Rutas de pruebas Firebase (solo en local)
        if ($this->app->environment('local')) {
            Route::middleware('web')
                ->group(base_path('routes/firebase.php'));
        }
    }
}
