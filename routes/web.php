<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InputController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\DashboardController;

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
