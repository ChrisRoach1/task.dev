<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Task;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('dashboard', [
            'tasks' => auth()->user()->tasks()->get()
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'taskName' => 'required'
        ]);

        $task = Task::create([
            "name" => $request->taskName,
            'user_id' => auth()->id()
        ]);

        return redirect(route('dashboard', absolute: false));

    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'id' => 'required'
        ]);

        $task = Task::query()->where(['id' => $request->id, 'user_id' => Auth::id()])->first();

        if($task == null) {
            throw ValidationException::withMessages(["You can't delete a task that isn't yours"]);
        }

        $task->update([
           'completed'=>true,
           'completed_at'=>now()
        ]);

        TaskCompleted::dispatch("completed");

        return Redirect::to('dashboard');
    }
}
