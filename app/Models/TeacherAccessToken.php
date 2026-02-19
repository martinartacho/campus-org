<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAccessToken extends Model
{
    protected $fillable = [
        'teacher_id',
        'token',
        'expires_at',
        'used_at',
        'metadata', 
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'metadata' => 'array', 
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    
    // Verificar si ya se completaron los datos básicos
    public function isBasicDataCompleted(): bool
    {
        return isset($this->metadata['basic_data_completed']) && 
               $this->metadata['basic_data_completed'] === true;
    }
    
    // Verificar si ya se completaron los datos de pago
    public function isPaymentDataCompleted(): bool
    {
        return isset($this->metadata['payment_data_completed']) && 
               $this->metadata['payment_data_completed'] === true;
    }
}