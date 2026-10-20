<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\CriterionController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Login & Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('departments', DepartmentController::class);
    Route::resource('criteria', CriterionController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('hods', \App\Http\Controllers\HodController::class);
    Route::resource('evaluations', EvaluationController::class);
    Route::post('/evaluations/{evaluation}/validate', [EvaluationController::class, 'validateStatus'])->name('evaluations.validate');

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    Route::get('/calculate/{period}', [EvaluationController::class, 'calculate'])->name('calculate');
});
