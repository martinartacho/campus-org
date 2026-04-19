<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PushLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileTeacherController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventTypeController;
use App\Http\Controllers\Admin\EventQuestionController;
use App\Http\Controllers\Admin\EventAnswerController;
use App\Http\Controllers\Admin\EventQuestionTemplateController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Campus\CategoryController;
use App\Http\Controllers\Campus\CourseController;
use App\Http\Controllers\Campus\CourseTeacherController;
use App\Http\Controllers\WebHelpController;
use App\Http\Controllers\ReleaseController;
use App\Http\Controllers\Campus\TeacherController;
use App\Http\Controllers\Campus\CourseRegistrationController;
use App\Http\Controllers\Campus\CampusImportController;
use App\Http\Controllers\Campus\ResourceController;
// use App\Http\Controllers\TeacherAccess\TeacherAccessController; // Temporalment comentat
// use App\Http\Controllers\Manager\DashboardController; // Per ara inhabilitat
use App\Http\Controllers\Manager\RegistrationController;
use App\Http\Controllers\Treasury\TeacherTreasuryController;
use App\Http\Controllers\TreasuryController;
use App\Http\Controllers\WoodComerceController;


use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Route;

// Ruta de prueba absoluta fuera de todo
/* Route::get('test-import-absoluto', function() {
    return 'RUTA ABSOLUTA FUNCIONA - ' . date('Y-m-d H:i:s');
}); */

// Rutas de importación (acceso público para pruebas)
Route::get('importar-cursos', [CampusImportController::class, 'create'])
    ->name('importar.cursos');

Route::post('importar-cursos', [CampusImportController::class, 'store'])
    ->name('importar.cursos.store');

Route::post('importar-cursos/validate', [CampusImportController::class, 'validateCSV'])
    ->name('importar.cursos.validate');

Route::get('importar-cursos/template', [CampusImportController::class, 'downloadTemplate'])
    ->name('importar.cursos.template');

// Rutas duplicadas eliminadas - están definidas dentro del grupo campus



// Language
Route::post('/set-locale', [LocaleController::class, 'set'])->name('set-locale');
Route::post('/language/resolve-conflict', [LocaleController::class, 'resolveConflict'])
    ->name('language.resolve-conflict');

    
// Rutas públicas
Route::get('/', fn () => view('welcome'));

// WoodComerce - Rutas absolutamente primeras (sin ningún middleware)
Route::prefix('campus/courses/woodcomerce')->name('campus.courses.woodcomerce.')->group(function () {
    Route::get('/', function() {
        try {
            $etlService = app('App\Services\WoodComerceETLService');
            $controller = new \App\Http\Controllers\WoodComerceController($etlService);
            
            $request = \Illuminate\Http\Request::create('/campus/courses/woodcomerce', 'GET');
            return $controller->index($request);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    })->name('index');
    
    Route::get('/export', function() {
        try {
            $etlService = app('App\Services\WoodComerceETLService');
            $controller = new \App\Http\Controllers\WoodComerceController($etlService);
            
            $request = \Illuminate\Http\Request::create('/campus/courses/woodcomerce/export', 'GET');
            return $controller->export($request);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    })->name('export');
    
    Route::post('/export-selected', function() {
        try {
            $etlService = app('App\Services\WoodComerceETLService');
            $controller = new \App\Http\Controllers\WoodComerceController($etlService);
            
            $request = \Illuminate\Http\Request::create('/campus/courses/woodcomerce/export-selected', 'POST', request()->all());
            return $controller->exportSelected($request);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    })->name('export-selected');
    
    Route::get('/download/{filename}', function($filename) {
        try {
            $controller = new \App\Http\Controllers\WoodComerceController(app('App\Services\WoodComerceETLService'));
            return $controller->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    })->name('download');
});


// WoodComerce - Rutas fuera del middleware global (antes del auth)
Route::get('/woodcomerce-test', function() {
    try {
        $etlService = app('App\Services\WoodComerceETLService');
        $controller = new \App\Http\Controllers\WoodComerceController($etlService);
        
        $request = \Illuminate\Http\Request::create('/woodcomerce-test', 'GET');
        return $controller->index($request);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
})->name('woodcomerce.test');

Route::get('/simple-woodcomerce', function() {
    try {
        $etlService = app('App\Services\WoodComerceETLService');
        $controller = new \App\Http\Controllers\WoodComerceController($etlService);
        
        $request = \Illuminate\Http\Request::create('/simple-woodcomerce', 'GET');
        return $controller->index($request);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
})->name('simple.woodcomerce');

Route::get('/simple-woodcomerce-export', function() {
    try {
        $etlService = app('App\Services\WoodComerceETLService');
        $controller = new \App\Http\Controllers\WoodComerceController($etlService);
        
        $request = \Illuminate\Http\Request::create('/simple-woodcomerce-export', 'GET');
        return $controller->export($request);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
})->name('simple.woodcomerce.export');

// Auth
require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    Route::get('/dashboard/switch/{role}', [DashboardController::class, 'switchRole'])
        ->name('dashboard.switch.role');

    // Admin Dashboard Widgets - Solo para super-admin, admin, manager
    Route::middleware(['role:super-admin|admin|manager'])->prefix('admin')->name('admin.')->group(function () {
        Route::prefix('dashboard_widgets')->name('dashboard_widgets.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\DashboardWidgetController::class, 'index'])
                ->name('index');
            Route::put('/widgets', [App\Http\Controllers\Admin\DashboardWidgetController::class, 'updateWidgets'])
                ->name('update_widgets');
            Route::put('/quick-actions', [App\Http\Controllers\Admin\DashboardWidgetController::class, 'updateQuickActions'])
                ->name('update_quick_actions');
        });
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->name('dashboard');
    });

/*     Route::prefix('manager')->name('manager.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Manager\DashboardController::class, 'index'])
            ->name('dashboard');
    }); */

    Route::prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Teacher\DashboardController::class, 'index'])
            ->name('dashboard');
    });

    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Student\DashboardController::class, 'index'])
            ->name('dashboard');
    });

        
});

