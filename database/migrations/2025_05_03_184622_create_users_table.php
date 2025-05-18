<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Identification and authentication
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Custom profile fields
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();

            // Role type and position
            $table->enum('role', ['citizen', 'institutional'])->default('citizen');
            $table->string('position')->nullable(); 
            $table->string('photo_path')->nullable(); 

            // Firebase UID (optional)
            $table->string('firebase_uid')->nullable()->unique();

            // Administration relation
            $table->foreignId('administration_id')->nullable()->constrained()->nullOnDelete();

            // Tokens and timestamps
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
