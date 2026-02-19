<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventQuestionTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'type',
        'options',
        'required',
        'is_template',
        'template_name',
        'template_description'
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
        'is_template' => 'boolean'
    ];

    /**
     * Scope para obtener solo plantillas
     */
    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    /**
     * Scope para buscar por nombre de plantilla
     */
    public function scopeByName($query, $name)
    {
        return $query->where('template_name', 'like', "%{$name}%");
    }

    /**
     * Obtener opciones como array para formularios
     */
    public function getOptionsArrayAttribute()
    {
        return $this->options ?: [];
    }
}