Route::middleware(['auth', 'permission:campus.courses.view'])
->prefix('manager')
->name('manager.')
->group(function () {

    Route::get('/courses', [CourseController::class, 'index'])
        ->name('courses.index');
    Route::get('/registrations', [RegistrationController::class, 'index'])
        ->name('registrations.index');
});

// Rutes de PDFs fora del grup de permisos (només amb auth)
Route::middleware(['auth'])
    ->prefix('campus/teachers')
    ->name('campus.teachers.')
    ->group(function () {
        
        Route::get('pdfs', [ProfileTeacherController::class, 'teachersPdfsPage'])
            ->name('pdfs');
        
        Route::get('{teacher}/pdfs/{filename}', [ProfileTeacherController::class, 'showPdfForAdmin'])
            ->name('pdfs.show');
        
        Route::get('{teacher}/pdf/{filename}', [ProfileTeacherController::class, 'downloadPdfForAdmin'])
            ->name('pdf.download');
        
        Route::delete('{teacher}/pdf/{filename}', [ProfileTeacherController::class, 'deletePdfForAdmin'])
            ->name('pdf.delete');
    });




Route::middleware(['auth', 'permission:campus.teachers.view'])
    ->prefix('campus')
    ->name('campus.')
    ->group(function () {
        
        Route::get('teachers', [TeacherTreasuryController::class, 'index'])
            ->name('teachers.index');
        
        Route::get('teachers/rgpd', [TeacherTreasuryController::class, 'rgpdIndex'])
            ->name('teachers.rgpd.index');
        
        Route::get('teachers/{teacher}', [TeacherTreasuryController::class, 'show'])
            ->name('teachers.show');

        Route::get('teachers/{teacher}/consents', [TeacherTreasuryController::class, 'consentHistory'])
            ->name('teachers.consents');

        Route::get('teachers/{teacher}/payment-pdf/{season}/{course}', [TeacherTreasuryController::class, 'downloadTeacherPaymentPdf'])
            ->name('teachers.payment.pdf');

        Route::post('teachers/{teacher}/consent', [TeacherTreasuryController::class, 'storeConsent'])
            ->name('teachers.consent.store');
        
        // Ruta per admins veure PDFs d'un teacher específic
        Route::get('teachers/{teacher}/pdfs', [ProfileTeacherController::class, 'pdfDownloadPageForAdmin'])
            ->name('admin.teachers.pdfs')
            ->middleware('can:view,teacher');
        
        Route::post('teachers/import', [TeacherTreasuryController::class, 'import'])
            ->name('teachers.import');
        
        Route::get('teachers/template', [\App\Http\Controllers\Campus\TeacherController::class, 'template'])
            ->name('teachers.template');
        
        Route::get(
            'teachers/export/csv',
            [TeacherTreasuryController::class, 'exportCsv']
        )->name('teachers.export.csv');

        Route::post(
            'teachers/{teacher}/consent/pdf',
            [TeacherTreasuryController::class, 'generateConsentPdf']
        )->name('teachers.consent.pdf');            

        Route::get(
            'teachers/export/{format}',
            [TeacherTreasuryController::class, 'export']
            )->whereIn('format', ['csv', 'xlsx'])
        ->name('teachers.export');

      


        });    

  //  Per autocompletar dades perfil treasury teacher
        Route::get(
            'teacher/complete-profile/{token}',
            [\App\Http\Controllers\Public\TeacherPublicProfileController::class, 'edit']
        )->name('teacher.public.profile');

        Route::post(
            'teacher/complete-profile/{token}',
            [\App\Http\Controllers\Public\TeacherPublicProfileController::class, 'update']
        )->name('teacher.complete.profile');

        Route::post(
            'teacher/tab-dades-personals/{token}',
            [\App\Http\Controllers\Public\TeacherPublicProfileController::class, 'tabDadesPersonals']
        )->name('teacher.tab.dades.personals');        
        

Route::get(
        'consents/{consent}/download',
        [TeacherTreasuryController::class, 'downloadConsent']
    )->name('consents.download');

Route::get(
        'consents/{consent}/download-payment',
        [TeacherTreasuryController::class, 'downloadPayment']
    )->name('consents.download.payment');

//  ruta per veure el resultat del formulari de success
// Route::get('teacher-access/success/{token}', [TeacherAccessController::class, 'success'])
//     ->name('teacher.access.success'); // Temporalment comentat

