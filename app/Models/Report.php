<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Report
 *
 * Representa una denuncia realizada por un ciudadano.
 *
 * @property int $id
 * @property int $user_id                         ID del usuario que creó la denuncia
 * @property int $administration_id               ID de la administración destinataria
 * @property string $title                        Título de la denuncia
 * @property string $description                  Descripción de la denuncia
 * @property string|null $image_path              Ruta a la imagen subida (si existe)
 * @property string|null $address                 Dirección textual aproximada
 * @property float|null $latitude                 Coordenada de latitud (opcional)
 * @property float|null $longitude                Coordenada de longitud (opcional)
 * @property string $status                       Estado de la denuncia (pending, reviewing, resolved, dismissed)
 * @property string $severity                     Gravedad del incidente (low, medium, high, critical)
 * @property string|null $response                Respuesta oficial de la administración
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Administration $administration
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ReportNotification[] $notifications
 */
class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'administration_id',
        'title',
        'description',
        'image_path',
        'address',
        'latitude',
        'longitude',
        'status',
        'severity', 
        'response',
    ];

    /**
     * Relación con el usuario que envió la denuncia.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la administración que recibe la denuncia.
     *
     * @return BelongsTo
     */
    public function administration(): BelongsTo
    {
        return $this->belongsTo(Administration::class);
    }

    /**
     * Relación con las notificaciones asociadas a esta denuncia.
     *
     * @return HasMany
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(ReportNotification::class);
    }
}
