@extends('layouts.app')

@section('title',$project->name)

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
        Manage project workflow, tasks, collaboration, and progress tracking.
    </p>
</div>


<x-badge :value="$project->status"/>

</div>



<div class="grid grid-4">


<div class="card stat">
    <div class="stat-label">Progress</div>
    <div class="stat-value">
        {{ number_format($data['progress'] ?? 0,0) }}%
    </div>
</div>


<div class="card stat">
    <div class="stat-label">Members</div>
    <div class="stat-value">
        {{ $project->memberships->count() }}
    </div>
</div>


<div class="card stat">
    <div class="stat-label">Tasks</div>
    <div class="stat-value">
        {{ $tasks->count() }}
    </div>
</div>


<div class="card stat">
    <div class="stat-label">Health</div>
    <div class="stat-value" style="font-size:18px">
        {{ $data['health'] ?? '-' }}
    </div>
</div>


</div>



<section class="card mt-4">

<div class="section-head">
<h2 class="section-title">
Project Overview
</h2>
</div>


<div class="card-pad">

<p class="muted" style="line-height:1.7">
{{ $project->description ?? 'No project description available.' }}
</p>


<div style="margin-top:25px">

<div style="display:flex;justify-content:space-between;font-size:13px">
<span class="muted">
Project Progress
</span>

<strong>
{{ number_format($data['progress'] ?? 0,0) }}%
</strong>
</div>


<x-progress :value="$data['progress'] ?? 0"/>

</div>


</div>

</section>




<section class="card mt-4">

<div class="section-head">

<h2 class="section-title">
Workspace Views
</h2>

</div>


<div class="card-pad flex gap-3" style="flex-wrap:wrap">

<a class="btn btn-primary"
href="{{ route('projects.view',[$project,'list']) }}">
List
</a>


<a class="btn btn-secondary"
href="{{ route('projects.view',[$project,'board']) }}">
Board
</a>


<a class="btn btn-secondary"
href="{{ route('projects.view',[$project,'calendar']) }}">
Calendar
</a>


<a class="btn btn-secondary"
href="{{ route('projects.view',[$project,'timeline']) }}">
Timeline
</a>


<a class="btn btn-secondary"
href="{{ route('projects.view',[$project,'grid']) }}">
Grid
</a>


</div>

</section>




<section class="card mt-4">

<div class="section-head">
<h2 class="section-title">
Recent Tasks
</h2>
</div>


<div class="card-pad" style="display:grid;gap:12px">


@forelse($tasks->take(5) as $task)

<a href="{{ route('tasks.show',$task) }}"
class="card"
style="padding:15px">

<div style="display:flex;justify-content:space-between">

<strong>
{{ $task->title }}
</strong>

<x-badge :value="$task->status"/>

</div>


<div class="muted" style="margin-top:8px;font-size:13px">
{{ $task->due_date?->format('d M Y') ?? 'No deadline' }}
</div>

</a>


@empty

<div class="empty">
No tasks available.
</div>

@endforelse


</div>

</section>


@endsection
