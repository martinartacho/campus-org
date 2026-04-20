<?php

namespace App\Services;

use App\Models\CampusSeason;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SeasonPeriodGenerator
{
    /**
     * Generate periods for an academic year
     */
    public function generateForAcademicYear(CampusSeason $academicYear, array $periodConfig): Collection
    {
        if (!$academicYear->isAcademicYear()) {
            throw new \InvalidArgumentException('El season proporcionado no es un año académico');
        }

        $periods = collect();
        $startDate = $academicYear->season_start;
        
        foreach ($periodConfig as $index => $config) {
            $period = $this->createPeriod($academicYear, $config, $index + 1, $startDate);
            $periods->push($period);
            
            // Update start date for next period
            $startDate = $period->season_end->copy()->addDay();
        }

        return $periods;
    }

    /**
     * Create a single period
     */
    protected function createPeriod(CampusSeason $academicYear, array $config, int $periodNumber, Carbon $startDate): CampusSeason
    {
        $durationMonths = $this->getDurationInMonths($config['type']);
        $seasonEnd = $startDate->copy()->addMonths($durationMonths)->subDay();
        
        // Fechas de registro pueden estar fuera del período padre
        $registrationStart = $config['registration_start'] ?? $startDate->copy()->subMonths(2);
        $registrationEnd = $config['registration_end'] ?? $startDate->copy()->subWeek();

        return CampusSeason::create([
            'parent_id' => $academicYear->id,
            'name' => $config['name'] ?? $this->generatePeriodName($academicYear, $config['type'], $periodNumber),
            'slug' => \Str::slug($config['name'] ?? $this->generatePeriodName($academicYear, $config['type'], $periodNumber)),
            'academic_year' => $academicYear->academic_year,
            'type' => $config['type'],
            'season_start' => $startDate,
            'season_end' => $seasonEnd,
            'registration_start' => $registrationStart,
            'registration_end' => $registrationEnd,
            'status' => $config['status'] ?? 'planning',
            'is_active' => $config['is_active'] ?? true,
            'is_current' => false,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Get duration in months for period type
     */
    protected function getDurationInMonths(string $type): int
    {
        return match($type) {
            'annual' => 12,
            'semester' => 6,
            'trimester' => 3,
            'quarter' => 4,
            'bimensual' => 2,
            'monthly' => 1,
            'custom' => 1, // Default, should be specified in config
            default => throw new \InvalidArgumentException("Tipo de período no reconocido: {$type}"),
        };
    }

    /**
     * Generate period name
     */
    protected function generatePeriodName(CampusSeason $academicYear, string $type, int $periodNumber): string
    {
        $typeLabels = [
            'semester' => 'Semestre',
            'trimester' => 'Trimestre',
            'quarter' => 'Quadrimestre',
            'bimensual' => 'Període Bimensual',
            'monthly' => 'Període Mensual',
            'custom' => 'Període Especial',
        ];

        $ordinalLabels = [
            1 => '1r',
            2 => '2n',
            3 => '3r',
            4 => '4t',
        ];

        $typeLabel = $typeLabels[$type] ?? 'Període';
        $ordinal = $ordinalLabels[$periodNumber] ?? $periodNumber . 'è';

        return "{$academicYear->name} - {$ordinal} {$typeLabel}";
    }

    /**
     * Get predefined configurations
     */
    public static function getPredefinedConfigurations(): array
    {
        return [
            'two_semesters' => [
                ['type' => 'semester'],
                ['type' => 'semester'],
            ],
            'three_trimesters' => [
                ['type' => 'trimester'],
                ['type' => 'trimester'],
                ['type' => 'trimester'],
            ],
            'two_quarters' => [
                ['type' => 'quarter'],
                ['type' => 'quarter'],
            ],
            'trimester_plus_quarter' => [
                ['type' => 'trimester'],
                ['type' => 'quarter'],
            ],
            'four_bimensual' => [
                ['type' => 'bimensual'],
                ['type' => 'bimensual'],
                ['type' => 'bimensual'],
                ['type' => 'bimensual'],
            ],
            'monthly' => [
                ['type' => 'monthly'],
                ['type' => 'monthly'],
                ['type' => 'monthly'],
                ['type' => 'monthly'],
                ['type' => 'monthly'],
                ['type' => 'monthly'],
                ['type' => 'monthly'],
                ['type' => 'monthly'],
                ['type' => 'monthly'],
                ['type' => 'monthly'],
            ],
        ];
    }

    /**
     * Validate period configuration
     */
    public function validateConfiguration(array $config): bool
    {
        // Validar que no exista el mismo tipo para el mismo parent
        if (isset($config['parent_id']) && isset($config['type'])) {
            $exists = CampusSeason::where('parent_id', $config['parent_id'])
                ->where('type', $config['type'])
                ->exists();
            
            if ($exists) {
                return false;
            }
        }

        return true;
    }
}
