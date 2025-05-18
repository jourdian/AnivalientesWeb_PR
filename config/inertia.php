<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Forzar la verificación de existencia de componentes Vue
    |--------------------------------------------------------------------------
    |
    | Esta opción controla si los tests Inertia comprobarán que los
    | componentes definidos en los tests existen realmente.
    | Para testear sin depender del front, se recomienda desactivarlo.
    |
    */

    'testing' => [
        'ensure_pages_exist' => false,
    ],

];
