<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PushLogController;
use App\Http\Controllers\ProfileController;
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
use App\Http\Controllers\Campus\TeacherController;
use App\Http\Controllers\Campus\CourseRegistrationController;
use App\Http\Controllers\Campus\CampusImportController;
use App\Http\Controllers\Campus\ResourceController;
use App\Http\Controllers\TeacherAccess\TeacherAccessController;
// use App\Http\Controllers\Manager\DashboardController; // Per ara inhabilitat
use App\Http\Controllers\Manager\RegistrationController;
use App\Http\Controllers\Treasury\TeacherTreasuryController;
use App\Http\Controllers\TreasuryController;


use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

// Ruta de prueba absoluta fuera de todo
Route::get('test-import-absoluto', function() {
    return 'RUTA ABSOLUTA FUNCIONA - ' . date('Y-m-d H:i:s');
});

// Rutas de importación (acceso público para pruebas)
Route::get('importar-cursos', [CampusImportController::class, 'create'])
    ->name('importar.cursos');

Route::post('importar-cursos', [CampusImportController::class, 'store'])
    ->name('importar.cursos.store');

Route::post('importar-cursos/validate', [CampusImportController::class, 'validateCSV'])
    ->name('importar.cursos.validate');

Route::get('importar-cursos/template', [CampusImportController::class, 'downloadTemplate'])
    ->name('importar.cursos.template');

Route::post('campus/courses/import', [CampusImportController::class, 'store'])
    ->name('campus.courses.import.store');

Route::get('campus/courses/import/template', [CampusImportController::class, 'downloadTemplate'])
    ->name('campus.courses.import.template');



// Language
Route::post('/set-locale', [LocaleController::class, 'set'])->name('set-locale');
Route::post('/language/resolve-conflict', [LocaleController::class, 'resolveConflict'])
    ->name('language.resolve-conflict');

    
// Rutas públicas
Route::get('/', fn () => view('welcome'));

// Auth
require __DIR__.'/auth.php';


Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

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




Route::middleware(['auth', 'permission:campus.teachers.view'])
    ->prefix('campus/treasury')
    ->name('campus.treasury.')
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
Route::get('teacher-access/success/{token}', [TeacherAccessController::class, 'success'])
    ->name('teacher.access.success');

// ENVIAR MAIL (Treasury)    
Route::middleware(['auth', 'permission:campus.consents.request'])
    ->prefix('campus/treasury')
    ->name('campus.treasury.')
    ->group(function () {

        Route::post(
            'teachers/{teacher}/send-access',
            [\App\Http\Controllers\TeacherAccess\SendTeacherAccessController::class, 'send']
        )->name('teachers.send-access');
    });
// OBRIR ENLLAÇ (sense login)

// Consentiments RGPD
Route::get(
    '/teacher/access/{token}/{purpose}/{courseCode?}',
    [TeacherAccessController::class, 'show']
)->name('teacher.access.form');

Route::get(
    '/teacher/access/{token}/payments',
    [TeacherAccessController::class, 'show']
)->name('teacher.access.payments')
->defaults('purpose', 'payments');

Route::post(
    '/teacher/access/{token}/personal-data',
    [TeacherAccessController::class, 'updatePersonalData']
)->name('teacher.access.personal-data.update');


Route::post(
    '/teacher-access/{token}',
    [TeacherAccessController::class, 'store']
)->name('teacher.access.store');


        
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

// Ruta de prueba simple
Route::get('test-import', function() {
    return 'Importación funciona - ' . date('Y-m-d H:i:s');
});

    // Rutas de importación (acceso público para pruebas)
    Route::get('campus/courses/import', [CampusImportController::class, 'create'])
        ->name('campus.courses.import');

    Route::post('campus/courses/import', [CampusImportController::class, 'store'])
        ->name('campus.courses.import.store');

    Route::get('campus/courses/import/template', [CampusImportController::class, 'downloadTemplate'])
        ->name('campus.courses.import.template');

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
        
        // Cursos (professors)
        Route::get('/teacher-courses', function () {
            return view('campus.teacher-courses');
        })->name('teacher-courses')->middleware('can:campus.my_courses.manage');
        
        // Estudiants (professors)
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
        
        // Course data for AJAX
        Route::get('courses/{course}/data', [CourseController::class, 'getCourseData'])
            ->name('courses.data')
            ->middleware('can:campus.courses.view');
                
        // Check conflicts endpoint
        Route::post('courses/check-conflict', [CourseController::class, 'checkConflict'])
            ->name('courses.check-conflict')
            ->middleware('can:campus.courses.view');
                
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
        
        Route::get('teachers/template', [\App\Http\Controllers\Campus\TeacherController::class, 'template'])
            ->name('teachers.template');
                
        Route::resource('registrations', \App\Http\Controllers\Campus\RegistrationController::class)
            ->middleware('can:campus.registrations.view');

        // Registration Import/Export - Use different prefix to avoid conflicts
        Route::get('registrations-import', [\App\Http\Controllers\Campus\ImportController::class, 'showImportForm'])
            ->name('registrations.import.form');
        Route::post('registrations-import', [\App\Http\Controllers\Campus\ImportController::class, 'import'])
            ->name('registrations.import.store');
        Route::get('registrations-export', [\App\Http\Controllers\Campus\ImportController::class, 'export'])
            ->name('registrations.export');
        Route::get('registrations-list', [\App\Http\Controllers\Campus\ImportController::class, 'index'])
            ->name('registrations.list'); 
            
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
    });

    // Treasury Routes
    Route::middleware(['auth', 'role:treasury'])
    ->prefix('treasury')
    ->name('treasury.')
    ->group(function () {
        
        Route::get('/dashboard', [TreasuryController::class, 'dashboard'])
            ->name('dashboard');
        
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
            ->name('reports.index');
    });

});
