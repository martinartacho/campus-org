<?php

namespace App\Services\Dashboard;

use App\Models\User;
use Illuminate\Support\Collection;

class StudentDashboardData
{
    public function build(User $user): array
    {
        return [
            'student' => $user->student ?? null,
            'courses' => collect(), // Collection, no array
            'stats'   => [],
            'debug'   => null,
            'error'   => null,
        ];
    }

    public static function from(User $user): array
    {
        return (new self())->build($user);
    }
}
