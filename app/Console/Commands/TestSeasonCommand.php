<?php

namespace App\Console\Commands;

use App\Models\CampusSeason;
use App\Services\SeasonPeriodGenerator;
use Illuminate\Console\Command;
use Carbon\Carbon;

class TestSeasonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'season:test {action} {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test season functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'create':
                $this->createTestSeason();
                break;
            case 'generate':
                $this->testGeneration();
                break;
            case 'list':
                $this->listSeasons();
                break;
            default:
                $this->error('Action not found. Use: create, generate, or list');
                break;
        }
    }
    
    private function createTestSeason()
    {
        $name = $this->option('name') ?? 'Test Academic Year 2026-27';
        
        $season = CampusSeason::create([
            'name' => $name,
            'slug' => \Str::slug($name),
            'season_start' => Carbon::create(2026, 9, 1),
            'season_end' => Carbon::create(2027, 6, 30),
            'type' => 'annual',
            'status' => 'draft',
            'is_active' => false,
            'is_current' => false,
        ]);
        
        $this->info("Created season: {$season->name} (ID: {$season->id})");
    }
    
    private function testGeneration()
    {
        $academicYear = CampusSeason::where('type', 'annual')->latest()->first();
        
        if (!$academicYear) {
            $this->error('No academic year found. Run "season:test create" first.');
            return;
        }
        
        $this->info("Testing generation for: {$academicYear->name}");
        
        try {
            $generator = new SeasonPeriodGenerator();
            $configurations = SeasonPeriodGenerator::getPredefinedConfigurations();
            
            foreach ($configurations as $key => $config) {
                $this->line("\n--- Testing configuration: {$key} ---");
                
                // Delete existing children for this test
                $academicYear->children()->delete();
                
                $periods = $generator->generateForAcademicYear($academicYear, $config);
                
                $this->info("Generated {$periods->count()} periods:");
                foreach ($periods as $period) {
                    $this->line("  - {$period->name} ({$period->type})");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
        }
    }
    
    private function listSeasons()
    {
        $seasons = CampusSeason::all();
        
        $this->table(['ID', 'Name', 'Type', 'Parent ID', 'Status'], 
            $seasons->map(function($season) {
                return [
                    $season->id,
                    $season->name,
                    $season->type,
                    $season->parent_id ?? 'None',
                    $season->status,
                ];
            })
        );
    }
}
