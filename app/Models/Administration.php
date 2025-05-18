<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Administration
 *
 * Representa una administración pública o entidad receptora de denuncias.
 *
 * @property int $id
 * @property string $name               Nombre de la administración
 * @property string|null $phone         Teléfono de contacto
 * @property string $email              Correo electrónico
 * @property string|null $logo_path     Ruta del logotipo en almacenamiento público
 * @property string|null $address       Dirección completa
 * @property string|null $city          Ciudad
 * @property string|null $province      Provincia
 * @property float|null $latitude       Coordenada de latitud (para geolocalización)
 * @property float|null $longitude      Coordenada de longitud (para geolocalización)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 */
class Administration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'logo_path',
        'address',
        'city',
        'province',
        'latitude',
        'longitude',
    ];

    /**
     * Relación: una administración tiene muchos usuarios.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
