<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;
use Illuminate\Support\Facades\Log;


class MobileReportController extends Controller
{
   /**
 * Obtener denuncias del usuario
 *
 * Devuelve la lista de denuncias realizadas por el usuario autenticado.
 *
 * @group App Móvil
 * @authenticated
 *
 * @response 200 [
 *   {
 *     "id": 14,
 *     "title": "Perro abandonado en la carretera",
 *     "description": "Vi al animal solo desde hace 2 días",
 *     "status": "pending",
 *     "latitude": 39.49,
 *     "longitude": -0.68,
 *     "created_at": "2025-05-01T10:20:00Z"
 *   }
 * ]
 */
    public function index(Request $request)
    {
        Log::info('Accessing /api/reports');
        Log::info('User ID: ' . optional(Auth::user())->id);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $reports = Report::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(
            $reports->map(function ($report) {
                return [
                    'id' => $report->id,
                    'title' => $report->title,
                    'description' => $report->description,
                    'status' => $report->status,
                    'created_at' => $report->created_at,
                    'image_url' => $report->image_path
                        ? asset('storage/' . $report->image_path)
                        : null,
                    'latitude' => (float) $report->latitude,
                    'longitude' => (float) $report->longitude,
                ];
            })
        );
    }

/**
 * Enviar nueva denuncia
 *
 * Este endpoint permite reportar un caso de abandono animal.
 *
 * @group App Móvil
 * @authenticated
 *
 * @bodyParam title string requerido Título de la denuncia. Ejemplo: Perro abandonado
 * @bodyParam description string requerido Descripción del caso. Ejemplo: El perro lleva dos días solo en el campo.
 * @bodyParam latitude number requerido Coordenada latitud. Ejemplo: 39.4948
 * @bodyParam longitude number requerido Coordenada longitud. Ejemplo: -0.6857
 * @bodyParam administration_id integer requerido ID de la administración receptora. Ejemplo: 3
 * @bodyParam photo file requerido Imagen del animal. Ejemplo: archivo JPEG o PNG
 *
 * @response 201 {
 *   "id": 14,
 *   "message": "Denuncia registrada correctamente"
 * }
 */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'administration_id' => 'required|exists:administrations,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'required|image|max:2048',
        ]);

        $user = Auth::user();

        // Guardar imagen en /storage/app/public/report_photos
        $path = $request->file('image')->store('report_photos', 'public');

        // Crear nueva denuncia
        $report = Report::create([
            'user_id' => $user->id,
            'administration_id' => $request->administration_id,
            'title' => $request->title,
            'description' => $request->description,
            'image_path' => $path,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $this->reverseGeocode($request->latitude, $request->longitude),
            'status' => 'pending',
            'severity' => 'medium',
        ]);

        return response()->json([
            'message' => 'Denuncia creada con éxito',
            'report' => $report
        ], 201);
    }

    private function reverseGeocode($lat, $lng)
    {
        return "Ubicación aproximada: $lat, $lng";
    }
}
