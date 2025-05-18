<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ExpoPushService;
use Illuminate\Support\Facades\Log;



class ReportNotificationController extends Controller
{
    /**
     * Envía una notificación asociada a una denuncia.
     *
     * Esta notificación será visible para el denunciante y puede incluirse en los logs.
     * El usuario autenticado debe pertenecer a la administración de la denuncia.
     * 
     * @group Administración - Notificaciones
     * 
     * @urlParam report int required ID de la denuncia. Example: 12
     * 
     * @bodyParam message string required Contenido de la notificación. Max: 1000 caracteres. 
     * 
     * @response 200 {
     *   "success": true,
     *   "notification": {
     *     "id": 31,
     *     "report_id": 12,
     *     "user_id": 3,
     *     "message": "La incidencia ha sido revisada por el equipo técnico.",
     *     "created_at": "2025-05-10T14:00:00Z",
     *     "updated_at": "2025-05-10T14:00:00Z",
     *     "user": {
     *       "id": 3,
     *       "first_name": "Ana",
     *       "last_name": "Soler",
     *       "email": "ana@ayuntamiento.es"
     *     }
     *   }
     * }
     */
public function store(Request $request, Report $report)
{
    $validated = $request->validate([
        'message' => 'required|string|max:1000',
    ]);

    // Usuario institucional autenticado (quien envía la notificación)
    $adminUser = $request->user();
    $adminUser->loadMissing('administration');

    // Crear notificación en base de datos
    $notification = $report->notifications()->create([
        'user_id' => $adminUser->id,
        'message' => $validated['message'],
    ]);

    // Usuario ciudadano que recibirá la notificación
    $citizen = $report->user;

    // Enviar notificación push si el ciudadano tiene token Expo
    if ($citizen && $citizen->expo_token) {
        $title = 'Nuevo mensaje sobre tu denuncia';
        $body = $validated['message'];

        ExpoPushService::send($citizen->expo_token, $title, $body, [
            'type' => 'manual',
            'report' => $report->toArray(),
            'administration' => optional($adminUser->administration)->name ?? 'Administración',
            'reportTitle' => $report->title,
            'status' => $report->status,
            'severity' => $report->severity,
            'response' => $report->response,
            'message' => $body,
        ]);
    }

    return back();
}


}
