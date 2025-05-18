<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * Clase AppServiceProvider
 *
 * Registra servicios globales de la aplicación y comparte datos persistentes con Inertia.js.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra cualquier servicio de aplicación.
     *
     * Este método se utiliza para registrar bindings en el contenedor de servicios.
     * No se ha añadido lógica personalizada en este caso.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Arranca cualquier servicio de aplicación.
     *
     * Este método se ejecuta durante el arranque del framework. Aquí se define
     * la lógica para compartir información global con todas las vistas de Inertia,
     * como el usuario autenticado y su administración asociada.
     *
     * @return void
     */
    public function boot(): void
    {
        Inertia::share([
            'auth' => fn () => Auth::check() ? [
                'id' => Auth::id(),
                'first_name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
                'email' => Auth::user()->email,
                'photo_path' => Auth::user()->photo_path,
                'administration' => optional(Auth::user()->administration)->only(['id', 'name']),
            ] : null,
        ]);
    }
}
