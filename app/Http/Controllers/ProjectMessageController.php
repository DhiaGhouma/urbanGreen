<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMessage;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ProjectMessageController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        ProjectMessage::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'message' => $request->message
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Message envoyé avec succès.');
    }

    public function destroy(Project $project, ProjectMessage $message): RedirectResponse
    {
        // Only the message author can delete their message
        if ($message->user_id !== auth()->id()) {
            abort(403);
        }

        $message->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Message supprimé avec succès.');
    }
}
