<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'tasks' => auth()->user()->tasks()->where('completed', false)->get(),
        'notes' => auth()->user()->todaysNotes()
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/example', function () {
    return Inertia::render('ExampleDashboard');
})->middleware(['auth', 'verified'])->name('example');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::post('/task', [TaskController::class, 'store'])->name('task.create');
    Route::patch('/task', [TaskController::class, 'update'])->name('task.update');
    Route::delete('/task', [TaskController::class, 'destroy'])->name('task.destroy');
});

Route::middleware('auth')->group(function () {
    Route::patch('/note', [\App\Http\Controllers\NoteController::class, 'update'])->name('note.update');
});


require __DIR__.'/auth.php';
