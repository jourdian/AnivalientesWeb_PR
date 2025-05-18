<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Administration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LogoUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_institutional_user_can_upload_logo()
    {
        Storage::fake('public');

        $admin = Administration::factory()->create();
        $user = User::factory()->create([
            'role' => 'institutional',
            'administration_id' => $admin->id,
        ]);

        $logo = UploadedFile::fake()->image('logo.png');

        $response = $this->actingAs($user, 'web')
            ->post('/settings/logo', [
                'logo' => $logo,
            ]);

        $response->assertRedirect(); 
        Storage::disk('public')->assertExists("admin_logos/{$logo->hashName()}");
    }

    public function test_citizen_user_cannot_upload_logo()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'citizen',
        ]);

        $logo = UploadedFile::fake()->image('logo.png');

        $response = $this->actingAs($user, 'web')
            ->post('/settings/logo', [
                'logo' => $logo,
            ]);

        $response->assertStatus(403);
    }
}
