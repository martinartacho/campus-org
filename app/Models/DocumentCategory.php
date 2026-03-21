<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(DocumentCategory::class, 'parent_id')
            ->orderBy('sort_order');
    }

    /**
     * Get all documents in this category.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'category_id');
    }

    /**
     * Get all documents in this category and its children.
     */
    public function allDocuments(): HasMany
    {
        return Document::whereIn('category_id', $this->getDescendantIds());
    }

    /**
     * Get all descendant category IDs.
     */
    public function getDescendantIds(): array
    {
        $ids = [$this->id];
        
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getDescendantIds());
        }
        
        return $ids;
    }

    /**
     * Check if user has access to this category.
     */
    public function hasAccess($user): bool
    {
        // Simplificado: acceso basado en roles del campus
        return $user->hasAnyRole(['admin', 'super-admin', 'secretaria', 'gestio', 'junta', 'director', 'manager', 'coordinacio']);
    }

    /**
     * Scope to get only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get root categories.
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the category tree as nested array.
     */
    public static function getTree(): array
    {
        return self::with(['children' => function($query) {
            $query->active()->orderBy('sort_order');
        }])
        ->root()
        ->active()
        ->orderBy('sort_order')
        ->get()
        ->toArray();
    }
}
