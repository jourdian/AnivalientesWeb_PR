<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Administration;

class MobileAdministrationController extends Controller
{
/**
 * Listado de administraciones
 *
 * Devuelve todas las administraciones registradas, utilizadas para asociar denuncias.
 *
 * @group App Móvil
 * @authenticated
 *
 * @response 200 [
 *   {
 *     "id": 1,
 *     "name": "Ayuntamiento de UOC",
 *     "latitude": 39.49,
 *     "longitude": -0.68,
 *     "city": "UOC City"
 *   }
 * ]
 */
    public function index()
    {
        return Administration::select('id', 'name', 'email', 'latitude', 'longitude')->get();
    }
}
