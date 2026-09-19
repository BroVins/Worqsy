@extends('layouts.app')

@section('title','Project Grid')

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
        Grid overview for quick project monitoring.
    </p>
</div>


<a href="{{ route('projects.show',$project) }}"
class="btn btn-secondary">
Back to Project
</a>

</div>



<section class="card">


<div class="section-head">

<h2 class="section-title">
Task Overview
</h2>


<div class="muted" style="font-size:13px">
{{ $tasks->count() }} Tasks
</div>

</div>



<div class="grid grid-3 card-pad">


@forelse($tasks as $task)


<a href="{{ route('tasks.show',$task) }}"
class="card project-card">


<div>

<div class="project-name">
{{ $task->title }}
</div>


<div class="muted" style="font-size:13px;margin-top:8px">

{{ $project->project_code }}

</div>

</div>



<div class="meta-row">


<x-badge :value="$task->status"/>


@if($task->due_date)

<span>
📅 {{ $task->due_date->format('d M Y') }}
</span>

@endif


</div>



@if($task->assignees && $task->assignees->count())

<div class="muted" style="font-size:13px">

Assigned:
{{ $task->assignees->first()->name }}

</div>

@endif



</a>


@empty


<div class="empty">
No tasks available.
</div>


@endforelse


</div>


</section>


@endsection
