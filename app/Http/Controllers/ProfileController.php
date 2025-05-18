<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Mostrar la vista de edición del perfil.
     *
     * Devuelve la vista Inertia con los datos del usuario autenticado para que pueda revisar su perfil.
     *
     * @group Web Admin
     * @authenticated
     * 
     * @response 200 {
     *   "user": {
     *     "id": 5,
     *     "first_name": "Juan",
     *     "last_name": "Pérez",
     *     "email": "juan@example.com",
     *     "photo_path": "user_photos/xyz.jpg",
     *     "phone": "123456789",
     *     "position": "Jefe de unidad"
     *   }
     * }
     */
    public function edit()
    {
        return Inertia::render('admin/Profile', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Actualizar el perfil del usuario autenticado.
     *
     * Este endpoint permite a un usuario institucional actualizar sus datos personales y foto de perfil.
     *
     * @group Web Admin
     * @authenticated
     * 
     * @bodyParam first_name string Nombre del usuario. Example: Juan
     * @bodyParam last_name string Apellidos del usuario. Example: Pérez
     * @bodyParam phone string Teléfono de contacto. Example: 600123456
     * @bodyParam position string Cargo dentro de la administración. Example: Coordinador
     * @bodyParam photo file Foto de perfil (jpg, png, etc.). Example: (file)
     * 
     * @response 302 {
     *   "message": "Perfil actualizado correctamente."
     * }
     */
    public function update(Request $request)
    {
            
        
        $user = $request->user();

 $validated = $request->validate([
    'first_name' => 'sometimes|required|string|max:255',
    'last_name'  => 'sometimes|required|string|max:255',
    'phone'      => 'nullable|string|max:20',
    'position'   => 'nullable|string|max:255',
    'photo'      => 'nullable|image|max:2048', 
]);

if ($request->hasFile('photo')) {
    // Elimina la anterior si existe
    if ($user->photo_path && Storage::disk('public')->exists($user->photo_path)) {
        Storage::disk('public')->delete($user->photo_path);
    }

    // Guarda la nueva y actualiza photo_path
    $path = $request->file('photo')->store('user_photos', 'public');
    $validated['photo_path'] = $path; // 👈 importante: esto va en la DB
}

        // Actualiza el perfil del usuario con los datos validados
        $user->update($validated);
        Auth::setUser($user->fresh());

        return redirect()->route('profile.edit')->with('success', 'Perfil actualizado correctamente.');
    }
}
