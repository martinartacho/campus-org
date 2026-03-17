<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Mostrar dashboard de backups
     */
    public function index()
    {
        // Obtener registros de backups
        $backups = DB::table('backup_records')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($backup) {
                $backup->created_at_formatted = Carbon::parse($backup->created_at)->format('d/m/Y H:i:s');
                $backup->file_size_formatted = $backup->file_size ?? 'N/A';
                $backup->status_badge = $backup->status === 'success' 
                    ? '<span class="badge bg-success">✅ Éxito</span>'
                    : '<span class="badge bg-danger">❌ Error</span>';
                return $backup;
            });

        // Estadísticas
        $stats = $this->getBackupStats();

        // Backups recientes en filesystem
        $recentBackups = $this->getRecentBackups();

        // Espacio en disco
        $diskUsage = $this->getDiskUsage();

        return view('admin.backups.index', compact('backups', 'stats', 'recentBackups', 'diskUsage'));
    }

    /**
     * Ejecutar backup manual
     */
    public function execute(Request $request)
    {
        $environment = $request->input('environment', 'dev');
        
        try {
            // Ejecutar comando de backup
            $exitCode = \Artisan::call("backup:database --environment={$environment}");
            
            if ($exitCode === 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Backup ejecutado exitosamente en entorno {$environment}",
                    'output' => \Artisan::output()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Error ejecutando backup en entorno {$environment}",
                    'output' => \Artisan::output()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descargar backup
     */
    public function download($filename)
    {
        $filePath = "/var/www/backups/{$filename}";
        
        if (!File::exists($filePath)) {
            abort(404, 'Archivo no encontrado');
        }

        // Verificar que sea un archivo de backup válido
        if (!preg_match('/^campus_(dev|prod)_\d{8}_\d{6}\.sql\.gz$/', $filename)) {
            abort(403, 'Archivo no autorizado');
        }

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/gzip',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Eliminar backup
     */
    public function destroy($filename)
    {
        $filePath = "/var/www/backups/{$filename}";
        
        if (!File::exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Archivo no encontrado'
            ], 404);
        }

        // Verificar que sea un archivo de backup válido
        if (!preg_match('/^campus_(dev|prod)_\d{8}_\d{6}\.sql\.gz$/', $filename)) {
            return response()->json([
                'success' => false,
                'message' => 'Archivo no autorizado'
            ], 403);
        }

        try {
            File::delete($filePath);
            
            // Eliminar registro de la base de datos si existe
            DB::table('backup_records')
                ->where('filename', $filename)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Backup eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error eliminando backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de backups
     */
    private function getBackupStats()
    {
        $last24h = DB::table('backup_records')
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        $last7days = DB::table('backup_records')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $successRate = DB::table('backup_records')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as successful')
            ->first();

        $lastBackup = DB::table('backup_records')
            ->where('status', 'success')
            ->orderBy('created_at', 'desc')
            ->value('created_at');

        return [
            'last_24h' => $last24h,
            'last_7days' => $last7days,
            'success_rate' => $successRate->total > 0 
                ? round(($successRate->successful / $successRate->total) * 100, 2) 
                : 0,
            'last_backup' => $lastBackup 
                ? Carbon::parse($lastBackup)->diffForHumans() 
                : 'Nunca',
            'total_backups' => DB::table('backup_records')->count()
        ];
    }

    /**
     * Obtener backups recientes del filesystem
     */
    private function getRecentBackups()
    {
        $backupDir = '/var/www/backups';
        $files = [];

        if (is_dir($backupDir)) {
            $fileList = glob($backupDir . '/campus_*.sql.gz');
            rsort($fileList);
            
            $files = array_slice($fileList, 0, 10);
            
            $files = array_map(function ($file) {
                $filename = basename($file);
                $stat = stat($file);
                
                return [
                    'filename' => $filename,
                    'size' => $this->formatBytes($stat['size']),
                    'modified' => Carbon::createFromTimestamp($stat['mtime'])->format('d/m/Y H:i:s'),
                    'exists_in_db' => DB::table('backup_records')
                        ->where('filename', $filename)
                        ->exists()
                ];
            }, $files);
        }

        return collect($files);
    }

    /**
     * Obtener uso de disco
     */
    private function getDiskUsage()
    {
        $backupDir = '/var/www/backups';
        $totalSpace = disk_total_space(dirname($backupDir));
        $freeSpace = disk_free_space(dirname($backupDir));
        $usedSpace = $totalSpace - $freeSpace;
        
        // Calcular espacio usado por backups
        $backupSize = 0;
        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    $backupSize += filesize($file);
                }
            }
        }

        return [
            'total' => $this->formatBytes($totalSpace),
            'used' => $this->formatBytes($usedSpace),
            'free' => $this->formatBytes($freeSpace),
            'backup_used' => $this->formatBytes($backupSize),
            'usage_percentage' => round(($usedSpace / $totalSpace) * 100, 2)
        ];
    }

    /**
     * Formatear bytes
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
