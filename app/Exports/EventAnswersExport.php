<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventAnswersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;
    protected $questions;

    public function __construct($data, $questions)
    {
        $this->data = $data;
        $this->questions = $questions;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        $headings = ['User', 'Email'];
        
        foreach ($this->questions as $question) {
            $headings[] = $question->question;
        }
        
        $headings[] = 'Submission Date';
        
        return $headings;
    }

    public function map($item): array
    {
        $row = [
            $item['user']->name,
            $item['user']->email,
        ];
        
        // Agregar respuestas a cada pregunta
        foreach ($this->questions as $question) {
            $answer = $item['answers']->firstWhere('question_id', $question->id);
            $row[] = $answer ? $answer->answer : '-';
        }
        
        // Agregar fecha de envío
        $row[] = $item['submission_date']->format('Y-m-d H:i');
        
        return $row;
    }
}