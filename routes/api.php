<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FcmTokenController;
use App\Http\Controllers\NotificationController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use App\Models\FcmToken;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\TaskBoardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskListController;
use App\Http\Controllers\TaskChecklistController;
use App\Http\Controllers\Api\CoursesController;

use App\Models\Notification;
use App\Services\FCMService;



// Rutas públicas
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink($request->only('email'));

    return $status === Password::RESET_LINK_SENT
        ? response()->json(['message' => __($status)])
        : response()->json(['message' => __($status)], 422);
});


// Ruras feedback
Route::middleware('auth:api')->prefix('feedback')->group(function () {
    Route::post('/', [FeedbackController::class, 'store']);
});

// Rutas protegidas con token JWT
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
});


// Rutas notifications Firebase
// Notificaciones
Route::middleware('auth:api')->group(function () {
    Route::post('/save-fcm-token', [FcmTokenController::class, 'saveFcmToken']);
    Route::get('/unread-count', [FcmTokenController::class, 'unreadCount']);
    Route::get('/notifications-api', [FcmTokenController::class, 'getNotificationsApi']);
    Route::post('/{id}/mark-read-api', [FcmTokenController::class, 'markAsReadApi']);
});

// Rutas calendar events
Route::middleware('auth:api')->group(function () {
    Route::get('/events', [CalendarController::class, 'index']);           // Próximos 5 eventos visibles
    Route::get('/events/{id}', [CalendarController::class, 'show']);       // Detalle de evento
    Route::post('/events/{id}/answers', [CalendarController::class, 'storeAnswer']); // Responder preguntas
    Route::put('/answers/{id}', [CalendarController::class, 'updateAnswer']);        // Editar respuesta
    Route::delete('/answers/{id}', [CalendarController::class, 'destroyAnswer']);    // Eliminar respuesta
    Route::get('/events/{event}/user-responses', [CalendarController::class, 'getUserResponses']);  //Obtener respuestas

});

// Rutas de ayuda - API pública
Route::prefix('help')->group(function () {
    Route::get('/contextual', [HelpController::class, 'contextual']);
    Route::get('/areas', [HelpController::class, 'areas']);
    Route::get('/search', [HelpController::class, 'search']);
    Route::get('/area/{area}', [HelpController::class, 'byArea']);
    Route::get('/{slug}', [HelpController::class, 'show']);
});

// Rutas del sistema de tareas
Route::middleware('auth:api')->prefix('tasks')->group(function () {
    // Task Boards
    Route::get('/boards', [TaskBoardController::class, 'apiIndex']);
    Route::post('/boards', [TaskBoardController::class, 'store']);
    Route::get('/boards/{board}', [TaskBoardController::class, 'apiShow']);
    Route::put('/boards/{board}', [TaskBoardController::class, 'update']);
    Route::delete('/boards/{board}', [TaskBoardController::class, 'destroy']);
    Route::get('/boards/{board}/statistics', [TaskBoardController::class, 'statistics']);
    
    // Task Lists
    Route::post('/lists', [TaskListController::class, 'store']);
    Route::put('/lists/{list}', [TaskListController::class, 'update']);
    Route::delete('/lists/{list}', [TaskListController::class, 'destroy']);
    Route::put('/boards/{board}/lists/reorder', [TaskListController::class, 'reorder']);
    
    // Tasks
    Route::post('/', [TaskController::class, 'store']);
    Route::put('/{task}', [TaskController::class, 'update']);
    Route::delete('/{task}', [TaskController::class, 'destroy']);
    Route::put('/{task}/move', [TaskController::class, 'move']);
    Route::post('/{task}/comments', [TaskController::class, 'addComment']);
    Route::post('/{task}/attachments', [TaskController::class, 'uploadAttachment']);
    Route::get('/{task}/activities', [TaskController::class, 'activities']);
    
    // Task Checklists
    Route::get('/{task}/checklists', [TaskChecklistController::class, 'index']);
    Route::post('/{task}/checklists', [TaskChecklistController::class, 'store']);
    Route::put('/checklists/{checklist}', [TaskChecklistController::class, 'update']);
    Route::delete('/checklists/{checklist}', [TaskChecklistController::class, 'destroy']);
    Route::put('/checklists/{checklist}/toggle', [TaskChecklistController::class, 'toggle']);
    Route::put('/{task}/checklists/reorder', [TaskChecklistController::class, 'reorder']);
});

// Rutas de descarga de archivos
Route::middleware('auth:api')->get('/tasks/attachments/{attachment}/download', [TaskController::class, 'downloadAttachment']);

