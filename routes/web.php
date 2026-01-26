<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InputController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;

use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Input Routes
    Route::get('/input/{dept}', [InputController::class, 'index'])->name('input.index');
    Route::get('/input/{dept}/create', [InputController::class, 'create'])->name('input.create');
    Route::post('/input/{dept}', [InputController::class, 'store'])->name('input.store');
    Route::get('/input/{dept}/{date}', [InputController::class, 'show'])->name('input.show');

    // Kanban Routes
    Route::get('/kanban/{dept}', [KanbanController::class, 'index'])->name('kanban.index');
    Route::post('/kanban/move', [KanbanController::class, 'move'])->name('kanban.move');
    Route::post('/kanban/reorder', [KanbanController::class, 'reorder'])->name('kanban.reorder');
    
    // Report Routes
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/export/{type}', [ReportController::class, 'export'])->name('report.export');
});
