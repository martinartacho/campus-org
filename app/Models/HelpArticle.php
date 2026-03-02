<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'area',
        'context',
        'status',
        'order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'string',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(HelpCategory::class, 'help_category_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    public function scopeValidated($query)
    {
        return $query->where('status', 'validated');
    }

    public function scopeByArea($query, $area)
    {
        return $query->where('area', $area);
    }

    public function scopeByContext($query, $context)
    {
        return $query->where('context', $context);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('title', 'asc');
    }

    // Métodos
    public function isActive(): bool
    {
        return $this->status === 'validated';
    }

    public function getFormattedContent(): string
    {
        return nl2br(e($this->content));
    }

    public function generateSlug(): string
    {
        return strtolower(str_replace(' ', '-', $this->title));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = $article->generateSlug();
            }
            if (empty($article->created_by)) {
                $article->created_by = auth()->id();
            }
        });

        static::updating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = $article->generateSlug();
            }
            $article->updated_by = auth()->id();
        });
    }
}
