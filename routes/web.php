<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompetenceController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;

// ========== Публичные маршруты ==========

// Корневая страница (гостевая / лендинг)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ========== Аутентифицированные маршруты ==========

Route::middleware(['auth', 'verified'])->group(function () {

    // Дашборд
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Профиль
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Компетенции
    Route::get('/competences', [CompetenceController::class, 'index'])->name('competences.index');
    Route::get('/competences/{competence}', [CompetenceController::class, 'show'])->name('competences.show');

    // Рекомендации курсов (должен быть ДО /courses/{course}, иначе «recommendations» захватится как {course})
    Route::get('/courses/recommendations', [CourseController::class, 'recommendations'])->name('courses.recommendations');
    // Курсы
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

    // Модули
    Route::get('/modules/{module}', [ModuleController::class, 'show'])->name('modules.show');

    // Отправка заданий
    Route::post('/modules/{module}/submit', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
});

// ========== Имперсонация (только для администраторов) ==========

Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/impersonate/{user}', [UserController::class, 'impersonate'])
        ->name('impersonate');
    Route::post('/stop-impersonate', [UserController::class, 'stopImpersonate'])
        ->name('stop.impersonate');
});

// ========== Аутентификация (Breeze) ==========
require __DIR__.'/auth.php';
