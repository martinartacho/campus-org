<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ConsentHistory;
use App\Models\TreasuryData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Database\Seeders\Test\TreasuryTestSeeder;

class TreasuryConsentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TreasuryTestSeeder::class);
        Storage::fake('private');
    }

    /** @test */
    public function treasury_user_can_generate_consent_pdf()
    {
        $treasury = User::factory()->create();
        $treasury->assignRole('treasury');

        $teacher = User::factory()->teacher()->create();

        TreasuryData::create([
            'teacher_id' => $teacher->id,
            'key' => 'tax_id',
            'value' => '12345678A',
        ]);

        $this->actingAs($treasury)
            ->post(route('treasury.teachers.consent.pdf', $teacher))
            ->assertRedirect();

        $this->assertDatabaseCount('consent_histories', 1);

        $consent = ConsentHistory::first();

        Storage::disk('private')->assertExists($consent->document_path);
    }

    /** @test */
public function consent_pdf_is_generated_only_once_per_season()
{
    $treasury = User::factory()->create();
    $treasury->assignRole('treasury');

    $teacher = User::factory()->teacher()->create();


    $this->actingAs($treasury)
        ->post(route('treasury.teachers.consent.pdf', $teacher));

    $this->assertDatabaseCount('consent_histories', 1);
}


    /** @test */
    public function treasury_user_can_view_consent_history()
    {
        $treasury = User::factory()->create();
        $treasury->assignRole('treasury');

        $teacher = User::factory()->teacher()->create();

        ConsentHistory::create([
            'teacher_id' => $teacher->id,
            'season' => '2025-2026',
            'checksum' => Str::random(64),
            'document_path' => 'consents/test.pdf',
            'accepted_at' => now(),
        ]);

        $this->actingAs($treasury)
            ->get(route('treasury.teachers.consents', $teacher))
            ->assertStatus(200)
            ->assertSee('2025-2026');
    }

    /** @test */
    public function non_treasury_user_cannot_access_consent_system()
    {
        $user = User::factory()->create();
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($user)
            ->post(route('treasury.teachers.consent.pdf', $teacher))
            ->assertForbidden();
    }
}
