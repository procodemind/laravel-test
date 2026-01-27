@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2">
            <form method="get" action="{{ route('tasks.index') }}" id="projectFilterForm" class="d-flex align-items-center gap-2">
                <label for="project_id" class="form-label m-0">Project:</label>
                <select class="form-select" id="project_id" name="project_id" onchange="document.getElementById('projectFilterForm').submit()">
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @selected($project->id === $selectedProjectId)>{{ $project->name }}</option>
                    @endforeach
                </select>
            </form>
            <a class="btn btn-outline-secondary" href="{{ route('projects.index') }}">Manage Projects</a>
        </div>
        @if($selectedProjectId)
            <form method="post" action="{{ route('tasks.store') }}" class="d-flex align-items-center gap-2">
                @csrf
                <input type="hidden" name="project_id" value="{{ $selectedProjectId }}">
                <input type="text" class="form-control" name="name" placeholder="New task name" required>
                <button type="submit" class="btn btn-primary">Add Task</button>
            </form>
        @endif
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($projects->isEmpty())
        <div class="alert alert-info">
            No projects yet. Create one on the <a href="{{ route('projects.index') }}">Projects</a> page.
        </div>
    @elseif($selectedProjectId && $tasks->isEmpty())
        <div class="alert alert-secondary">No tasks for this project yet.</div>
    @else
        <ul id="task-list" class="list-group">
            @foreach ($tasks as $task)
                <li class="list-group-item d-flex align-items-center justify-content-between" data-id="{{ $task->id }}">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge text-bg-secondary">{{ $task->priority }}</span>
                        <form method="post" action="{{ route('tasks.update', $task) }}" class="d-flex align-items-center gap-2">
                            @csrf
                            @method('put')
                            <input type="hidden" name="project_id" value="{{ $selectedProjectId }}">
                            <input type="text" name="name" class="form-control" value="{{ $task->name }}" required>
                            <button class="btn btn-sm btn-outline-success" type="submit">Save</button>
                        </form>
                    </div>
                    <form method="post" action="{{ route('tasks.destroy', $task) }}">
                        @csrf
                        @method('delete')
                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
@endsection

@section('scripts')
    @if($selectedProjectId && $tasks->isNotEmpty())
    <script>
        const list = document.getElementById('task-list');
        if (list) {
            new Sortable(list, {
                animation: 150,
                onEnd: () => {
                    const order = Array.from(list.querySelectorAll('li')).map(li => parseInt(li.dataset.id, 10));
                    fetch('{{ route('tasks.reorder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.csrfToken,
                        },
                        body: JSON.stringify({
                            project_id: {{ $selectedProjectId }},
                            order: order
                        })
                    }).then(() => {
                        // After reorder, reload to refresh priority badges
                        window.location.reload();
                    });
                }
            });
        }
    </script>
    @endif
@endsection

