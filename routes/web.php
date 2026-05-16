<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompetenceController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\ExpertSubmissionController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Models\Competence;
use App\Models\Document;
use Illuminate\Support\Facades\Gate;

// ========== Публичные маршруты ==========

// Корневая страница (гостевая / лендинг)
Route::get('/', function () {
    $competences = Competence::take(6)->get();

    $sections = [
        'bank'        => 'Банк заданий',
        'regulations' => 'Положения',
        'schedules'   => 'Расписания',
    ];

    $documents = Document::orderBy('title')->get()->groupBy('section');

    return view('welcome', compact('competences', 'sections', 'documents'));
})->name('home');

// ========== Аутентифицированные маршруты ==========

Route::middleware(['auth'])->group(function () {

    // Дашборд → редирект на профиль
    Route::get('/dashboard', function () {
        return redirect()->route('profile.edit');
    })->name('dashboard');

    // Профиль
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Компетенции (просмотр для всех ролей)
    Route::get('/competences', [CompetenceController::class, 'index'])->name('competences.index');
    Route::get('/competences/{competence:slug}', [CompetenceController::class, 'show'])->name('competences.show');

    // CRUD компетенций (admin + expert)
    Route::post('/competences', [CompetenceController::class, 'store'])
        ->middleware('can:competence-manage')
        ->name('competences.store');
    Route::put('/competences/{competence:slug}', [CompetenceController::class, 'update'])
        ->middleware('can:competence-manage')
        ->name('competences.update');
    Route::delete('/competences/{competence:slug}', [CompetenceController::class, 'destroy'])
        ->middleware('can:competence-manage')
        ->name('competences.destroy');

    // Модули (просмотр)
    Route::get('/modules/{module:slug}', [ModuleController::class, 'show'])->name('modules.show');

    // CRUD модулей (admin + expert)
    Route::post('/modules', [ModuleController::class, 'store'])
        ->middleware('can:module-manage')
        ->name('modules.store');
    Route::put('/modules/{module:slug}', [ModuleController::class, 'update'])
        ->middleware('can:module-manage')
        ->name('modules.update');
    Route::delete('/modules/{module:slug}', [ModuleController::class, 'destroy'])
        ->middleware('can:module-manage')
        ->name('modules.destroy');

    // Отправка заданий (user + admin)
    Route::middleware(['can:submission-manage'])->group(function () {
        Route::post('/modules/{module}/submit', [SubmissionController::class, 'store'])->name('submissions.store');
        Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/{submission}/download', [SubmissionController::class, 'download'])->name('submissions.download');
    });

    // Документация (все роли — просмотр, скачивание, открытие PDF)
    Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation.index');
    Route::get('/documentation/{document}/download', [DocumentationController::class, 'download'])
        ->name('documentation.download');
    Route::get('/documentation/{document}/view', [DocumentationController::class, 'view'])
        ->name('documentation.view');

    // Предпросмотр офисных документов (DOCX/XLSX) через Office Web Viewer (все роли)
    Route::get('/documentation/{document}/preview', [DocumentationController::class, 'preview'])
        ->name('documentation.preview');

    // Загрузка и удаление документов (admin + expert)
    Route::post('/documentation', [DocumentationController::class, 'store'])
        ->middleware('can:document-manage')
        ->name('documentation.store');
    Route::delete('/documentation/{document}', [DocumentationController::class, 'destroy'])
        ->middleware('can:document-manage')
        ->name('documentation.destroy');

    // Редирект со старых URL курсов на модули (если есть пересечения)
    Route::get('/courses/recommendations', function () {
        return redirect()->route('competences.index')
            ->with('info', 'Рекомендации теперь доступны в каталоге компетенций.');
    })->name('courses.recommendations');
    Route::get('/courses/{course}', function () {
        return redirect()->route('competences.index')
            ->with('info', 'Страница курса больше не используется. Перейдите к модулям напрямую.');
    })->name('courses.show');
});

// ========== Signed-маршруты (без auth, только временная подпись) ==========

// Отдача файла для Office Web Viewer — доступ по временной signed-ссылке
Route::get('/documentation/serve/{document}', [DocumentationController::class, 'serve'])
    ->name('documentation.serve');

// ========== Маршруты эксперта (expert + admin) ==========

Route::middleware(['auth', 'expert'])->prefix('expert')->name('expert.')->group(function () {
    Route::get('/submissions', [ExpertSubmissionController::class, 'index'])
        ->name('submissions.index');
    Route::put('/submissions/{submission}', [ExpertSubmissionController::class, 'update'])
        ->name('submissions.update');
    Route::get('/submissions/{submission}/download', [SubmissionController::class, 'download'])
        ->name('submissions.download');
});

// ========== Административные маршруты ==========

Route::middleware(['auth', 'admin'])->group(function () {

    // Имперсонация
    Route::post('/impersonate/{user}', [UserController::class, 'impersonate'])
        ->name('impersonate');
    Route::post('/stop-impersonate', [UserController::class, 'stopImpersonate'])
        ->name('stop.impersonate');

    // Управление пользователями (CRUD API)
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::patch('/admin/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('admin.users.role');
    Route::patch('/admin/users/{user}/block', [AdminUserController::class, 'toggleBlock'])->name('admin.users.block');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});

// ========== Аутентификация (Breeze) ==========
require __DIR__.'/auth.php';
