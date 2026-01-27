@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <h5 class="m-0">Projects</h5>
        <form method="post" action="{{ route('projects.store') }}" class="d-flex align-items-center gap-2">
            @csrf
            <input type="text" class="form-control" name="name" placeholder="New project name" required>
            <button type="submit" class="btn btn-primary">Add Project</button>
        </form>
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
        <div class="alert alert-secondary">No projects yet.</div>
    @else
        <div class="list-group">
            @foreach ($projects as $project)
                <div class="list-group-item d-flex align-items-center justify-content-between">
                    <form method="post" action="{{ route('projects.update', $project) }}" class="d-flex align-items-center gap-2">
                        @csrf
                        @method('put')
                        <input type="text" name="name" class="form-control" value="{{ $project->name }}" required>
                        <button class="btn btn-sm btn-outline-success" type="submit">Save</button>
                    </form>
                    <div class="d-flex align-items-center gap-2">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('tasks.index', ['project_id' => $project->id]) }}">Open</a>
                        <form method="post" action="{{ route('projects.destroy', $project) }}">
                            @csrf
                            @method('delete')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

