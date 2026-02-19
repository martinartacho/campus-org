<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportService
{
    /**
     * Exportar datos a PDF
     *
     * @param string $view Ruta de la vista Blade
     * @param array $data Datos para pasar a la vista
     * @param string $filename Nombre del archivo (opcional)
     * @return mixed
     */
    public function exportToPDF(string $view, array $data, string $filename = null)
    {
        $filename = $filename ?: $this->generateFilename('pdf');
        
        return Pdf::loadView($view, $data)
                  ->setPaper('a4', 'landscape')
                  ->download($filename);
    }

    /**
     * Exportar datos a Excel
     *
     * @param string $exportClass Clase de exportación de Laravel Excel
     * @param mixed $data Datos a exportar
     * @param string $filename Nombre del archivo (opcional)
     * @return BinaryFileResponse
     */
    public function exportToExcel($export, string $filename = null)
    {
        $filename = $filename ?: $this->generateFilename('xlsx');
        
        // Si es una instancia, usarla directamente
        if (is_object($export)) {
            return Excel::download($export, $filename);
        }
        
        // Si es un string, crear una nueva instancia
        return Excel::download(new $export, $filename);
    }

    /**
     * Generar nombre de archivo único
     *
     * @param string $extension
     * @return string
     */
    protected function generateFilename(string $extension): string
    {
        return 'export-' . now()->format('Y-m-d-H-i-s') . '.' . $extension;
    }
}