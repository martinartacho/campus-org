<?php

namespace Database\Factories;

use App\Models\ConsentHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsentHistoryFactory extends Factory
{
    protected $model = ConsentHistory::class;

    public function definition(): array
    {
        return [
            'season' => config('campus.current_season', '2025-2026'),
            'checksum' => fake()->sha256(),
            'accepted_at' => now(),
        ];
    }
}
