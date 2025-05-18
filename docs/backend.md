# Estructura y funcionamiento del backend

Este documento describe la organización del backend de AniValientes, basado en Laravel 10. Incluye la estructura general del código, patrones aplicados y recomendaciones para su mantenimiento o ampliación.

---

## 🏛️ Estructura general de carpetas

El backend reside en la carpeta `AnivalientesWeb/`, organizado según las convenciones de Laravel:

```
app/
├── Console/              # Comandos de consola
├── Exceptions/           # Manejadores de errores
├── Http/
│   ├── Controllers/      # Controladores HTTP
│   │   ├── Admin/        # Controladores para panel institucional
│   │   └── Api/          # Controladores para la app móvil (REST)
│   ├── Middleware/       # Middleware personalizado
│   └── Requests/         # Validadores de peticiones
├── Models/               # Modelos Eloquent
├── Providers/            # Proveedores del framework

routes/
├── web.php               # Rutas para el panel web
├── api.php               # Rutas REST de la app móvil

resources/views/          # Vistas Blade (usadas por Inertia y Scribe)
database/migrations/      # Migraciones de la base de datos
config/                   # Archivos de configuración
public/                   # Archivos accesibles vía web
```

---

## 🌐 Rutas y dominios

* `routes/web.php` → panel web institucional
* `routes/api.php` → API REST para la app móvil (protegido por Sanctum)

Uso de grupos y middleware:

```php
Route::middleware(['auth', 'verified', EnsureInstitutionalUser::class])->group(function () {
    Route::get('/dashboard', ...);
    Route::put('/reports/{report}', ...);
});
```

---

## 🔁 Flujo de peticiones

1. El navegador/app realiza una solicitud HTTP
2. Laravel enruta la petición según `web.php` o `api.php`
3. Se aplica middleware (autenticación, roles)
4. Se llama al controlador correspondiente
5. El controlador valida con Form Request (si aplica)
6. Se consulta/actualiza la base de datos con Eloquent
7. Se devuelve una respuesta JSON o una vista

---

## ⚖️ Validaciones y seguridad

* Se usan clases de validación personalizadas (`FormRequest`) para evitar lógica en el controlador
* Middleware para proteger roles y rutas
* Validación CSRF en el panel web
* Token Bearer en la app (Laravel Sanctum)
* Políticas y autorizaciones pueden añadirse si se amplía

---

## 📑 Controladores principales

### Web (panel institucional)

* `DashboardController`: carga métricas y gráficas
* `ReportController`: listar, actualizar y mostrar denuncias
* `ReportNotificationController`: enviar respuestas y notificaciones
* `SettingsController`: información y configuración institucional
* `ProfileController`: perfil del usuario

### API (app móvil)

* `LoginController`: autenticación con email y contraseña
* `MobileReportController`: denuncias ciudadanas (listar, crear)
* `MobileAdministrationController`: listar administraciones

---

## 🔄 Integración con Scribe (documentación API)

* Se usa [Scribe](https://scribe.knuckles.wtf) para documentar la API REST
* Anotaciones `@group`, `@authenticated`, `@bodyParam`, `@response` en los controladores
* Comando para regenerar docs:

```bash
docker compose exec app php artisan scribe:generate
```

* Docs disponibles en `/docs` durante desarrollo

---

## 🛠️ Recomendaciones para ampliación

* Mantener la separación entre controladores web y móviles
* Usar `FormRequest` siempre que se validen entradas
* Crear servicios (Service classes) si la lógica de negocio crece
* Aplicar patrones como Repositorio si se generalizan consultas
* Documentar nuevos endpoints con Scribe de forma sistemática
* Añadir tests a `tests/Feature` conforme se amplíe la funcionalidad

---

## 🔗 Dependencias clave

* `laravel/sanctum` – para la autenticación de la app
* `knuckleswtf/scribe` – para documentar la API
* `inertiajs/inertia-laravel` – para la integración con Vue

Ver `composer.json` para la lista completa.