// Campus Document Management Routes (con prefix /campus/)
Route::prefix('campus')->name('campus.')->group(function () {
    // Category routes con auth y permisos
    Route::middleware(['auth'])->prefix('documents/categories')->name('documents.categories.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DocumentCategoryController::class, 'index'])
            ->name('index')->middleware('can:documents.categories.index');
        Route::get('/create', [\App\Http\Controllers\DocumentCategoryController::class, 'create'])
            ->name('create')->middleware('can:documents.categories.create');
        Route::post('/', [\App\Http\Controllers\DocumentCategoryController::class, 'store'])
            ->name('store')->middleware('can:documents.categories.create');
        Route::get('/{category}', [\App\Http\Controllers\DocumentCategoryController::class, 'show'])
            ->name('show')->middleware('can:documents.categories.view');
        Route::get('/{category}/edit', [\App\Http\Controllers\DocumentCategoryController::class, 'edit'])
            ->name('edit')->middleware('can:documents.categories.edit');
        Route::put('/{category}', [\App\Http\Controllers\DocumentCategoryController::class, 'update'])
            ->name('update')->middleware('can:documents.categories.edit');
        Route::delete('/{category}', [\App\Http\Controllers\DocumentCategoryController::class, 'destroy'])
            ->name('destroy')->middleware('can:documents.categories.delete');
        Route::put('/{category}/toggle', [\App\Http\Controllers\DocumentCategoryController::class, 'toggle'])
            ->name('toggle')->middleware('can:documents.categories.edit');
    });
    
    // Document routes (con auth)
    Route::middleware(['auth'])->prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DocumentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\DocumentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\DocumentController::class, 'store'])->name('store');
        Route::get('/{document}', [\App\Http\Controllers\DocumentController::class, 'show'])->name('show');
        Route::get('/{document}/edit', [\App\Http\Controllers\DocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}', [\App\Http\Controllers\DocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [\App\Http\Controllers\DocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{document}/download', [\App\Http\Controllers\DocumentController::class, 'download'])->name('download');
        
        // API routes
        Route::get('/categories/{category}/documents', [\App\Http\Controllers\DocumentController::class, 'getCategoryDocuments'])->name('documents.category.documents');
    });
    
    // Secretaria-specific routes (acceso restringido)
    Route::prefix('secretaria')->name('secretaria.')->middleware(['role:secretaria,admin,super-admin'])->group(function () {
        Route::get('/documents', [\App\Http\Controllers\DocumentController::class, 'index'])->name('documents.index');
        Route::get('/documents/create', [\App\Http\Controllers\DocumentController::class, 'create'])->name('documents.create');
    });
});

// Teacher Document Routes
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher'])->group(function () {
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DocumentController::class, 'teacherIndex'])->name('index');
        Route::get('/create', [\App\Http\Controllers\DocumentController::class, 'teacherCreate'])->name('create');
        Route::post('/', [\App\Http\Controllers\DocumentController::class, 'teacherStore'])->name('store');
        Route::get('/{document}', [\App\Http\Controllers\DocumentController::class, 'teacherShow'])->name('show');
        Route::get('/{document}/edit', [\App\Http\Controllers\DocumentController::class, 'teacherEdit'])->name('edit');
        Route::put('/{document}', [\App\Http\Controllers\DocumentController::class, 'teacherUpdate'])->name('update');
        Route::delete('/{document}', [\App\Http\Controllers\DocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{document}/download', [\App\Http\Controllers\DocumentController::class, 'download'])->name('download');
    });
});

// Student Document Routes
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DocumentController::class, 'studentIndex'])->name('index');
        Route::get('/{document}', [\App\Http\Controllers\DocumentController::class, 'studentShow'])->name('show');
        Route::get('/{document}/download', [\App\Http\Controllers\DocumentController::class, 'studentDownload'])->name('download');
    });
});

// ENVIAR MAIL (Treasury)    
Route::middleware(['auth', 'permission:campus.consents.request'])
    ->prefix('campus/treasury')
    ->name('campus.treasury.')
    ->group(function () {

        // Route::post(
//             'teachers/{teacher}/send-access',
//             [\App\Http\Controllers\TeacherAccess\SendTeacherAccessController::class, 'send']
//         )->name('teachers.send-access'); // Temporalment comentat
    });
// OBRIR ENLLAÇ (sense login)
// Consentiments RGPD
// Route::get(
//     '/teacher/access/{token}/{purpose}/{courseCode?}',
//     [TeacherAccessController::class, 'show']
// )->name('teacher.access.form');

// Route::get(
//     '/teacher/access/{token}/payments',
//     [TeacherAccessController::class, 'show']
// )->name('teacher.access.payments')
// ->defaults('purpose', 'payments');

// Route::post(
//     '/teacher/access/{token}/personal-data',
//     [TeacherAccessController::class, 'updatePersonalData']
// )->name('teacher.access.personal-data.update');


