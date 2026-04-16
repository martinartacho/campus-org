<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'category_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'uploaded_by',
        'is_public',
        'document_date',
        'reference',
        'tags',
        'is_active',
        'download_count',
        'last_accessed_at',
        'teacher_id',
        'course_id',
        'document_type',
        'student_visibility',
        'academic_year',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'document_date' => 'date',
        'download_count' => 'integer',
        'last_accessed_at' => 'datetime',
        'document_type' => 'string',
        'student_visibility' => 'string',
        'academic_year' => 'integer',
    ];

    /**
     * Get the category that owns the document.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    /**
     * Get the user that uploaded the document.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the teacher that owns the document.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the course that owns the document.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(CampusCourse::class, 'course_id');
    }

    /**
     * Get the downloads for the document.
     */
    public function downloads(): HasMany
    {
        return $this->hasMany(DocumentDownload::class);
    }

    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the file icon based on file type.
     */
    public function getFileIconAttribute(): string
    {
        $iconMap = [
            'pdf' => 'bi-file-earmark-pdf',
            'doc' => 'bi-file-earmark-word',
            'docx' => 'bi-file-earmark-word',
            'xls' => 'bi-file-earmark-excel',
            'xlsx' => 'bi-file-earmark-excel',
            'ppt' => 'bi-file-earmark-slides',
            'pptx' => 'bi-file-earmark-slides',
            'jpg' => 'bi-file-earmark-image',
            'jpeg' => 'bi-file-earmark-image',
            'png' => 'bi-file-earmark-image',
            'gif' => 'bi-file-earmark-image',
            'zip' => 'bi-file-earmark-zip',
            'rar' => 'bi-file-earmark-zip',
        ];

        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        
        return $iconMap[$extension] ?? 'bi-file-earmark';
    }

    /**
     * Scope to get documents for a specific teacher.
     */
    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope to get documents by course.
     */
    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope to get documents by document type.
     */
    public function scopeByDocumentType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope to get documents visible to students.
     */
    public function scopeVisibleToStudents($query, $studentId = null)
    {
        if ($studentId) {
            return $query->where(function ($q) use ($studentId) {
                $q->where('student_visibility', 'all')
                  ->orWhere('student_visibility', 'course')
                  ->whereHas('course', function ($courseQuery) use ($studentId) {
                      $courseQuery->whereHas('students', function ($studentQuery) use ($studentId) {
                          $studentQuery->where('user_id', $studentId);
                      });
                  });
            });
        }
        
        return $query->where('student_visibility', 'all');
    }

    /**
     * Scope to get documents by academic year.
     */
    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Check if user has access to this document.
     */
    public function hasAccess($user): bool
    {
        // Si es un documento de profesor, usar lógica específica
        if ($this->teacher_id) {
            // El profesor dueño siempre tiene acceso
            if ($this->teacher_id === $user->id) {
                return true;
            }

            // Si es estudiante, verificar visibilidad
            if ($user->hasRole('student')) {
                return $this->canBeViewedByStudent($user);
            }

            // Si es admin/super-admin, dar acceso
            if ($user->hasAnyRole(['admin', 'super-admin'])) {
                return true;
            }

            // Otros roles no tienen acceso a documentos de profesor
            return false;
        }

        // Documentos del sistema original (no de profesor)
        // If document is public, everyone has access
        if ($this->is_public) {
            return true;
        }

        // Simplificado: acceso basado en categoría
        return $this->category->hasAccess($user);
    }

    
    /**
     * Increment download count and record download.
     */
    public function recordDownload($user, $ipAddress, $userAgent): void
    {
        $this->increment('download_count');
        $this->update(['last_accessed_at' => now()]);

        $this->downloads()->create([
            'user_id' => $user->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'downloaded_at' => now(),
        ]);
    }

    /**
     * Scope to get only active documents.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get documents accessible by user.
     */
    public function scopeAccessibleBy($query, $user)
    {
        if ($user->hasAnyRole(['admin', 'super-admin', 'secretaria'])) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('is_public', true)
              ->orWhereHas('category', function ($categoryQuery) use ($user) {
                  // Solo usuarios con roles de acceso pueden ver documentos no públicos
                  $categoryQuery->whereHas('parent', function ($parentQuery) {
                      $parentQuery->where('name', 'Documentació'); // Solo categorías bajo Documentació
                  });
              });
        });
    }

    /**
     * Scope to search documents.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('tags', 'like', "%{$term}%")
              ->orWhere('reference', 'like', "%{$term}%");
        });
    }

    /**
     * Scope to filter by year.
     */
    public function scopeByYear($query, $year)
    {
        return $query->whereYear('document_date', $year);
    }

    /**
     * Check if document can be viewed by a student.
     */
    public function canBeViewedByStudent($student): bool
    {
        if ($this->student_visibility === 'all') return true;
        
        if ($this->student_visibility === 'course' && $this->course) {
            return $this->course->students()->where('user_id', $student->id)->exists();
        }
        
        return false;
    }

    /**
     * Get document type label.
     */
    public function getDocumentTypeLabelAttribute(): string
    {
        return match($this->document_type) {
            'material' => 'Material',
            'tarea' => 'Tarea',
            'evaluacion' => 'Evaluación',
            'recurso' => 'Recurso',
            default => 'General',
        };
    }

    /**
     * Get student visibility label.
     */
    public function getStudentVisibilityLabelAttribute(): string
    {
        return match($this->student_visibility) {
            'all' => 'Todos',
            'course' => 'Curso',
            'private' => 'Privado',
            default => 'Desconocido',
        };
    }
}
