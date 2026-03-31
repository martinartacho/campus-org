<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BankingDataRecoveryService;

class RecoverBankingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banking:recover {--fix : Apply fixes automatically}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose and recover corrupted banking data';

    /**
     * Execute the console command.
     */
    public function handle(BankingDataRecoveryService $recoveryService)
    {
        $this->info('🔍 Iniciando diagnóstico de datos bancarios...');
        
        if ($this->option('fix')) {
            $this->warn('⚠️  Se aplicarán correcciones automáticas');
            if (!$this->confirm('¿Desea continuar?')) {
                $this->info('Operación cancelada');
                return;
            }
        }

        $results = $recoveryService->diagnoseAndFix();

        $this->displayResults($results);

        if (!empty($results['errors'])) {
            $this->error('❌ Se encontraron errores:');
            foreach ($results['errors'] as $error) {
                $this->line("  - {$error}");
            }
        }

        $this->info('✅ Diagnóstico completado');
    }

    /**
     * Display recovery results
     */
    private function displayResults(array $results): void
    {
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Total Teachers', $results['total_teachers']],
                ['IBANs Corruptos', $results['corrupted_ibans']],
                ['IBANs Vacíos', $results['empty_ibans']],
                ['IBANs Válidos', $results['valid_ibans']],
                ['Correcciones Aplicadas', $results['fixed_count']],
            ]
        );

        // Progress bar
        $total = $results['total_teachers'];
        $valid = $results['valid_ibans'];
        $percentage = $total > 0 ? ($valid / $total) * 100 : 0;

        $this->line("\n📊 Estado General:");
        $this->line("  Profesores con datos válidos: {$valid}/{$total} ({$percentage}%)");
        
        if ($results['fixed_count'] > 0) {
            $this->info("  ✅ Se corrigieron {$results['fixed_count']} registros");
        }
    }
}