// Route::post(
//     '/teacher-access/{token}',
//     [TeacherAccessController::class, 'store']
// )->name('teacher.access.store');


        
//  Rutas protegidas por login y verificación
Route::middleware(['auth', 'verified'])->group(function () {
   
    
    /* Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard'); */

    //  Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //  Rutas Admin (roles, permisos, usuarios)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::middleware('can:users.index')->resource('users', AdminUserController::class);
        Route::middleware('can:roles.index')->resource('roles', RoleController::class);
        Route::middleware('can:permissions.index')->resource('permissions', PermissionController::class);
    });

    //  Configuración del sistema (logo, idioma)
    Route::middleware('can:admin.access')->group(function () {
        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::post('/settings/logo', [SettingsController::class, 'updateLogo'])->name('settings.updateLogo');
        Route::put('/settings/language', [SettingsController::class, 'updateLanguage'])->name('settings.updateLanguage');
        
        // Gestión de backups
        Route::prefix('admin/backups')->name('admin.backups.')->group(function () {
            Route::get('/', [BackupController::class, 'index'])->name('index');
            Route::post('/execute', [BackupController::class, 'execute'])->name('execute');
            Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
            Route::delete('/{filename}', [BackupController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
        Route::put('/settings', [ProfileController::class, 'updateSettings'])->name('settings.update');
        // Otras rutas relacionadas...
    });

    Route::middleware('auth')->group(function () {
    // Nueva ruta para actualizar idioma de usuario
        Route::put('/profile/language', [ProfileController::class, 'updateLanguage'])
            ->name('profile.language.update');
        
        // Nueva ruta para actualizar preferencias de notificación
        Route::put('/profile/notifications', [ProfileController::class, 'updateNotifications'])
            ->name('profile.notifications.update');
        
        // Nuevas rutas para datos bancarios
        Route::put('/profile/banking-data', [ProfileController::class, 'updateBankingData'])
            ->name('profile.banking-data.update');
        Route::post('/profile/banking-data/pdf', [ProfileController::class, 'generateBankingPDF'])
            ->name('profile.banking-data.pdf');
        
        // Ruta para el perfil del profesor (NUEVO - ProfileTeacherController)
        Route::get('/teacher/profile', [ProfileTeacherController::class, 'edit'])
            ->name('teacher.profile');
        Route::put('/teacher/profile', [ProfileTeacherController::class, 'update'])
            ->name('teacher.profile.update');
        Route::post('/teacher/profile/pdf', [ProfileTeacherController::class, 'generatePDF'])
            ->name('teacher.profile.pdf');
        /* Route::get('/teacher/profile/download/{filename}', [ProfileTeacherController::class, 'downloadPDF'])
            ->name('teacher.profile.download'); */
        
        Route::get('/teacher/profile/pdf/{filename}', [ProfileTeacherController::class, 'downloadPDF'])
            ->name('teacher.profile.download');
        
        Route::get('/teacher/profile/pdfs', [ProfileTeacherController::class, 'pdfDownloadPage'])
            ->name('teacher.profile.pdfs');
        
        // Rutas antiguas (mantener por compatibilidad)
        Route::get('/teacher/profile/old', [ProfileController::class, 'teacherEdit'])
            ->name('teacher.profile.old');
        Route::put('/teacher/profile/old', [ProfileController::class, 'teacherUpdate'])
            ->name('teacher.profile.update.old');
        
        // Rutas para PDFs de pago
        Route::post('/profile/payment/pdf', [ProfileController::class, 'generatePaymentPDF'])
            ->name('profile.payment.pdf');
        
        // Ruta segura para descargar PDFs privados
        Route::get('/pdfs/download/{path}', [ProfileController::class, 'downloadPDF'])
            ->name('pdfs.download')
            ->middleware('signed');
});
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
       Route::get('/feedback', [AdminFeedbackController::class, 'index'])->name('admin.feedback.index');
       Route::delete('/feedback/{id}', [AdminFeedbackController::class, 'destroy'])->name('admin.feedback.destroy');
    });

    //  Logs Push relacionados con notificaciones
    Route::prefix('settings/push-logs')->name('push.logs.')->middleware('can:notifications.logs')->group(function () {
        Route::get('/', [PushLogController::class, 'index'])->name('');
        Route::get('/download/{filename}', [PushLogController::class, 'download'])->name('download');
        Route::delete('/delete/{filename}', [PushLogController::class, 'delete'])->name('delete');
    });

    //  Notificaciones (CRUD completo + acciones)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index')->middleware('permission:notifications.view');
        Route::get('/create', [NotificationController::class, 'create'])->name('create')->middleware('permission:notifications.create');
        Route::post('/', [NotificationController::class, 'store'])->name('store')->middleware('permission:notifications.create');

        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show')->middleware('permission:notifications.view');
        Route::get('/{notification}/edit', [NotificationController::class, 'edit'])->name('edit')->middleware('permission:notifications.edit');
        Route::put('/{notification}', [NotificationController::class, 'update'])->name('update')->middleware('permission:notifications.edit');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy')->middleware('permission:notifications.delete');

        Route::post('/{notification}/publish', [NotificationController::class, 'publish'])->name('publish')->middleware('permission:notifications.publish');
        Route::post('/{notification}/send-push', [NotificationController::class, 'sendPush'])->name('send-push')->middleware('permission:notifications.publish');

        Route::post('/mark-as-read/{notification}', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    
        // Estructura para las rutas de envío:
        Route::post('/{notification}/send-email', [NotificationController::class, 'sendEmail'])
            ->name('send-email')
            ->middleware('permission:notifications.publish');
        
        Route::post('/{notification}/send-web', [NotificationController::class, 'sendWeb'])
            ->name('send-web')
            ->middleware('permission:notifications.publish');
        
        Route::post('/{notification}/send-push', [NotificationController::class, 'sendPush'])
            ->name('send-push')
            ->middleware('permission:notifications.publish');
    });


    // API interna para frontend (no REST)
    Route::prefix('api')->group(function () {
        Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    });

    // Rutas administrativas
    Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
        // Rutas para respuestas de eventos
        Route::get('events/{event}/answers/export/{format}', [EventAnswerController::class, 'export'])
            ->name('events.answers.export'); // Nombre: admin.events.answers.export
            
        Route::get('events/{event}/answers/print', [EventAnswerController::class, 'print'])
            ->name('events.answers.print'); // Nombre: admin.events.answers.print    

        // Exportar eventos
        /* Route::get('/events/{event}/export-pdf', [EventController::class, 'exportAnswersToPDF'])
            ->name('events.export.pdf');
        Route::get('/events/{event}/export-excel', [EventController::class, 'exportAnswersToExcel'])
            ->name('events.export.excel'); */

        // Event Types Routes
        Route::resource('event-types', EventTypeController::class)->except(['show']);
        
        // Events Routes
        Route::resource('events', EventController::class);

        // Rutas para preguntas de eventos
        Route::resource('events.questions', EventQuestionController::class)->except(['show']);
        
        // Rutas para respuestas de eventos
        Route::resource('events.answers', EventAnswerController::class);

        // Rutas para plantillas de preguntas 
        Route::resource('event-question-templates', EventQuestionTemplateController::class)->except(['show']);

        Route::get('question-templates/{templateId}/questions', [EventQuestionTemplateController::class, 'getQuestions'])->name('question-templates.questions');

        // API para plantillas
        Route::get('event-question-templates/api/list', [EventQuestionTemplateController::class, 'apiIndex'])
            ->name('event-question-templates.api');
        
          
    });


    // Rutas públicas del calendario
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/event/{event}', [CalendarController::class, 'show'])->name('calendar.event.show');
    Route::get('/calendar/event/{event}/details', [CalendarController::class, 'eventDetails'])->name('calendar.event.details');
    Route::post('/calendar/event/answers', [CalendarController::class, 'saveAnswers'])->name('calendar.event.answers');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    // Rutes del Campus
    Route::prefix('campus')->name('campus.')->middleware(['auth'])->group(function () {
        // Perfil personal
        Route::get('/profile', function () {
            return view('campus.profile');
        })->name('profile')->middleware('can:campus.profile.view');
        
        // Cursos (estudiants)
        Route::get('/my-courses', function () {
            return view('campus.my-courses');
        })->name('my-courses')->middleware('can:campus.my_courses.view');
        
        // Matriculacions (estudiants)
        Route::get('/my-registrations', function () {
            return view('campus.my-registrations');
        })->name('my-registrations')->middleware('can:campus.my_courses.view');
        
        // Rutas específicas para estudiantes
        Route::prefix('student')->name('student.')->middleware(['role:student'])->group(function () {
            Route::get('/profile', function () {
                return view('campus.student.profile');
            })->name('profile');
            
            Route::get('/registrations', function () {
                $user = auth()->user();
                $student = $user->student;
                
                if (!$student) {
                    return view('campus.student.registrations')->with('registrations', collect());
                }
                
                // Get active registrations (courses that haven't ended yet)
                $registrations = \App\Models\CampusRegistration::where('student_id', $student->id)
                    ->where('status', 'confirmed')
                    ->whereHas('course', function($query) {
                        $query->where('end_date', '>=', now());
                    })
                    ->with(['course', 'course.season'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                return view('campus.student.registrations')->with('registrations', $registrations);
            })->name('registrations');
            
            Route::get('/history', function () {
                $user = auth()->user();
                $student = $user->student;
                
                if (!$student) {
                    return view('campus.student.history')->with('registrations', collect());
                }
                
                // Get completed registrations (courses that have ended)
                $registrations = \App\Models\CampusRegistration::where('student_id', $student->id)
                    ->where('status', 'confirmed')
                    ->whereHas('course', function($query) {
                        $query->where('end_date', '<', now());
                    })
                    ->with(['course', 'course.season'])
                    ->orderBy('updated_at', 'desc')
                    ->get();
                
                return view('campus.student.history')->with('registrations', $registrations);
            })->name('history');
        });
        
        // Cursos (professorat)
        Route::get('/teacher-courses', function () {
            return view('campus.teacher-courses');
        })->name('teacher-courses')->middleware('can:campus.my_courses.manage');
        
        // Estudiants (professorat)
        Route::get('/teacher-students', function () {
            return view('campus.teacher-students');
        })->name('teacher-students')->middleware('can:campus.students.view');
        
        // Catàleg de cursos
        Route::get('/catalog', function () {
            return view('campus.catalog');
        })->name('catalog')->middleware('can:campus.courses.view');
        
        // Matricular-se
        Route::get('/enroll', function () {
            return view('campus.enroll');
        })->name('enroll')->middleware('can:campus.courses.enroll');
        
        // CRUD del campus (admin/gestor)
        // Rutas para Categories
        Route::resource('categories', CategoryController::class)
            ->middleware('can:campus.categories.view'); 

        Route::post('categories/{category}/toggle-active', [CategoryController::class, 'toggleActive'])
            ->name('categories.toggleActive')
            ->middleware('can:campus.categories.edit');

        Route::post('categories/{category}/toggle-featured', [CategoryController::class, 'toggleFeatured'])
            ->name('categories.toggleFeatured')
            ->middleware('can:campus.categories.edit');

        // seasons
        Route::resource('seasons', \App\Http\Controllers\Campus\SeasonController::class)
            ->middleware('can:campus.seasons.view');

        Route::post('seasons/{season}/set-as-current', [\App\Http\Controllers\Campus\SeasonController::class, 'setAsCurrent'])
        ->name('seasons.setAsCurrent')
        ->middleware('can:campus.seasons.edit');

        Route::post('seasons/{season}/toggle-active', [\App\Http\Controllers\Campus\SeasonController::class, 'toggleActive'])
            ->name('seasons.toggleActive')
            ->middleware('can:campus.seasons.edit');

        // Courses
        Route::resource('courses', CourseController::class)
            ->middleware('can:campus.courses.view');
        
        // Clear season session
        Route::get('courses/clear-season', [\App\Http\Controllers\Campus\CourseController::class, 'clearSeason'])
            ->name("campus.courses.clear-season")
            ->middleware('can:campus.courses.view');
        
        // Course data for AJAX
        Route::get('courses/{course}/data', [CourseController::class, 'getCourseData'])
            ->name('courses.data')
            ->middleware('can:campus.courses.view');
                
        // Check conflicts endpoint
        Route::post('courses/check-conflict', [CourseController::class, 'checkConflict'])
            ->name('courses.check-conflict')
            ->middleware('can:campus.courses.view');
                
        // Generate course code endpoint
        Route::post('courses/generate-code', [CourseController::class, 'generateCode'])
            ->name('courses.generate-code')
            ->middleware('can:campus.courses.create');
                
        // Teachers assignment
        Route::middleware(['auth'])->group(function () {

            Route::get(
                'courses/{course}/teachers',
                [CourseTeacherController::class, 'index']
            )->name('courses.teachers');

            Route::post(
                'courses/{course}/teachers',
                [CourseTeacherController::class, 'store']
            )->name('courses.teachers.store');

            Route::delete(
                'courses/{course}/teachers/{teacher}',
                [CourseTeacherController::class, 'destroy']
            )->name('courses.teachers.destroy');

            Route::get('courses/{course}/registrations', [CourseRegistrationController::class, 'index'])
        ->name('courses.registrations')
        ->middleware('can:campus.registrations.view');

        });



        Route::resource('students', \App\Http\Controllers\Campus\StudentController::class)
            ->middleware('can:campus.students.view');
        
        Route::resource('teachers', \App\Http\Controllers\Campus\TeacherController::class)
            ->middleware('can:campus.teachers.index');
        
        Route::post('teachers/generate-code', [\App\Http\Controllers\Campus\TeacherController::class, 'generateCode'])
            ->name('campus.teachers.generate-code')
            ->middleware('can:campus.teachers.edit');
        Route::get('teachers/template', [\App\Http\Controllers\Campus\TeacherController::class, 'template'])
            ->name('teachers.template');
                
        Route::resource('registrations', \App\Http\Controllers\Campus\RegistrationController::class)
            ->middleware('can:campus.registrations.view');

        
        Route::post('registrations/{registration}/validate', [\App\Http\Controllers\Campus\RegistrationController::class, 'validateRegistration'])
            ->name('registrations.validate');

        // Registration Import/Export - Use different prefix to avoid conflicts
        Route::get('registrations-import', [\App\Http\Controllers\Campus\RegistrationImportController::class, 'showImportForm'])
            ->name('campus.registrations.import.form');
        Route::post('registrations-import/validate', [\App\Http\Controllers\Campus\RegistrationImportController::class, 'validateImport'])
            ->name('campus.registrations.import.validate');
        Route::post('registrations-import/process', [\App\Http\Controllers\Campus\RegistrationImportController::class, 'processImport'])
            ->name('campus.registrations.import.process');
        
        // Queue Worker Control Routes
        Route::post('registrations/queue/start', [\App\Http\Controllers\Campus\RegistrationImportController::class, 'startQueueWorker'])
            ->name('registrations.queue.start');
        Route::post('registrations/queue/stop', [\App\Http\Controllers\Campus\RegistrationImportController::class, 'stopQueueWorker'])
            ->name('registrations.queue.stop');
        Route::post('registrations/queue/process', [\App\Http\Controllers\Campus\RegistrationImportController::class, 'processQueueNow'])
            ->name('registrations.queue.process');
        Route::get('registrations/queue/status', [\App\Http\Controllers\Campus\RegistrationImportController::class, 'getQueueStatus'])
            ->name('registrations.queue.status');
            
        Route::get('registrations-export', [\App\Http\Controllers\Campus\ImportController::class, 'export'])
            ->name('campus.registrations.export');
        Route::get('registrations-list', [\App\Http\Controllers\Campus\ImportController::class, 'index'])
            ->name('campus.registrations.list'); 
        
        
        // Rutas duplicadas eliminadas - están definidas dentro del grupo campus
            

        // Re-Cursos - Resource Management
        Route::get('resources', [ResourceController::class, 'index'])->name('resources.index');
        Route::get('resources/calendar', [ResourceController::class, 'calendar'])->name('resources.calendar');
        Route::post('resources/assign', [ResourceController::class, 'assign'])->name('resources.assign');
        
        // Spaces CRUD
        Route::get('resources/spaces', [ResourceController::class, 'spaces'])->name('resources.spaces');
        Route::post('resources/spaces', [ResourceController::class, 'storeSpace'])->name('resources.spaces.store');
        Route::get('resources/spaces/{id}/edit', [ResourceController::class, 'editSpace'])->name('resources.spaces.edit');
        Route::put('resources/spaces/{id}', [ResourceController::class, 'updateSpace'])->name('resources.spaces.update');
        Route::delete('resources/spaces/{id}', [ResourceController::class, 'destroySpace'])->name('resources.spaces.destroy');
        
        // TimeSlots CRUD
        Route::get('resources/timeslots', [ResourceController::class, 'timeSlots'])->name('resources.timeslots');
        Route::post('resources/timeslots', [ResourceController::class, 'storeTimeSlot'])->name('resources.timeslots.store');
        Route::get('resources/timeslots/{id}/edit', [ResourceController::class, 'editTimeSlot'])->name('resources.timeslots.edit');
        Route::put('resources/timeslots/{id}', [ResourceController::class, 'updateTimeSlot'])->name('resources.timeslots.update');
        Route::delete('resources/timeslots/{id}', [ResourceController::class, 'destroyTimeSlot'])->name('resources.timeslots.destroy');
        
        Route::get('resources/teachers', [ResourceController::class, 'teachers'])->name('resources.teachers');
        Route::get('resources/getnextcode', [ResourceController::class, 'getNextCode'])->name('resources.getnextcode'); 
            
    });
    
    // Help System Routes - Admin
    Route::middleware(['auth', 'role:admin'])
    ->prefix('campus/help')
    ->name('campus.help.')
    ->group(function () {
        Route::get('/', function() {
            return redirect()->route('campus.help.dashboard');
        });
        
        Route::get('/dashboard', [App\Http\Controllers\Admin\HelpDashboardController::class, 'index'])
            ->name('dashboard');
        
        Route::get('/articles', [App\Http\Controllers\Admin\HelpArticleController::class, 'index'])
            ->name('articles.index');
        Route::get('/articles/create', [App\Http\Controllers\Admin\HelpArticleController::class, 'create'])
            ->name('articles.create');
        Route::post('/articles', [App\Http\Controllers\Admin\HelpArticleController::class, 'store'])
            ->name('articles.store');
        Route::get('/articles/{helpArticle}', [App\Http\Controllers\Admin\HelpArticleController::class, 'show'])
            ->name('articles.show');
        Route::get('/articles/{helpArticle}/edit', [App\Http\Controllers\Admin\HelpArticleController::class, 'edit'])
            ->name('articles.edit');
        Route::put('/articles/{helpArticle}', [App\Http\Controllers\Admin\HelpArticleController::class, 'update'])
            ->name('articles.update');
        Route::delete('/articles/{helpArticle}', [App\Http\Controllers\Admin\HelpArticleController::class, 'destroy'])
            ->name('articles.destroy');
        Route::post('/articles/{helpArticle}/toggle-status', [App\Http\Controllers\Admin\HelpArticleController::class, 'toggleStatus'])
            ->name('articles.toggle-status');
            
        Route::get('/categories', [App\Http\Controllers\Admin\HelpCategoryController::class, 'index'])
            ->name('categories.index');
        Route::get('/categories/create', [App\Http\Controllers\Admin\HelpCategoryController::class, 'create'])
            ->name('categories.create');
        Route::post('/categories', [App\Http\Controllers\Admin\HelpCategoryController::class, 'store'])
            ->name('categories.store');
        Route::get('/categories/{helpCategory}/edit', [App\Http\Controllers\Admin\HelpCategoryController::class, 'edit'])
            ->name('categories.edit');
        Route::put('/categories/{helpCategory}', [App\Http\Controllers\Admin\HelpCategoryController::class, 'update'])
            ->name('categories.update');
        Route::delete('/categories/{helpCategory}', [App\Http\Controllers\Admin\HelpCategoryController::class, 'destroy'])
            ->name('categories.destroy');
        Route::post('/categories/{helpCategory}/toggle-active', [App\Http\Controllers\Admin\HelpCategoryController::class, 'toggleActive'])
            ->name('categories.toggle-active');
    });

    // Release System Routes - Admin
    Route::middleware(['auth', 'role:admin'])
    ->prefix('admin/releases')
    ->name('admin.releases.')
    ->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ReleaseController::class, 'index'])
            ->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\ReleaseController::class, 'create'])
            ->name('create');
        Route::post('/', [App\Http\Controllers\Admin\ReleaseController::class, 'store'])
            ->name('store');
        Route::get('/{release}', [App\Http\Controllers\Admin\ReleaseController::class, 'show'])
            ->name('show');
        Route::get('/{release}/edit', [App\Http\Controllers\Admin\ReleaseController::class, 'edit'])
            ->name('edit');
        Route::put('/{release}', [App\Http\Controllers\Admin\ReleaseController::class, 'update'])
            ->name('update');
        Route::delete('/{release}', [App\Http\Controllers\Admin\ReleaseController::class, 'destroy'])
            ->name('destroy');
        Route::post('/{release}/publish', [App\Http\Controllers\Admin\ReleaseController::class, 'publish'])
            ->name('publish');
        Route::post('/{release}/archive', [App\Http\Controllers\Admin\ReleaseController::class, 'archive'])
            ->name('archive');
    });

    Route::middleware(['auth', 'role:teacher'])
    ->prefix('campus/teacher')
    ->name('campus.teacher.')
    ->group(function () {

        Route::get('/courses', [TeacherController::class, 'courses'])
            ->name('courses.index');

        Route::get('/courses/{course}', [TeacherController::class, 'showCourse'])
            ->name('courses.show');

        Route::get('/courses/{course}/students', [TeacherController::class, 'students'])
            ->name('courses.students');

        Route::get('/courses/{course}/students/export', [TeacherController::class, 'exportStudentsHtml'])
            ->name('courses.students.export');

        // Notificaciones de teacher
        Route::prefix('courses/{course}/notifications')->name('courses.notifications.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Campus\TeacherNotificationController::class, 'index'])
                ->name('index');
            
            Route::get('/create', [\App\Http\Controllers\Campus\TeacherNotificationController::class, 'create'])
                ->name('create');
            
            Route::post('/', [\App\Http\Controllers\Campus\TeacherNotificationController::class, 'store'])
                ->name('store');
            
            Route::get('/{notification}', [\App\Http\Controllers\Campus\TeacherNotificationController::class, 'show'])
                ->name('show');
            
            Route::post('/{notification}/publish', [\App\Http\Controllers\Campus\TeacherNotificationController::class, 'publish'])
                ->name('publish');
            
            Route::post('/{notification}/read', [\App\Http\Controllers\Campus\TeacherNotificationController::class, 'markAsRead'])
                ->name('mark.read');
        });
    });

