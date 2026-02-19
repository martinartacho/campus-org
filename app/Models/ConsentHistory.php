<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsentHistory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'teacher_id',
        'season',
        'document_path',
        'payment_document_path',
        'accepted_at',
        'checksum',
        'delegated_by_user_id',
        'delegated_reason',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    // Añadir valor por defecto para document_path
    protected $attributes = [
        'document_path' => 'pending',
    ];

    public function teacher()
    {
        return $this->belongsTo(CampusTeacher::class, 'teacher_id');
    }

    public function course()
    {
        return $this->belongsTo(CampusCourse::class, 'course_id');
    }

    public function season()
    {
        return $this->belongsTo(CampusSeason::class, 'season_id');
    }

    public function delegatedBy()
    {
        return $this->belongsTo(User::class, 'delegated_by_user_id');
    }
}