<?php

use App\Http\Controllers\Campus\OrdreImportController;
use App\Http\Controllers\Campus\CourseApiController;

Route::prefix('ordres')->name('ordres.')->group(function () {
    Route::get('/import', [OrdreImportController::class, 'index'])->name('import');
    Route::post('/import', [OrdreImportController::class, 'import'])->name('import.store');
    Route::get('/validate', [OrdreImportController::class, 'validateOrdres'])->name('validate');
    Route::post('/auto-match', [OrdreImportController::class, 'autoMatch'])->name('auto-match');
    Route::post('/process', [OrdreImportController::class, 'process'])->name('process');
});

// API per cursos (web, no Flutter)
Route::get('api/courses', [CourseApiController::class, 'index'])->name('courses.api');
Route::get('api/courses/search', [CourseApiController::class, 'search'])->name('courses.search');
