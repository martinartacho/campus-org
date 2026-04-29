<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampusNonLectiveDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'description',
        'is_active'
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean'
    ];

    /**
     * Obté dies no lectius per a un rang de dates
     */
    public static function getInRange($startDate, $endDate)
    {
        return self::where('is_active', true)
            ->whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->toArray();
    }

    /**
     * Verifica si una data específica és no lectiva
     */
    public static function isNonLective($date)
    {
        return self::where('date', $date)
            ->where('is_active', true)
            ->exists();
    }
}
