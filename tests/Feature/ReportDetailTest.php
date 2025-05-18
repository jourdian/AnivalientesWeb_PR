<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Report;
use App\Models\Administration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_institutional_user_can_view_report_detail()
    {
        $admin = Administration::factory()->create();

        $institutional = User::factory()->create([
            'role' => 'institutional',
            'administration_id' => $admin->id,
        ]);

        $report = Report::factory()->create([
            'administration_id' => $admin->id,
        ]);

        $response = $this->actingAs($institutional)
            ->get("/reports/{$report->id}", ['Accept' => 'application/json']);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $report->title,
            'description' => $report->description,
        ]);
    }

    public function test_citizen_user_cannot_view_report_detail()
    {
        $citizen = User::factory()->create([
            'role' => 'citizen',
        ]);

        $report = Report::factory()->create();

        $response = $this->actingAs($citizen)
            ->get("/reports/{$report->id}");

        $response->assertStatus(403);
    }
}