// ... (rest of the code remains the same)
    Route::middleware(['auth', 'permission:support-requests.view'])
    ->prefix('admin/support-requests')
    ->name('admin.support-requests.')
    ->group(function () {
        
        Route::get('/', [App\Http\Controllers\Admin\SupportRequestController::class, 'index'])
            ->name('index');
            
        Route::get('/{supportRequest}', [App\Http\Controllers\Admin\SupportRequestController::class, 'show'])
            ->name('show');
            
        Route::put('/{supportRequest}/status', [App\Http\Controllers\Admin\SupportRequestController::class, 'updateStatus'])
            ->name('update-status');
            
        Route::delete('/{supportRequest}', [App\Http\Controllers\Admin\SupportRequestController::class, 'destroy'])
            ->name('destroy');
            
        Route::post('/bulk-update', [App\Http\Controllers\Admin\SupportRequestController::class, 'bulkUpdate'])
            ->name('bulk-update');
            
        Route::post('/{supportRequest}/notify', [App\Http\Controllers\Admin\SupportRequestController::class, 'sendNotification'])
            ->name('send-notification');
            
        Route::post('/bulk-delete', [App\Http\Controllers\Admin\SupportRequestController::class, 'bulkDelete'])
            ->name('bulk-delete');
            
        Route::post('/bulk-notify', [App\Http\Controllers\Admin\SupportRequestController::class, 'bulkNotify'])
            ->name('bulk-notify');
    });

    // Treasury Routes
    Route::middleware(['auth', 'role:admin|treasury'])
    ->prefix('treasury')
    ->name('treasury.')
    ->group(function () {
        
        Route::get('/settings/pdf', [SettingsController::class, 'pdfSettings'])
            ->name('settings');
        
        Route::put('/settings/pdf-deadline', [SettingsController::class, 'updatePdfDeadlineTreasury'])
            ->name('settings.updatePdfDeadline');
        
        Route::put('/settings/payment-freeze', [SettingsController::class, 'updatePaymentFreezeTreasury'])
            ->name('settings.updatePaymentFreeze');
        
        Route::get('/consents', [TreasuryController::class, 'consents'])
            ->name('consents');
        
        Route::get('/consents/{consent}', [TreasuryController::class, 'showConsent'])
            ->name('consents.show');
        
        Route::get('/consents/export', [TreasuryController::class, 'exportConsents'])
            ->name('consents.export');
        
        Route::get('/payments', [TreasuryController::class, 'payments'])
            ->name('payments.index');
            
        Route::get('/teachers', [TreasuryController::class, 'teachers'])
            ->name('teachers.index');
            
        Route::get('/reports', [TreasuryController::class, 'reports'])
            ->name('reports');
    });

