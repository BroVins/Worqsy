@extends('layouts.app')

@section('title','Project Board')

@section('content')

<div class="page-head">

<div>
    <div class="muted" style="font-size:13px">
        {{ $project->project_code }}
    </div>

    <h1 class="page-title">
        {{ $project->name }}
    </h1>

    <p class="page-subtitle">
        Visual task management board for project workflow.
    </p>
</div>


<a href="{{ route('projects.show',$project) }}"
class="btn btn-secondary">
Back to Project
</a>

</div>



<div class="board">


@php
$statuses = [
    'TODO' => 'To Do',
    'IN_PROGRESS' => 'In Progress',
    'REVIEWING' => 'Review',
    'REVISION' => 'Revision',
    'APPROVED' => 'Completed'
];
@endphp



@foreach($statuses as $key=>$label)

<div class="board-col">


<div class="board-title">
{{ $label }}
</div>



@foreach($tasks->filter(fn($task)=>$task->status->value === $key) as $task)


<a href="{{ route('tasks.show',$task) }}"
class="task-card">


<div class="task-title">
{{ $task->title }}
</div>


<div class="task-meta">

{{ $task->due_date?->format('d M Y') ?? 'No deadline' }}

</div>


<div style="margin-top:8px">

<x-badge :value="$task->status"/>

</div>


</a>


@endforeach



</div>

@endforeach


</div>


@endsection
