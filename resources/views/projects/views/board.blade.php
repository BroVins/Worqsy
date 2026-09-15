@extends('layouts.app')
@section('title',$project->name.' · Board')
@section('content')
<div class="page-head"><div><div class="project-code">{{ $project->project_code }}</div><h1 class="page-title">{{ $project->name }}</h1></div>@can('createTask',$project)<a class="btn btn-primary" href="{{ route('tasks.create',$project) }}">+ New Task</a>@endcan</div>
<x-project-tabs :project="$project" active="board" />
<div class="board mt-4">
@foreach(['ASSIGNED','IN_PROGRESS','DONE_SUBMITTED','REVIEWING','REVISION','RESUBMITTED','APPROVED'] as $status)
<div class="board-col"><div class="board-title">{{ str_replace('_',' ',$status) }} · {{ $tasks->where('status.value',$status)->count() }}</div>
@foreach($tasks->filter(fn($t)=>$t->status->value===$status) as $task)
<a class="task-card" href="{{ route('tasks.show',$task) }}"><div class="task-title">{{ $task->title }}</div><div class="task-meta">{{ $task->task_code }} · {{ $task->assignees->pluck('name')->join(', ') ?: 'Unassigned' }}</div><div class="task-meta">{{ $task->due_date?->format('d M Y') ?? 'No due date' }}</div></a>
@endforeach
</div>
@endforeach
</div>
@endsection
