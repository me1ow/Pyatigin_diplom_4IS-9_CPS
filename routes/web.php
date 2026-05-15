<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompetenceController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminUserController;

// ========== Публичные маршруты ==========

// Корневая страница (гостевая / лендинг)
Route::get('/', function () {
    return view('welcome');
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

    // Компетенции
    Route::get('/competences', [CompetenceController::class, 'index'])->name('competences.index');
    Route::get('/competences/{competence}', [CompetenceController::class, 'show'])->name('competences.show');

    // Рекомендации курсов (должен быть ДО /courses/{course})
    Route::get('/courses/recommendations', [CourseController::class, 'recommendations'])->name('courses.recommendations');
    // Курсы
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

    // Модули
    Route::get('/modules/{module}', [ModuleController::class, 'show'])->name('modules.show');

    // Отправка заданий
    Route::post('/modules/{module}/submit', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
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
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});

// ========== Аутентификация (Breeze) ==========
require __DIR__.'/auth.php';
