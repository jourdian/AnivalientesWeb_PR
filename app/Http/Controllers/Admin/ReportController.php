<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Services\ExpoPushService;


class ReportController extends Controller
{
    /**
     * Listado paginado de denuncias para la administración.
     *
     * Devuelve una vista Inertia con filtros por estado y nombre del denunciante.
     * Añade manualmente los enlaces « y » a la paginación.
     *
     * @group Administración - Denuncias
     *
     * @queryParam status string Estado de la denuncia: pending, reviewing, resolved, dismissed. Ejemplo: pending
     * @queryParam search string Texto para buscar por nombre o apellidos del denunciante. Ejemplo: García
     *
     * @response View (Inertia)
     */
    public function index(Request $request)
    {
        $admin = Auth::user()->administration;

        $query = Report::with('user')->where('administration_id', $admin->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%");
            });
        }

        $reports = $query->latest()->paginate(8)->withQueryString();

        $reportsArray = $reports->toArray();

        $links = collect($reportsArray['links'])->map(function ($link) {
            if ($link['label'] === 'pagination.previous') $link['label'] = '←';
            elseif ($link['label'] === 'pagination.next') $link['label'] = '→';
            return $link;
        });

        $firstPageUrl = $reports->currentPage() === 1 ? null : $reports->url(1);
        $lastPageUrl = $reports->currentPage() === $reports->lastPage() ? null : $reports->url($reports->lastPage());

        $links->prepend(['url' => $firstPageUrl, 'label' => '«', 'active' => false]);
        $links->push(['url' => $lastPageUrl, 'label' => '»', 'active' => false]);

        $reportsArray['links'] = $links->toArray();

        return Inertia::render('admin/Reports', [
            'reports' => $reportsArray,
            'filters' => $request->only('status', 'search'),
            'user' => Auth::user()->load('administration'),
        ]);
    }

    /**
     * Actualiza los campos `response`, `status` y `severity` de una denuncia.
     *
     * @group Administración - Denuncias
     *
     * @urlParam report int ID de la denuncia. Example: 42
     * @bodyParam response string Descripción de la actuación realizada. Nullable. Example: Se envió equipo técnico.
     * @bodyParam status string Estado de la denuncia. Obligatorio. oneOf: pending, reviewing, resolved, dismissed
     * @bodyParam severity string Nivel de gravedad. Obligatorio. oneOf: low, medium, high, critical
     *
     * @response redirect
     */
public function update(Request $request, Report $report)
{
    if ($request->user()->administration_id !== $report->administration_id) {
        abort(403, 'No puedes modificar esta denuncia');
    }

    $validated = $request->validate([
        'response' => ['nullable', 'string', 'max:1000'],
        'status' => ['required', 'in:pending,reviewing,resolved,dismissed'],
        'severity' => ['required', 'in:low,medium,high,critical'],
    ]);

    $report->update($validated);

    $user = $report->user;

    if ($user && $user->expo_token) {
        $title = "La denuncia ha sido actualizada";

        $bodyLines = [
            "Denuncia: «{$report->title}»",
            "Actuación: " . ($report->response ?: "Sin especificar"),
            "Estado: " . ucfirst($report->status),
            "Urgencia: " . ucfirst($report->severity),
        ];

        $body = implode("\n", $bodyLines);
ExpoPushService::send($report->user->expo_token,
    'Su denuncia "' . $report->title . '" ha sido actualizada',
    "Actuación: " . ($report->response ?: 'Sin respuesta') . "\n" .
    "Estado: " . $report->status . "\n" .
    "Urgencia: " . $report->severity,
    [
        'type' => 'update', 
        'report' => $report->toArray(),
        'administration' => optional($report->user->administration)->name ?? 'Administración',
        'reportTitle' => $report->title,
        'status' => $report->status,
        'severity' => $report->severity,
        'response' => $report->response,
    ]
);

    }

    return redirect()->back();
}


    /**
     * Devuelve las posiciones geográficas de las denuncias.
     *
     * Solo incluye las denuncias con latitud y longitud no nulas.
     *
     * @group Administración - Denuncias
     *
     * @response 200 [
     *   { "id": 1, "latitude": 39.47, "longitude": -0.37, "description": "Basura acumulada" },
     *   ...
     * ]
     */
    public function mapPositions(Request $request)
    {
        $admin = Auth::user()->administration;

        $reports = Report::where('administration_id', $admin->id)
            ->select('id', 'latitude', 'longitude', 'description')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return response()->json($reports);
    }

    /**
     * Devuelve datos para generar un heatmap de denuncias por día en un mes concreto.
     *
     * @group Administración - Denuncias
     *
     * @queryParam month int Mes (1-12). Required. Example: 5
     * @queryParam year int Año (YYYY). Required. Example: 2025
     *
     * @response 200 [
     *   { "date": "2025-05-01", "count": 3 },
     *   ...
     * ]
     */
    public function mapHeatmap(Request $request)
    {
        $admin = Auth::user()->administration;

        $month = $request->input('month');
        $year = $request->input('year');

        if (!$month || !$year) {
            return response()->json([], 400);
        }

        $data = Report::where('administration_id', $admin->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }

    /**
     * Devuelve los datos completos de una denuncia específica.
     *
     * Incluye usuario denunciante y notificaciones asociadas.
     *
     * @group Administración - Denuncias
     *
     * @urlParam report int ID de la denuncia. Example: 42
     *
     * @response 200 {
     *   "id": 42,
     *   "title": "Perrito abandonado",
     *   "description": "He encontrado un perrito pidiendo pan en la esquina de la iglesia...",
     *   "user": { "id": 5, "first_name": "Luis", "last_name": "Pérez" },
     *   "notifications": [...]
     * }
     */
    public function show(Report $report)
    {
        $report->load(['user', 'notifications.user']);

        return response()->json($report);
    }
}
