@extends('layouts.app')
@section('title',$project->name.' · List')
@section('content')
<div class="page-head"><div><div class="project-code">{{ $project->project_code }}</div><h1 class="page-title">{{ $project->name }}</h1></div>@can('createTask',$project)<a class="btn btn-primary" href="{{ route('tasks.create',$project) }}">+ New Task</a>@endcan</div>
<x-project-tabs :project="$project" active="list" />
<section class="card mt-4"><div class="table-wrap"><table class="table"><thead><tr><th>Task</th><th>Assignee</th><th>Status</th><th>Priority</th><th>Weight</th><th>Due</th></tr></thead><tbody>
@forelse($tasks as $task)<tr><td><a href="{{ route('tasks.show',$task) }}"><strong>{{ $task->title }}</strong><br><span class="muted">{{ $task->task_code }}</span></a></td><td>{{ $task->assignees->pluck('name')->join(', ') ?: '—' }}</td><td><x-badge :value="$task->status" /></td><td><x-badge :value="$task->priority" /></td><td>{{ $task->weight }}</td><td>{{ $task->due_date?->format('d M Y H:i') ?? '—' }}</td></tr>@empty<tr><td colspan="6"><div class="empty">No tasks.</div></td></tr>@endforelse
</tbody></table></div></section>
@endsection
