<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampusTeacherPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'course_id',
        'season_id',
        'payment_option',
        'first_name',
        'last_name',
        'fiscal_id',
        'postal_code',
        'city',
        'iban',
        'bank_titular',
        'fiscal_situation',
        'invoice',
        'observacions',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(CampusTeacher::class, 'teacher_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(CampusCourse::class, 'course_id');
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(CampusSeason::class, 'season_id');
    }
    
    // Accesor para el tipo de pago legible
    public function getPaymentOptionTextAttribute(): string
    {
        $options = [
            'own_fee' => 'Faré el cobrament',
            'ceded_fee' => 'Cedeixo la titularitat',
            'waived_fee' => 'Renuncio al cobrament',
        ];
        
        return $options[$this->payment_option] ?? $this->payment_option;
    }
    
    // Accesor para situación fiscal
    public function getFiscalSituationTextAttribute(): string
    {
        $situations = [
            'autonom' => 'Autònom/a',
            'treballador' => 'Treballador/a per compte alié',
            'jubilat' => 'Jubilat/ada',
            'jubilat_especial' => 'Jubilat/ada amb conveni especial',
        ];
        
        return $situations[$this->fiscal_situation] ?? $this->fiscal_situation;
    }
}