<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'area',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByArea($query, $area)
    {
        return $query->where('area', $area);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('name', 'asc');
    }

    // Métodos
    public function getIconClass(): string
    {
        return $this->icon ?? 'bi-question-circle';
    }

    public function getDisplayName(): string
    {
        return $this->name;
    }
    
    // Accessor for active property
    public function getActiveAttribute()
    {
        return $this->is_active;
    }
    
    // Mutator for active property
    public function setActiveAttribute($value)
    {
        $this->attributes['is_active'] = $value;
    }
    
    // Relationships
    public function articles()
    {
        return $this->hasMany(HelpArticle::class, 'help_category_id');
    }
}
