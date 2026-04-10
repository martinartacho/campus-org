<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the task that owns the activity.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user that performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the activity description in Catalan.
     */
    public function getDescriptionAttribute(): string
    {
        return match($this->action) {
            'created' => 'ha creat la tasca',
            'updated' => 'ha actualitzat la tasca',
            'assigned' => 'ha assignat la tasca',
            'unassigned' => 'ha desassignat la tasca',
            'status_changed' => 'ha canviat l\'estat',
            'priority_changed' => 'ha canviat la prioritat',
            'due_date_changed' => 'ha canviat la data de venciment',
            'commented' => 'ha afegit un comentari',
            'attachment_added' => 'ha afegit un fitxer',
            'completed' => 'ha completat la tasca',
            'reopened' => 'ha reobert la tasca',
            default => 'ha realitzat una acció',
        };
    }

    /**
     * Get the formatted change description.
     */
    public function getChangeDescriptionAttribute(): string
    {
        if (!$this->old_values && !$this->new_values) {
            return '';
        }

        $changes = [];
        
        if ($this->old_values && $this->new_values) {
            foreach ($this->old_values as $key => $oldValue) {
                if (isset($this->new_values[$key]) && $this->new_values[$key] !== $oldValue) {
                    $changes[] = $this->formatFieldChange($key, $oldValue, $this->new_values[$key]);
                }
            }
        }

        return implode(', ', $changes);
    }

    /**
     * Format a specific field change.
     */
    private function formatFieldChange($field, $oldValue, $newValue): string
    {
        $fieldNames = [
            'status' => 'estat',
            'priority' => 'prioritat',
            'assigned_to' => 'assignat a',
            'due_date' => 'data de venciment',
            'title' => 'títol',
            'description' => 'descripció',
        ];

        $fieldName = $fieldNames[$field] ?? $field;
        
        // Format values based on field type
        $oldValue = $this->formatValue($field, $oldValue);
        $newValue = $this->formatValue($field, $newValue);

        return "{$fieldName}: {$oldValue} → {$newValue}";
    }

    /**
     * Format a value based on field type.
     */
    private function formatValue($field, $value): string
    {
        if ($value === null) return 'cap';
        
        return match($field) {
            'status' => match($value) {
                'pending' => 'pendent',
                'in_progress' => 'en curs',
                'blocked' => 'bloquejat',
                'completed' => 'completat',
                default => $value,
            },
            'priority' => match($value) {
                'low' => 'baixa',
                'medium' => 'mitjana',
                'high' => 'alta',
                'urgent' => 'urgent',
                default => $value,
            },
            'assigned_to' => $value ? User::find($value)?->name ?? 'Usuari desconegut' : 'cap',
            'due_date' => $value ? date('d/m/Y', strtotime($value)) : $value,
            default => $value,
        };
    }
}