// Rutas de Ayuda (públicas)
Route::get('help', [WebHelpController::class, 'index'])
    ->name('help.index');

Route::get('help/{slug}', [WebHelpController::class, 'show'])
    ->name('help.show');

Route::post('help/feedback', [WebHelpController::class, 'feedback'])
    ->name('help.feedback');

// Rutas de Release Notes (públiques)
Route::get('releases', [ReleaseController::class, 'index'])
    ->name('releases.index');

Route::get('releases/feed', [ReleaseController::class, 'feed'])
    ->name('releases.feed');

Route::get('releases/latest', [ReleaseController::class, 'latest'])
    ->name('releases.latest');

Route::get('releases/{slug}', [ReleaseController::class, 'show'])
    ->name('releases.show');

// Rutas de Soporte (públicas)
Route::get('support', [SupportController::class, 'create'])
    ->name('support.form');

Route::post('support', [SupportController::class, 'store'])
    ->name('support.store');

// Ruta de prova
// Route::get('/test-controller', [TestController::class, 'index']);
Route::get('/test-simple', function() {
    return 'Simple route works!';
});

// Rutas del sistema de tareas (usant SupportController temporalment)
Route::middleware(['auth'])->prefix('tasques')->name('tasks.')->group(function () {
    Route::get('/', [SupportController::class, 'taskBoardsIndex'])->name('boards.index');
    Route::get('/tauler/{board}', [SupportController::class, 'taskBoardShow'])->name('boards.show');
    Route::get('/crear', [SupportController::class, 'taskBoardCreate'])->name('boards.create');
    Route::post('/crear', [SupportController::class, 'taskBoardStore'])->name('boards.store');
    Route::get('/tauler/{board}/editar', [SupportController::class, 'taskBoardEdit'])->name('boards.edit');
    Route::put('/tauler/{board}', [SupportController::class, 'taskBoardUpdate'])->name('boards.update');
    Route::delete('/tauler/{board}', [SupportController::class, 'taskBoardDestroy'])->name('boards.destroy');
    
    // Dashboard personal de tareas (pendent implementar)
    // Route::get('/meves-tasques', [TaskController::class, 'myTasks'])->name('my-tasks');
    // Route::get('/calendari-tasques', [TaskController::class, 'calendar'])->name('calendar');
});

