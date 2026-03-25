<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReleaseNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'version',
        'type',
        'status',
        'summary',
        'content',
        'features',
        'improvements',
        'fixes',
        'breaking_changes',
        'affected_modules',
        'target_audience',
        'commits',
        'published_at',
        'created_by',
        'published_by',
    ];

    protected $casts = [
        'features' => 'array',
        'improvements' => 'array',
        'fixes' => 'array',
        'breaking_changes' => 'array',
        'affected_modules' => 'array',
        'target_audience' => 'array',
        'commits' => 'array',
        'published_at' => 'datetime',
    ];

    // Relaciones
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    // Métodos
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function getFormattedVersion(): string
    {
        return $this->version;
    }

    public function getFormattedContent(): string
    {
        // Convertir Markdown a HTML
        $html = $this->content;
        
        // Convertir headers ## i # a HTML
        $html = preg_replace('/^# (.*$)/m', '<h1 class="text-2xl font-bold text-gray-900 mb-4 mt-6">$1</h1>', $html);
        $html = preg_replace('/^## (.*$)/m', '<h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">$1</h2>', $html);
        $html = preg_replace('/^### (.*$)/m', '<h3 class="text-lg font-bold text-gray-900 mb-2 mt-4">$1</h3>', $html);
        
        // Convertir llistes
        $html = preg_replace('/^- (.*$)/m', '<li class="flex items-start text-gray-700 mb-2">• $1</li>', $html);
        $html = preg_replace('/(<li[^>]*>.*<\/li>)/s', '<ul class="space-y-2 mb-4">$1</ul>', $html);
        
        // Convertir salts de línia dobles a espais
        $html = preg_replace('/\n\n+/', '<br class="mb-4">', $html);
        $html = preg_replace('/\n/', '<br>', $html);
        
        // Convertir text en negreta i cursiva
        $html = preg_replace('/\*\*(.*?)\*\*/', '<strong class="font-semibold">$1</strong>', $html);
        $html = preg_replace('/\*(.*?)\*/', '<em class="italic">$1</em>', $html);
        
        // Convertir codi inline
        $html = preg_replace('/`([^`]+)`/', '<code class="bg-gray-100 px-1 py-0.5 rounded text-sm font-mono">$1</code>', $html);
        
        return $html;
    }

    public function getCommitCount(): int
    {
        return count($this->commits ?? []);
    }

    public function getFeatureCount(): int
    {
        return count($this->features ?? []);
    }

    public function getFixCount(): int
    {
        return count($this->fixes ?? []);
    }

    public function hasBreakingChanges(): bool
    {
        return !empty($this->breaking_changes);
    }

    public function generateSlug(): string
    {
        return strtolower(str_replace(' ', '-', $this->title));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($release) {
            if (empty($release->slug)) {
                $release->slug = $release->generateSlug();
            }
            if (empty($release->created_by)) {
                $release->created_by = auth()->id();
            }
        });

        static::updating(function ($release) {
            if (empty($release->slug)) {
                $release->slug = $release->generateSlug();
            }
            if ($release->isDirty('status') && $release->status === 'published' && !$release->published_at) {
                $release->published_at = now();
                $release->published_by = auth()->id();
            }
        });
    }
}
