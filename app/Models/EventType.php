<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventType extends Model
{
    protected $fillable = ['name', 'color', 'is_default'];
    
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            if ($model->is_default) {
                static::where('id', '!=', $model->id)->update(['is_default' => false]);
            }
        });
    }
}