<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class TreasuryData extends Model
{
    protected $fillable = [
        'teacher_id',
        'key',
        'value',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Xifrat només per claus sensibles
    public function setValueAttribute($value)
    {
        if ($this->key === 'bank_account' && $value !== null) {
            $this->attributes['value'] = Crypt::encryptString($value);
            return;
        }

        $this->attributes['value'] = $value;
    }

    public function getValueAttribute($value)
    {
        if ($this->key === 'bank_account' && $value !== null) {
            return Crypt::decryptString($value);
        }

        return $value;
    }
}
