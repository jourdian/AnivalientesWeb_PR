<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Obtener datos del dashboard para estadísticas.
     *
     * Este endpoint proporciona dos tipos de datos:
     * - La distribución de estados de las denuncias (gráfico circular).
     * - La cantidad de denuncias por día (gráfico de barras).
     *
     * @group Dashboard (Administrador)
     *
     * @queryParam range string El rango de tiempo a considerar. Opciones: `week` (últimos 7 días) o `month` (últimos 30 días). Por defecto: `week`. Ejemplo: month
     *
     * @response 200 {
     *   "statusDistribution": {
     *     "pending": 5,
     *     "resolved": 8,
     *     "dismissed": 2
     *   },
     *   "reportsByDay": [
     *     { "name": "03/05", "reports": 2 },
     *     { "name": "04/05", "reports": 5 }
     *   ]
     * }
     */
    public function data(Request $request)
    {
        $range = $request->query('range', 'week');

        $startDate = $range === 'month'
            ? Carbon::now()->subDays(30)->startOfDay()
            : Carbon::now()->subDays(6)->startOfDay();

        // Gráfico circular: distribución de estados de denuncia
        // La denuncias pueden estar en estado: pendiente, en proceso, resuelto o desestimado
        $statusDistribution = Report::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Gráfico de barras: denuncias por día
        $reportsByDay = Report::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($r) => [
                'name' => Carbon::parse($r->date)->translatedFormat('d/m'),
                'reports' => $r->count,
            ])
            ->values();

        return response()->json([
            'statusDistribution' => $statusDistribution,
            'reportsByDay' => $reportsByDay,
        ]);
    }
}
