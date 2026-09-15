@extends('layouts.app')
@section('title',$project->name.' · Grid')
@section('content')
<div class="page-head"><div><div class="project-code">{{ $project->project_code }}</div><h1 class="page-title">{{ $project->name }}</h1></div></div>
<x-project-tabs :project="$project" active="grid" />
<div class="grid grid-3 mt-4">@forelse($tasks as $task)<a href="{{ route('tasks.show',$task) }}" class="card project-card"><div class="project-code">{{ $task->task_code }}</div><div class="project-name">{{ $task->title }}</div><div class="meta-row"><x-badge :value="$task->status" /><x-badge :value="$task->priority" /></div><div class="muted" style="font-size:13px">{{ $task->assignees->pluck('name')->join(', ') ?: 'Unassigned' }}</div><div class="task-meta">Due {{ $task->due_date?->format('d M Y H:i') ?? '—' }}</div></a>@empty<div class="card empty">No tasks.</div>@endforelse</div>
@endsection
