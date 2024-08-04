<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class NoteController extends Controller
{
    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $note = Note::query()->where('id', $request->id)->first();


        $note->update([
            'content' => $request->get('content')
        ]);

        return redirect(route('dashboard', absolute: false));
    }
}