// Rutas API para tareas (sin middleware de API temporalmente)
Route::middleware(['auth'])->post('/api/tasks', [SupportController::class, 'apiCreateTask']);
Route::middleware(['auth'])->put('/api/tasks/{taskId}/move', [SupportController::class, 'apiMoveTask']);

// Rutas API para usuarios
Route::middleware(['auth'])->get('/api/users/by-role', [SupportController::class, 'apiUsersByRole']);
Route::middleware(['auth'])->get('/api/users/role/{role}', [SupportController::class, 'apiUsersByRoleName']);

// WoodComerce - Rutas fuera del middleware global (copiando lógica de simple-woodcomerce)
Route::prefix('woodcomerce-direct')->name('woodcomerce.direct.')->group(function () {
    Route::get('/', function() {
        try {
            $etlService = app('App\Services\WoodComerceETLService');
            $controller = new \App\Http\Controllers\WoodComerceController($etlService);
            
            $request = \Illuminate\Http\Request::create('/campus/courses/woodcomerce', 'GET');
            return $controller->index($request);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    })->name('index');
    
    Route::get('/export', function() {
        try {
            $etlService = app('App\Services\WoodComerceETLService');
            $controller = new \App\Http\Controllers\WoodComerceController($etlService);
            
            $request = \Illuminate\Http\Request::create('/campus/courses/woodcomerce/export', 'GET');
            return $controller->export($request);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    })->name('export');
});

});
