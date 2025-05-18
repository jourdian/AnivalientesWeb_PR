<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('administrations', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable(); 
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();

            // 👇 Coordenadas geográficas para el mapa
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Primero elimina la clave foránea en la tabla users si existe
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('administration_id');
        });

        Schema::dropIfExists('administrations');
    }
};
