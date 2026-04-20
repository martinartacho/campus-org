<?php

namespace App\Console\Commands;

use App\Models\CampusSeason;
use Illuminate\Console\Command;

class SeasonManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'season:manage 
                            {action : Acció a executar (list, activate, deactivate, current, delete)}
                            {--id= : ID de la temporada}
                            {--force : Forçar acció sense confirmació}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gestionar temporades acadèmiques existents';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'list':
                $this->listSeasons();
                break;
            case 'activate':
                $this->activateSeason();
                break;
            case 'deactivate':
                $this->deactivateSeason();
                break;
            case 'current':
                $this->setCurrentSeason();
                break;
            case 'delete':
                $this->deleteSeason();
                break;
            default:
                $this->error('Acció no vàlida. Opcions: list, activate, deactivate, current, delete');
                break;
        }
    }

    private function listSeasons(): void
    {
        $seasons = CampusSeason::with('parent')->get();
        
        $this->info('Llista de Temporades:');
        $this->table(['ID', 'Nom', 'Tipus', 'Pare', 'Estat', 'Activa', 'Actual'], 
            $seasons->map(function($season) {
                return [
                    $season->id,
                    $season->name,
                    $season->type,
                    $season->parent ? $season->parent->name : 'Cap',
                    $season->status,
                    $season->is_active ? 'Sí' : 'No',
                    $season->is_current ? 'Sí' : 'No',
                ];
            })
        );
    }

    private function activateSeason(): void
    {
        $id = $this->option('id');
        if (!$id) {
            $this->error('Cal especificar --id per activar una temporada');
            return;
        }

        $season = CampusSeason::find($id);
        if (!$season) {
            $this->error("Temporada amb ID {$id} no trobada");
            return;
        }

        if (!$this->option('force') && !$this->confirm("Activar la temporada '{$season->name}'?")) {
            $this->info('Acció cancel·lada');
            return;
        }

        $season->update(['is_active' => true]);
        $this->info("Temporada '{$season->name}' activada correctament");
    }

    private function deactivateSeason(): void
    {
        $id = $this->option('id');
        if (!$id) {
            $this->error('Cal especificar --id per desactivar una temporada');
            return;
        }

        $season = CampusSeason::find($id);
        if (!$season) {
            $this->error("Temporada amb ID {$id} no trobada");
            return;
        }

        if (!$this->option('force') && !$this->confirm("Desactivar la temporada '{$season->name}'?")) {
            $this->info('Acció cancel·lada');
            return;
        }

        $season->update(['is_active' => false]);
        $this->info("Temporada '{$season->name}' desactivada correctament");
    }

    private function setCurrentSeason(): void
    {
        $id = $this->option('id');
        if (!$id) {
            $this->error('Cal especificar --id per establir com a actual');
            return;
        }

        $season = CampusSeason::find($id);
        if (!$season) {
            $this->error("Temporada amb ID {$id} no trobada");
            return;
        }

        if (!$this->option('force') && !$this->confirm("Establir '{$season->name}' com a temporada actual?")) {
            $this->info('Acció cancel·lada');
            return;
        }

        // Desmarcar totes les altres
        CampusSeason::where('is_current', true)->update(['is_current' => false]);
        
        // Marcar aquesta com a actual
        $season->update(['is_current' => true]);
        
        $this->info("Temporada '{$season->name}' establida com a actual");
    }

    private function deleteSeason(): void
    {
        $id = $this->option('id');
        if (!$id) {
            $this->error('Cal especificar --id per esborrar una temporada');
            return;
        }

        $season = CampusSeason::with('children')->find($id);
        if (!$season) {
            $this->error("Temporada amb ID {$id} no trobada");
            return;
        }

        if ($season->children->count() > 0) {
            $this->error("Aquesta temporada té {$season->children->count()} períodes fills. Esborra'ls primer.");
            return;
        }

        if (!$this->option('force') && !$this->confirm("Esborrar permanentment la temporada '{$season->name}'?")) {
            $this->info('Acció cancel·lada');
            return;
        }

        $season->delete();
        $this->info("Temporada '{$season->name}' esborrada correctament");
    }
}
