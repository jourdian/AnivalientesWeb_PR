<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * Representa un usuario autenticado del sistema, que puede tener rol 'citizen' o 'institutional'.
 *
 * @property int $id
 * @property string $email
 * @property string|null $password
 * @property string $first_name
 * @property string $last_name
 * @property string|null $phone
 * @property string|null $street
 * @property string|null $city
 * @property string|null $province
 * @property string|null $firebase_uid
 * @property string $role                     Rol del usuario ('citizen' o 'institutional')
 * @property string|null $position            Cargo institucional (si aplica)
 * @property int|null $administration_id
 * @property string|null $photo_path          Ruta de la foto de perfil (almacenada en /storage)
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Administration|null $administration
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'street',
        'city',
        'province',
        'firebase_uid',
        'role',
        'position',
        'administration_id',
        'photo_path', 
        'expo_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts automáticos de atributos del modelo.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación con la administración a la que pertenece este usuario institucional.
     *
     * @return BelongsTo
     */
    public function administration(): BelongsTo
    {
        return $this->belongsTo(Administration::class);
    }
}
