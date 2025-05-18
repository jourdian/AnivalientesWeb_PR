<?php

namespace Database\Seeders;

use App\Models\Administration;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReportsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🐾 Generando denuncias (con humor) de abandono animal...');

        $uocVlc = Administration::where('email', 'info@uocvlc.com')->first();
        $uocBcn = Administration::where('email', 'info@uocbcn.com')->first();

        if (!$uocVlc || !$uocBcn) {
            $this->command->warn('⚠️ No se encontraron las administraciones UOCVLC o UOCBCN. Abortando.');
            return;
        }

        $citizens = User::where('role', 'citizen')->get();

        if ($citizens->isEmpty()) {
            $this->command->warn('⚠️ No hay ciudadanos registrados para asignar como autores.');
            return;
        }

        $denuncias = [
            ['description' => 'Han dejado a un pato con gabardina en un charco del parking. Parece estar esperando a alguien.', 'image_path' => 'report_photos/denuncia1.png'],
            ['description' => 'Encontré un hámster con maleta y pañuelo atado al cuello en una estación de autobús. Nadie lo reclama.', 'image_path' => 'report_photos/denuncia2.png'],
            ['description' => 'Un cerdo con una camiseta de “Soy libre” fue abandonado junto al contenedor de reciclaje.', 'image_path' => 'report_photos/denuncia3.png'],
            ['description' => 'Un gato lleva tres días pidiendo sushi en la puerta de un restaurante. Los camareros creen que es mascota de alguien.', 'image_path' => 'report_photos/denuncia4.png'],
            ['description' => 'Hay un perro con una nota en el cuello que dice “Ya sabes lo que hiciste”. Está en un banco del parque.', 'image_path' => 'report_photos/denuncia5.png'],
            ['description' => 'Una tortuga apareció con un cartel: “Me dejaron aquí en 1998”. Sigue en la misma esquina.', 'image_path' => 'report_photos/denuncia6.png'],
            ['description' => 'Un loro abandonado grita “¡Me han dejado solo otra vez!” cada cinco minutos desde la azotea.', 'image_path' => 'report_photos/denuncia7.png'],
            ['description' => 'Un burro atado frente a una discoteca desde el sábado. Lleva gafas de sol y parece enfadado.', 'image_path' => 'report_photos/denuncia8.png'],
            ['description' => 'Vi a dos conejos con mochilas acampando junto al río. Sospecho que los soltaron ahí.', 'image_path' => 'report_photos/denuncia9.png'],
            ['description' => 'Una cabra vestida de unicornio fue dejada en un parque infantil. Se niega a irse y juega con los columpios.', 'image_path' => 'report_photos/denuncia10.png'],
            ['description' => 'Una paloma en una fuente pública recita poemas en voz alta. Lleva una bufanda y un diario viejo.', 'image_path' => 'report_photos/denuncia11.png'],
            ['description' => 'Un gallo está pintando murales con las patas en un túnel peatonal. Parece que lo han dejado allí con pinturas.', 'image_path' => 'report_photos/denuncia12.png'],
            ['description' => 'Un zorro fue visto intentando pagar con una tarjeta prepago en una tienda de videojuegos. Está solo desde ayer.', 'image_path' => 'report_photos/denuncia13.png'],
            ['description' => 'Un mapache con gorro de lana y auriculares está sentado frente a una cafetería. Pide matcha y parece abandonado.', 'image_path' => 'report_photos/denuncia14.png'],
            ['description' => 'Una oveja con anillo en la oreja y gafas de sol hace directos en un selfie stick. Lleva horas sola en la plaza.', 'image_path' => 'report_photos/denuncia15.png'],
            ['description' => 'Un gato con corbata y maletín ronronea frente a un edificio de oficinas. Al parecer fue despedido y olvidado.', 'image_path' => 'report_photos/denuncia16.png'],
        ];

        $this->crearDenunciasParaAdmin($uocVlc, [39.4947639, -0.6857103], $citizens, $denuncias);
        $this->crearDenunciasParaAdmin($uocBcn, [41.3874, 2.1686], $citizens, $denuncias);

        $otherAdmins = Administration::whereNotIn('id', [$uocVlc->id, $uocBcn->id])->get();

        foreach ($otherAdmins as $admin) {
            for ($i = 0; $i < 3; $i++) {
                $denuncia = fake()->randomElement($denuncias);
                $autor = $citizens->random();

                Report::create([
                    'user_id' => $autor->id,
                    'administration_id' => $admin->id,
                    'title' => Str::limit($denuncia['description'], 50, '...'),
                    'description' => $denuncia['description'],
                    'image_path' => $denuncia['image_path'],
                    'address' => fake()->streetAddress() . ', ' . $admin->city,
                    'status' => fake()->randomElement(['pending', 'reviewing', 'resolved', 'dismissed']),
                    'severity' => fake()->randomElement(['low', 'medium', 'high']),
                    'created_at' => now()->subDays(rand(1, 60)),
                ]);
            }
        }

        // Añadido: denuncias específicas para usuarios evaluables
        $userVlc = User::where('email', 'alumnouocvlc@email.com')->first();
        $userBcn = User::where('email', 'alumnouocbcn@email.com')->first();

        if ($userVlc && $userBcn) {
            $denunciasSeleccionadas = collect($denuncias)->shuffle();

            // 8 para VLC
            foreach ($denunciasSeleccionadas->take(8) as $i => $denuncia) {
                $coords = $this->generateRandomCoordsNear(39.4947639, -0.6857103);
                Report::create([
                    'user_id' => $userVlc->id,
                    'administration_id' => $uocVlc->id,
                    'title' => Str::limit($denuncia['description'], 50, '...'),
                    'description' => $denuncia['description'],
                    'image_path' => $denuncia['image_path'],
                    'address' => fake()->address(),
                    'status' => $i < 4 ? 'pending' : 'resolved',
                    'severity' => fake()->randomElement(['low', 'medium', 'high']),
                    'latitude' => $coords[0],
                    'longitude' => $coords[1],
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);
            }

            // 8 para BCN
            foreach ($denunciasSeleccionadas->take(8) as $i => $denuncia) {
                $coords = $this->generateRandomCoordsNear(41.3874, 2.1686);
                Report::create([
                    'user_id' => $userBcn->id,
                    'administration_id' => $uocBcn->id,
                    'title' => 'Caso visible en app (BCN)',
                    'description' => $denuncia['description'],
                    'image_path' => $denuncia['image_path'],
                    'address' => fake()->address(),
                    'status' => $i < 4 ? 'pending' : 'resolved',
                    'severity' => fake()->randomElement(['low', 'medium', 'high']),
                    'latitude' => $coords[0],
                    'longitude' => $coords[1],
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);
            }

            $this->command->info('📱 Denuncias añadidas para usuarios evaluables.');
        } else {
            $this->command->warn('⚠️ Usuarios evaluables no encontrados. No se les asignaron denuncias directas.');
        }


        $this->command->info('✅ Denuncias graciosas generadas con éxito.');
    }

    private function crearDenunciasParaAdmin($admin, array $coordsBase, $citizens, array $denuncias): void
    {
        $mesActual = now()->startOfMonth();
        $mesAnterior = now()->subMonth()->startOfMonth();

        foreach ($denuncias as $denuncia) {
            $autor = $citizens->random();
            $fechaBase = rand(0, 1) ? $mesActual : $mesAnterior;
            $fecha = $fechaBase->copy()->addDays(rand(0, 27))->setTime(rand(0, 23), rand(0, 59));
            $fecha = $fecha->greaterThan(now()) ? now()->subMinutes(rand(1, 60)) : $fecha;
            
            do {
                $coords = $this->generateRandomCoordsNear(...$coordsBase);
            } while (
                $admin->email === 'info@uocbcn.com' &&
                !(
                    $coords[0] >= 41.34 && $coords[0] <= 41.46 &&
                    $coords[1] >= 2.07 && $coords[1] <= 2.20
                )
            );

            Report::create([
                'user_id' => $autor->id,
                'administration_id' => $admin->id,
                'title' => Str::limit($denuncia['description'], 50, '...'),
                'description' => $denuncia['description'],
                'image_path' => $denuncia['image_path'],
                'address' => fake()->address(),
                'status' => fake()->randomElement(['pending', 'reviewing', 'resolved', 'dismissed']),
                'severity' => fake()->randomElement(['low', 'medium', 'high']),
                'created_at' => $fecha,
                'latitude' => $coords[0],
                'longitude' => $coords[1],
            ]);
        }
    }
    private function crearDenunciasParaUsuario($admin, $user, array $coordsBase, array $denuncias): void
    {
        $mesActual = now()->startOfMonth();
        $mesAnterior = now()->subMonth()->startOfMonth();
    
        $denunciasSeleccionadas = collect($denuncias)->shuffle()->take(8);
    
        foreach ($denunciasSeleccionadas as $i => $denuncia) {
            $fechaBase = rand(0, 1) ? $mesActual : $mesAnterior;
            $fecha = $fechaBase->copy()->addDays(rand(0, 27))->setTime(rand(0, 23), rand(0, 59));
            $fecha = $fecha->greaterThan(now()) ? now()->subMinutes(rand(1, 60)) : $fecha;
                        
            $coords = $this->generateRandomCoordsNear(...$coordsBase);
    
            Report::create([
                'user_id' => $user->id,
                'administration_id' => $admin->id,
                'title' => 'Posible caso de abandono peculiar',
                'description' => $denuncia['description'],
                'image_path' => $denuncia['image_path'],
                'address' => fake()->address(),
                'status' => $i < 4 ? 'pending' : 'resolved',
                'severity' => fake()->randomElement(['low', 'medium', 'high']),
                'created_at' => $fecha,
                'latitude' => $coords[0],
                'longitude' => $coords[1],
            ]);
        }
    }
    
    private function generateRandomCoordsNear(float $lat0, float $lng0, float $radiusKm = 50): array
    {
        $r = $radiusKm * sqrt(mt_rand() / mt_getrandmax());
        $theta = mt_rand() / mt_getrandmax() * 2 * M_PI;

        $dx = $r * cos($theta);
        $dy = $r * sin($theta);

        $lat = $lat0 + ($dy / 111.32);
        $lng = $lng0 + ($dx / (111.32 * cos(deg2rad($lat0))));

        return [$lat, $lng];
    }
}
