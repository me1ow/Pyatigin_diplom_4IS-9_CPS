<?php

use App\Http\Controllers\CompetenceController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', [CompetenceController::class, 'index'])->name('home');

Route::get('/competence/{competence:slug}', [CompetenceController::class, 'show'])->name('competence.show');
Route::get('/course/{course}', [CourseController::class, 'show'])->name('course.show');
Route::get('/module/{module}', [ModuleController::class, 'show'])->name('module.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/module/{module}/submit', [SubmissionController::class, 'store'])->name('submission.store');
    Route::get('/my-submissions', [SubmissionController::class, 'index'])->name('submissions.index');
});

require __DIR__.'/auth.php';