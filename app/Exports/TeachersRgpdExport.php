<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeachersRgpdExport implements FromCollection, WithHeadings
{
    public function __construct(private string $season) {}

    public function collection(): Collection
    {
        return User::role('teacher')
            ->with(['consents' => function ($q) {
                $q->where('season', $this->season);
            }])
            ->get()
            ->map(function ($teacher) {

                $consent = $teacher->consents->first();

                return [
                    'teacher_id' => $teacher->id,
                    'name' => $teacher->name,
                    'email' => $teacher->email,
                    'rgpd_status' => $consent ? 'ACCEPTED' : 'PENDING',
                    'rgpd_accepted_at' => $consent?->accepted_at,
                    'delegated' => $consent && $consent->delegated_by_user_id ? 'YES' : 'NO',
                    'delegated_by' => $consent?->delegated_by_user_id,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'teacher_id',
            'name',
            'email',
            'rgpd_status',
            'rgpd_accepted_at',
            'delegated',
            'delegated_by',
        ];
    }
}
