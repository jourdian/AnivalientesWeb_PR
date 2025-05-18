<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ReportNotification
 *
 * Representa una notificación enviada desde la administración al usuario
 * relacionada con una denuncia específica.
 *
 * @property int $id
 * @property int $report_id                 ID de la denuncia asociada
 * @property int $user_id                   ID del usuario institucional que envió la notificación
 * @property string $message                Mensaje de la notificación
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Report $report
 * @property-read \App\Models\User $user
 */
class ReportNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'message',
    ];

    /**
     * Relación con la denuncia a la que pertenece esta notificación.
     *
     * @return BelongsTo
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Relación con el usuario institucional que creó la notificación.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
