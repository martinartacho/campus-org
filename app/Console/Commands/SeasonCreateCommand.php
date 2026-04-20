<?php

namespace App\Console\Commands;

use App\Models\CampusSeason;
use App\Services\SeasonPeriodGenerator;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SeasonCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'season:create 
                            {name? : Nom de la temporada acadèmica}
                            {--start= : Data d\'inici (YYYY-MM-DD)}
                            {--end= : Data de finalització (YYYY-MM-DD)}
                            {--config= : Configuració de períodos (two_semesters, three_trimesters, etc.)}
                            {--active : Marcar com a activa}
                            {--current : Marcar com a actual}
                            {--list : Llistar configuracions disponibles}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear temporada acadèmica i generar períodos automàticament';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('list')) {
            $this->listConfigurations();
            return;
        }

        $name = $this->argument('name');
        
        if (!$name) {
            $this->error('Cal especificar un nom per la temporada acadèmica');
            $this->line('Exemple: php artisan season:create "Curs 2026-27" --config=two_semesters');
            return;
        }

        // Validar que el nom contingui un any acadèmic vàlid
        if (!$this->isValidAcademicYearName($name)) {
            $this->error('El nom ha de contenir un any acadèmic vàlid (format: YYYY-YY)');
            $this->line('Exempels vàlids:');
            $this->line('  - "Curs 2026-27"');
            $this->line('  - "2026-27"');
            $this->line('  - "Academic Year 2026-27"');
            $this->line('  - "Curs 2025-26"');
            return;
        }
        $config = $this->option('config');
        
        // Extreure any acadèmic del nom per generar dates automàtiques
        $academicYear = $this->extractAcademicYear($name);
        
        // Validar dates
        if ($this->option('start')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $this->option('start'));
        } elseif ($academicYear) {
            // Generar dates basades en l'any acadèmic del nom
            $startDate = Carbon::create($academicYear['start_year'], 9, 1);
        } else {
            $startDate = Carbon::create(date('Y'), 9, 1);
        }
        
        if ($this->option('end')) {
            $endDate = Carbon::createFromFormat('Y-m-d', $this->option('end'));
        } elseif ($academicYear) {
            $endDate = Carbon::create($academicYear['end_year'], 6, 30);
        } else {
            $endDate = Carbon::create(date('Y') + 1, 6, 30);
        }

        // Validar que end > start
        if ($endDate->lte($startDate)) {
            $this->error('La data de finalització ha de ser posterior a la data d\'inici');
            return;
        }

        $this->info("Creant temporada: {$name}");
        $this->line("Període: {$startDate->format('d/m/Y')} - {$endDate->format('d/m/Y')}");

        // Crear temporada acadèmica
        $academicYear = $this->createAcademicYear($name, $startDate, $endDate);
        
        // Si s'especifica configuració, generar períodos
        if ($config) {
            $this->generatePeriods($academicYear, $config);
        } else {
            $this->info("Temporada acadèmica creada sense períodos fills");
        }

        $this->info("\nTemporada creada correctament!");
        $this->line("ID: {$academicYear->id} | Nom: {$academicYear->name}");
    }

    private function createAcademicYear(string $name, Carbon $startDate, Carbon $endDate): CampusSeason
    {
        // Si es marca com a actual, desmarcar les altres
        if ($this->option('current')) {
            CampusSeason::where('is_current', true)->update(['is_current' => false]);
        }

        return CampusSeason::create([
            'name' => $name,
            'slug' => \Str::slug($name),
            'season_start' => $startDate,
            'season_end' => $endDate,
            'type' => 'annual',
            'status' => 'active',
            'is_active' => $this->option('active') ?? false,
            'is_current' => $this->option('current') ?? false,
        ]);
    }

    private function generatePeriods(CampusSeason $academicYear, string $configKey): void
    {
        $configurations = SeasonPeriodGenerator::getPredefinedConfigurations();
        
        if (!isset($configurations[$configKey])) {
            $this->error("Configuració '{$configKey}' no trobada");
            $this->line("Usa 'php artisan season:create --list' per veure les opcions");
            return;
        }

        try {
            $generator = new SeasonPeriodGenerator();
            $periods = $generator->generateForAcademicYear($academicYear, $configurations[$configKey]);
            
            $this->info("Generats {$periods->count()} períodos:");
            foreach ($periods as $period) {
                $this->line("  - {$period->name} ({$period->type})");
            }
        } catch (\Exception $e) {
            $this->error("Error generant períodos: {$e->getMessage()}");
        }
    }

    private function listConfigurations(): void
    {
        $this->info('Configuracions de períodos disponibles:');
        $this->table(['Clau', 'Descripció', 'Períodos'], [
            ['two_semesters', '2 Semestres', '2 períodos de 6 mesos'],
            ['three_trimesters', '3 Trimestres', '3 períodos de 4 mesos'],
            ['two_quarters', '2 Quadrimestres', '2 períodos de 4 mesos'],
            ['trimester_plus_quarter', '1 Trimestre + 1 Quadrimestre', '2 períodos: 3 mesos + 4 mesos'],
            ['four_bimensual', '4 Bimensuals', '4 períodos de 2 mesos'],
            ['monthly', '10 Mensuals', '10 períodos de 1 mes'],
        ]);
    }

    /**
     * Validate academic year name format and extract years
     */
    private function isValidAcademicYearName(string $name): bool
    {
        // Pattern per trobar YYYY-YY (ex: 2026-27, 2025-26)
        if (!preg_match('/(\d{4})-(\d{2})/', $name, $matches)) {
            return false;
        }

        $startYear = (int)$matches[1];
        $endYear = (int)$matches[2];
        
        // Validar que sigui un any acadèmic lògic (ex: 2026-27, no 2026-26)
        $expectedEndYear = $startYear + 1;
        $expectedEndYearShort = $expectedEndYear % 100;
        
        return $endYear === $expectedEndYearShort;
    }

    /**
     * Extract academic year from name
     */
    private function extractAcademicYear(string $name): ?array
    {
        if (!preg_match('/(\d{4})-(\d{2})/', $name, $matches)) {
            return null;
        }

        $startYear = (int)$matches[1];
        $endYear = 2000 + (int)$matches[2]; // Convert 27 to 2027
        
        return [
            'start_year' => $startYear,
            'end_year' => $endYear,
            'academic_year' => $matches[1] . '-' . $matches[2]
        ];
    }
}
