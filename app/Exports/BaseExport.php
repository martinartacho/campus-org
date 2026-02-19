<?php

namespace App\Exports;

use App\Exports\BaseExport;

class EventAnswersExport extends BaseExport
{
    public function headings(): array
    {
        // Los encabezados se generarán dinámicamente en el map()
        return [];
    }

    public function map($item): array
    {
        // Esta estructura debe coincidir con la vista de impresión
        $row = [
            $item['user']->name,
            $item['user']->email,
        ];
        
        // Agregar respuestas a cada pregunta
        foreach ($item['answers'] as $answer) {
            $row[] = $answer ?? '-';
        }
        
        // Agregar fecha de envío
        $row[] = $item['submission_date']->format('Y-m-d H:i');
        
        return $row;
    }
}