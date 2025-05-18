<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Administration;

class SettingsController extends Controller
{
    /**
     * Muestra la página de configuración con usuarios institucionales y filtros aplicables.
     *
     * Esta vista incluye el listado paginado de usuarios pertenecientes a la misma administración,
     * con opciones de búsqueda por nombre, apellidos, email o rol.
     * 
     * @group Administración - Configuración
     * 
     * @queryParam search string Buscar por nombre, apellido o email. Example: Ana
     * @queryParam role string Filtrar por rol (e.g., institutional). Example: institutional
     * 
     * @response scenario=success {
     *   "administration": {
     *     "id": 1,
     *     "name": "Ayuntamiento de Ejemplo",
     *     "logo_path": "admin_logos/logo123.png"
     *   },
     *   "users": {
     *     "data": [
     *       {
     *         "id": 5,
     *         "first_name": "Ana",
     *         "last_name": "Soler",
     *         "email": "ana@ayuntamiento.es",
     *         "role": "institutional"
     *       }
     *     ],
     *     "current_page": 1,
     *     "last_page": 3
     *   },
     *   "filters": {
     *     "search": "Ana",
     *     "role": "institutional"
     *   }
     * }
     */
    public function index(Request $request)
    {
        $admin = Administration::find(Auth::user()->administration_id);

        $search = $request->input('search');
        $role = $request->input('role');

        $usersQuery = User::where('administration_id', $admin?->id)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%");
                });
            })
            ->when($role, function ($query, $role) {
                $query->where('role', $role);
            });

        return Inertia::render('admin/Settings', [
            'administration' => $admin,
            'users' => $usersQuery->orderBy('created_at', 'desc')
                                  ->paginate(10)
                                  ->withQueryString(),
            'filters' => $request->only('search', 'role'),
        ]);
    }

    /**
     * Actualiza el logotipo de la administración actual.
     *
     * Este endpoint permite subir un nuevo logotipo institucional (máx. 2MB).
     * El logotipo anterior se elimina si existe.
     * 
     * @group Administración - Configuración
     * 
     * @bodyParam logo file required Imagen del logotipo. Máximo 2048KB. MIME: image/png, image/jpeg. Example: logo.png
     * 
     * @response 302 {
     *   "redirect": "/admin/settings"
     * }
     */
    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|max:2048',
        ]);

        $admin = $request->user()->administration;

        if ($request->hasFile('logo')) {
            if ($admin->logo_path && Storage::disk('public')->exists($admin->logo_path)) {
                Storage::disk('public')->delete($admin->logo_path);
            }

            $path = $request->file('logo')->store('admin_logos', 'public');
            $admin->logo_path = $path;
            $admin->save();
        }

        return redirect()->route('settings');
    }
}
