<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Request;
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
        'notes' => auth()->user()->todaysNotes(),
        'tasksCompleted' => \App\Models\Task::query()->where('completed', true)->get()->count(),
        'totalUsers' => \App\Models\User::all()->count(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/tasks', function () {
    return Inertia::render('Tasks', [
        'tasks' => auth()->user()->tasks()->when(Request::input('view'), function ($query, $view){
            if($view == 'completed'){
                $query->where('completed', true);
            }else if($view == 'inProgress'){
                $query->where('completed', false);
            }
        })->when(Request::input('searchString'), function($query, $searchString){
            $query->where('name', 'like', "%{$searchString}%");
        })->get(),
        'currentView' => Request::input('view'),
        'searchString' => Request::input('searchString'),
    ]);
})->middleware(['auth', 'verified'])->name('tasks');


Route::get('/notes', function () {
    return Inertia::render('Notes', [
        'notes' => auth()->user()->notes()->when(Request::input('dateFilter'), function ($query, $view){
            $query->whereDate('created_at', '=', Request::input('dateFilter'));
        })->when(Request::input('searchString'), function($query, $searchString){
            $query->where('content', 'like', "%{$searchString}%");
        })->get(),
        'dateFilter' => Request::input('dateFilter'),
        'searchString' => Request::input('searchString'),
    ]);
})->middleware(['auth', 'verified'])->name('notes');


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
