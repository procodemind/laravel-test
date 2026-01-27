<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::orderBy('name')->get();
        return view('projects.index', compact('projects'));
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        Project::create($validated);
        return back()->with('status', 'Project created.');
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $validated = $request->validated();
        $project->update($validated);
        return back()->with('status', 'Project updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();
        return back()->with('status', 'Project deleted.');
    }
}
