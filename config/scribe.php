<?php

use Knuckles\Scribe\Extracting\Strategies;
use Knuckles\Scribe\Config\Defaults;
use Knuckles\Scribe\Config\AuthIn;
use function Knuckles\Scribe\Config\{removeStrategies, configureStrategy};

return [

    // Título de la documentación en la pestaña del navegador
    'title' => 'AniValientes – Documentación de la API',

    // Descripción general corta (también se usa en OpenAPI y Postman)
    'description' => 'API para registro y gestión de denuncias ciudadanas en el sistema AniValientes.',

    // URL base mostrada en la documentación
    'base_url' => config("app.url"),

    // Rutas incluidas en la documentación (solo API REST)
    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/*'],
                'domains' => ['*'],
            ],
            'include' => [],
            'exclude' => [],
        ],
    ],

    // Tipo de documentación generada: Blade renderizado por Laravel
    'type' => 'laravel',

    'theme' => 'default',

    'static' => [
        'output_path' => 'public/docs',
    ],
'locale' => 'es',

    'laravel' => [
        'add_routes' => true,
        'docs_url' => '/docs',
        'assets_directory' => null,
        'middleware' => [],
    ],

    'external' => [
        'html_attributes' => [],
    ],

    // Habilita pruebas desde el navegador con el botón "Try it out"
    'try_it_out' => [
        'enabled' => true,
        'base_url' => null,
        'use_csrf' => false,
        'csrf_url' => '/sanctum/csrf-cookie',
    ],

    /*
    |--------------------------------------------------------------------------
    | AUTENTICACIÓN CON SANCTUM
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'enabled' => true,
        'default' => true,
        'in' => AuthIn::BEARER->value,
        'name' => 'Authorization',
        'use_value' => env('SCRIBE_AUTH_KEY', 'Bearer {token-de-ejemplo}'),
        'placeholder' => 'Bearer {TOKEN}',
        'extra_info' => <<<HTML
<p>Todos los endpoints requieren autenticación mediante <code>Bearer token</code> (Laravel Sanctum).</p>
<p>Obtén tu token desde la app móvil o a través del login con correo electrónico y contraseña.</p>
HTML,
    ],

    /*
    |--------------------------------------------------------------------------
    | INTRODUCCIÓN PERSONALIZADA (PORTADA)
    |--------------------------------------------------------------------------
    */
    'intro_text' => <<<INTRO
# Documentación de la API AniValientes


AniValientes es una plataforma para la lucha contra el abandono animal.  
A través de esta API, los ciudadanos pueden reportar casos de abandono directamente desde una aplicación móvil, adjuntando imagen, localización y descripción.  
Las administraciones locales pueden recibir y gestionar estos reportes desde un panel web especializado.

Todos los endpoints requieren autenticación con token Bearer (Laravel Sanctum).  
Puedes utilizar los ejemplos interactivos o copiar el código en cURL o JavaScript.

INTRO,

    // Lenguajes para los ejemplos de código en cada endpoint
    'example_languages' => ['bash', 'javascript'],

    // Postman y OpenAPI se generan automáticamente
    'postman' => ['enabled' => true, 'overrides' => []],
    'openapi' => ['enabled' => true, 'overrides' => [], 'generators' => []],

    // Organización de grupos de endpoints
    'groups' => [
        'default' => 'Endpoints',
        'order' => [
            'App Móvil',
            'Autenticación',
        ],
    ],

    // Logo personalizado (opcional)
    'logo' => 'img/Logo_Anivalientes_transparente.png', 

    // Pie de página con fecha
    'last_updated' => 'Última actualización: {date:F j, Y}',

    'examples' => [
        'faker_seed' => 1234,
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    'strategies' => [
        'metadata' => [...Defaults::METADATA_STRATEGIES],
        'headers' => [
            ...Defaults::HEADERS_STRATEGIES,
            Strategies\StaticData::withSettings(data: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]),
        ],
        'urlParameters' => [...Defaults::URL_PARAMETERS_STRATEGIES],
        'queryParameters' => [...Defaults::QUERY_PARAMETERS_STRATEGIES],
        'bodyParameters' => [...Defaults::BODY_PARAMETERS_STRATEGIES],
        'responses' => configureStrategy(
            Defaults::RESPONSES_STRATEGIES,
            Strategies\Responses\ResponseCalls::withSettings(
                only: ['GET *'],
                config: ['app.debug' => false]
            )
        ),
        'responseFields' => [...Defaults::RESPONSE_FIELDS_STRATEGIES],
    ],

    // Conexión de base de datos para simular respuestas
    'database_connections_to_transact' => [config('database.default')],

    'fractal' => ['serializer' => null],
];
