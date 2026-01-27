<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReorderTasksRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::orderBy('name')->get();
        $selectedProjectId = (int) $request->query('project_id', $projects->first()->id ?? 0);

        $tasks = collect();
        if ($selectedProjectId) {
            $tasks = Task::where('project_id', $selectedProjectId)
                ->orderBy('priority')
                ->get();
        }

        return view('tasks.index', [
            'projects' => $projects,
            'selectedProjectId' => $selectedProjectId,
            'tasks' => $tasks,
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $projectId = (int) $validated['project_id'];

        $nextPriority = (int) Task::where('project_id', $projectId)->max('priority');
        $nextPriority = $nextPriority > 0 ? $nextPriority + 1 : 1;

        Task::create([
            'project_id' => $projectId,
            'name' => $validated['name'],
            'priority' => $nextPriority,
        ]);

        return back()->with('status', 'Task created.');
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $validated = $request->validated();
        $originalProjectId = $task->project_id;
        $newProjectId = (int) $validated['project_id'];

        $task->name = $validated['name'];

        if ($newProjectId !== $originalProjectId) {
            // Move to a different project: append to end of new project
            $nextPriority = (int) Task::where('project_id', $newProjectId)->max('priority');
            $nextPriority = $nextPriority > 0 ? $nextPriority + 1 : 1;
            $task->project_id = $newProjectId;
            $task->priority = $nextPriority;
            $task->save();

            $this->renumberPriorities($originalProjectId);
        } else {
            $task->save();
        }

        return back()->with('status', 'Task updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $projectId = $task->project_id;
        $task->delete();
        $this->renumberPriorities($projectId);

        return back()->with('status', 'Task deleted.');
    }

    public function reorder(ReorderTasksRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $projectId = (int) $validated['project_id'];
        $order = array_values($validated['order']);

        // Only update tasks that belong to this project
        $tasks = Task::where('project_id', $projectId)
            ->whereIn('id', $order)
            ->get()
            ->keyBy('id');

        foreach ($order as $index => $taskId) {
            if (isset($tasks[$taskId])) {
                $tasks[$taskId]->update(['priority' => $index + 1]);
            }
        }

        return back()->with('status', 'Tasks reordered.');
    }

    private function renumberPriorities(int $projectId): void
    {
        $tasks = Task::where('project_id', $projectId)
            ->orderBy('priority')
            ->orderBy('id')
            ->get();

        foreach ($tasks as $index => $t) {
            if ($t->priority !== $index + 1) {
                $t->priority = $index + 1;
                $t->save();
            }
        }
    }
}
