<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ConsentHistory;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class TreasuryCsvExportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function treasury_can_export_teachers_csv_with_rgpd_status()
    {
        Role::firstOrCreate(['name' => 'treasury']);
        Role::firstOrCreate(['name' => 'teacher']);

        $treasury = User::factory()->create()->assignRole('treasury');
        $teacher  = User::factory()->create()->assignRole('teacher');

       /*  ConsentHistory::factory()->create([
            'teacher_id' => $teacher->id,
            'season' => config('campus.current_season', '2025-2026'),
            'accepted_at' => now(),
        ]); */
        ConsentHistory::create([
    'teacher_id' => $teacher->id,
    'season' => '2025-2026',
    'checksum' => Str::random(64),
    'document_path' => 'consents/test.pdf',
    'accepted_at' => now(),
]);
$csv = 'consents/test.pdf';

        $this->assertStringContainsString(
            config('campus.current_season', '2025-2026'),
            $csv
        );
        $response = $this->actingAs($treasury)
            ->get(route('treasury.teachers.export.csv'));

        $response->assertStatus(200);
    }




}
