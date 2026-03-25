<?php

namespace App\Console\Commands;

use App\Models\ReleaseNote;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class ReleaseGenerateCommand extends Command
{
    protected $signature = 'release:generate {--release-version= : Versió del release (ex: 1.2.3)} {--since= : Tag o commit des del qual generar (ex: v1.2.0)} {--type=minor : Tipus de release (major/minor/patch)} {--preview : Previsualitzar sense guardar} {--auto-fix : Corregir problemes automàticament}';

    protected $description = 'Generar Release Notes automàticament desde commits de Git';

    public function handle()
    {
        $this->info('🚀 Generant Release Notes des de Git...');

        // Obtener o determinar versión
        $version = $this->option('release-version') ?: $this->askForVersion();
        $since = $this->option('since') ?: $this->getLatestTag();
        $type = $this->option('type');
        $preview = $this->option('preview');

        // Obtener commits
        $commits = $this->getCommitsSince($since);
        
        if ($commits->isEmpty()) {
            $this->error('❌ No s\'han trobat commits desde ' . $since);
            return 1;
        }

        $this->info("📝 Analitzant {$commits->count()} commits...");

        // Analizar commits y clasificar
        $classifiedCommits = $this->classifyCommits($commits);
        $releaseData = $this->buildReleaseData($version, $type, $classifiedCommits);

        // Mostrar previsualización
        $this->showPreview($releaseData);

        if ($preview) {
            $this->info('👁️ Mode previsualització - No s\'ha guardat res');
            return 0;
        }

        // Confirmar y guardar
        if ($this->confirm('💾 Vols guardar aquest Release Note?')) {
            $release = $this->saveRelease($releaseData);
            $this->info("✅ Release Note creat amb ID: {$release->id}");
            return 0;
        }

        $this->info('❌ Operació cancel·lada');
        return 0;
    }

    private function askForVersion(): string
    {
        $latestTag = $this->getLatestTag();
        $suggestedVersion = $this->suggestNextVersion($latestTag);
        
        return $this->ask(
            "Versió del release (sugerit: {$suggestedVersion})", 
            $suggestedVersion
        );
    }

    private function getLatestTag(): string
    {
        $result = Process::run(['git', 'describe', '--tags', '--abbrev=0']);
        return $result->successful() ? trim($result->output()) : 'v0.0.0';
    }

    private function suggestNextVersion(string $latestTag): string
    {
        // Simple version bump logic
        if (preg_match('/v(\d+)\.(\d+)\.(\d+)/', $latestTag, $matches)) {
            return "v{$matches[1]}.{$matches[2]}." . ($matches[3] + 1);
        }
        return 'v1.0.0';
    }

    private function getCommitsSince(string $since): \Illuminate\Support\Collection
    {
        $result = Process::run([
            'git', 'log', 
            '--pretty=format:%H|%s|%an|%ad',
            '--date=short',
            "{$since}..HEAD"
        ]);

        if (!$result->successful()) {
            return collect();
        }

        return collect(explode("\n", trim($result->output())))
            ->filter()
            ->map(function ($line) {
                $parts = explode('|', $line, 4);
                return [
                    'hash' => $parts[0] ?? '',
                    'message' => $parts[1] ?? '',
                    'author' => $parts[2] ?? '',
                    'date' => $parts[3] ?? '',
                ];
            });
    }

    private function classifyCommits(\Illuminate\Support\Collection $commits): array
    {
        $classified = [
            'features' => [],
            'fixes' => [],
            'improvements' => [],
            'breaking' => [],
            'docs' => [],
            'other' => []
        ];

        foreach ($commits as $commit) {
            $type = $this->getCommitType($commit['message']);
            $classified[$type][] = $commit;
        }

        return $classified;
    }

    private function getCommitType(string $message): string
    {
        $message = strtolower(trim($message));

        if (Str::startsWith($message, 'feat')) {
            return 'features';
        }
        if (Str::startsWith($message, 'fix')) {
            return 'fixes';
        }
        if (Str::startsWith($message, 'refactor') || Str::startsWith($message, 'perf')) {
            return 'improvements';
        }
        if (Str::contains($message, 'breaking') || Str::contains($message, '!')) {
            return 'breaking';
        }
        if (Str::startsWith($message, 'docs') || Str::startsWith($message, 'chore')) {
            return 'docs';
        }

        return 'other';
    }

    private function buildReleaseData(string $version, string $type, array $classifiedCommits): array
    {
        $title = "Release {$version}";
        $summary = $this->generateSummary($classifiedCommits);
        $content = $this->generateContent($classifiedCommits);

        return [
            'title' => $title,
            'version' => $version,
            'type' => $type,
            'summary' => $summary,
            'content' => $content,
            'features' => $this->extractItems($classifiedCommits['features']),
            'improvements' => $this->extractItems($classifiedCommits['improvements']),
            'fixes' => $this->extractItems($classifiedCommits['fixes']),
            'breaking_changes' => $this->extractItems($classifiedCommits['breaking']),
            'affected_modules' => $this->detectAffectedModules($classifiedCommits),
            'commits' => $this->formatCommits($classifiedCommits),
        ];
    }

    private function generateSummary(array $classifiedCommits): string
    {
        $total = array_sum(array_map('count', $classifiedCommits));
        $features = count($classifiedCommits['features']);
        $fixes = count($classifiedCommits['fixes']);
        
        return "{$total} canvis: {$features} novetats, {$fixes} correccions";
    }

    private function generateContent(array $classifiedCommits): string
    {
        $content = "# Release Notes\n\n";

        if (!empty($classifiedCommits['features'])) {
            $content .= "## 🆕 Novetats\n\n";
            foreach ($classifiedCommits['features'] as $commit) {
                $content .= "- {$commit['message']} ({$commit['hash']})\n";
            }
            $content .= "\n";
        }

        if (!empty($classifiedCommits['improvements'])) {
            $content .= "## 🔧 Millores\n\n";
            foreach ($classifiedCommits['improvements'] as $commit) {
                $content .= "- {$commit['message']} ({$commit['hash']})\n";
            }
            $content .= "\n";
        }

        if (!empty($classifiedCommits['fixes'])) {
            $content .= "## 🐛 Correccions\n\n";
            foreach ($classifiedCommits['fixes'] as $commit) {
                $content .= "- {$commit['message']} ({$commit['hash']})\n";
            }
            $content .= "\n";
        }

        if (!empty($classifiedCommits['breaking'])) {
            $content .= "## ⚠️ Canvis Disruptius\n\n";
            foreach ($classifiedCommits['breaking'] as $commit) {
                $content .= "- {$commit['message']} ({$commit['hash']})\n";
            }
        }

        return $content;
    }

    private function extractItems(array $commits): array
    {
        return array_map(function ($commit) {
            return [
                'title' => $commit['message'],
                'hash' => $commit['hash'],
                'author' => $commit['author'],
                'date' => $commit['date'],
            ];
        }, $commits);
    }

    private function detectAffectedModules(array $classifiedCommits): array
    {
        $modules = [];
        $allCommits = array_merge(...array_values($classifiedCommits));

        foreach ($allCommits as $commit) {
            if (Str::contains($commit['message'], 'admin')) {
                $modules[] = 'admin';
            }
            if (Str::contains($commit['message'], 'campus') || Str::contains($commit['message'], 'teacher')) {
                $modules[] = 'campus';
            }
            if (Str::contains($commit['message'], 'dashboard')) {
                $modules[] = 'dashboard';
            }
            if (Str::contains($commit['message'], 'help')) {
                $modules[] = 'help';
            }
        }

        return array_unique($modules);
    }

    private function formatCommits(array $classifiedCommits): array
    {
        $allCommits = array_merge(...array_values($classifiedCommits));
        return array_map(function ($commit) {
            return [
                'hash' => $commit['hash'],
                'message' => $commit['message'],
                'author' => $commit['author'],
                'date' => $commit['date'],
            ];
        }, $allCommits);
    }

    private function showPreview(array $releaseData): void
    {
        $this->info("\n📋 PREVISUALITZACIÓ DEL RELEASE:");
        $this->info("================================");
        $this->info("Títol: {$releaseData['title']}");
        $this->info("Versió: {$releaseData['version']}");
        $this->info("Tipus: {$releaseData['type']}");
        $this->info("Summary: {$releaseData['summary']}");
        $this->info("Mòduls afectats: " . implode(', ', $releaseData['affected_modules']));
        $this->info("Features: " . count($releaseData['features']));
        $this->info("Fixes: " . count($releaseData['fixes']));
        $this->info("Total commits: " . count($releaseData['commits']));
        
        $this->info("\n📝 Contingut (primeres línies):");
        $lines = explode("\n", $releaseData['content']);
        foreach (array_slice($lines, 0, 10) as $line) {
            $this->info($line);
        }
        if (count($lines) > 10) {
            $this->info("... (" . (count($lines) - 10) . " línies més)");
        }
    }

    private function saveRelease(array $releaseData): ReleaseNote
    {
        return ReleaseNote::create([
            'title' => $releaseData['title'],
            'version' => $releaseData['version'],
            'type' => $releaseData['type'],
            'summary' => $releaseData['summary'],
            'content' => $releaseData['content'],
            'features' => $releaseData['features'],
            'improvements' => $releaseData['improvements'],
            'fixes' => $releaseData['fixes'],
            'breaking_changes' => $releaseData['breaking_changes'],
            'affected_modules' => $releaseData['affected_modules'],
            'commits' => $releaseData['commits'],
            'created_by' => auth()->id() ?? 1,
        ]);
    }
}
