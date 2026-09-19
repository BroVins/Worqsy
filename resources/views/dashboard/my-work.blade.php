@extends('layouts.app')

@section('title','My Work')

@section('content')

<div class="page-head">

    <div>
        <h1 class="page-title">
            Good afternoon, {{ auth()->user()->name ?? 'User' }} 👋
        </h1>

        <p class="page-subtitle">
            Manage your tasks, projects, reviews, and workspace activities.
        </p>
    </div>

</div>



<div class="grid grid-4">


<div class="card stat">
    <div class="stat-label">Active Tasks</div>
    <div class="stat-value">{{ $tasks->count() }}</div>
    <div class="stat-note">Assigned to you</div>
</div>


<div class="card stat">
    <div class="stat-label">Pending Reviews</div>
    <div class="stat-value">{{ $reviews->count() }}</div>
    <div class="stat-note">Need attention</div>
</div>


<div class="card stat">
    <div class="stat-label">Projects</div>
    <div class="stat-value">{{ $projects->count() }}</div>
    <div class="stat-note">Workspace access</div>
</div>


<div class="card stat">
    <div class="stat-label">Overdue</div>

    <div class="stat-value">
        {{ $tasks->filter(fn($t)=>$t->due_date && $t->due_date->isPast() && $t->status->value!=='APPROVED')->count() }}
    </div>

    <div class="stat-note">
        Require action
    </div>

</div>


</div>




<div class="grid grid-2 mt-4">


<section class="card">

<div class="section-head">
    <h2 class="section-title">
        My Tasks
    </h2>

    <a href="{{ route('projects.index') }}" class="muted">
        View Projects
    </a>
</div>


@if($tasks->isEmpty())

<div class="empty">
    <strong>No active tasks</strong>
    <br>
    Assigned work will appear here.
</div>


@else


<div class="card-pad" style="display:grid;gap:12px">


@foreach($tasks as $task)

<a href="{{ route('tasks.show',$task) }}"
   class="card"
   style="padding:16px;">


<div style="display:flex;justify-content:space-between;gap:10px">

<div>

<strong>
{{ $task->title }}
</strong>

<div class="muted" style="font-size:13px">
{{ $task->project->project_code }}
</div>

</div>


<x-badge :value="$task->status"/>


</div>


<div class="meta-row" style="margin-top:12px">

<span>
📅 {{ $task->due_date?->format('d M Y') ?? 'No deadline' }}
</span>


</div>


</a>


@endforeach


</div>


@endif

</section>





<section class="card">


<div class="section-head">

<h2 class="section-title">
Review Inbox
</h2>


<a href="{{ route('reviews.index') }}" class="muted">
Open Reviews
</a>

</div>



@if($reviews->isEmpty())

<div class="empty">
✓
<br>
<strong>All caught up!</strong>
</div>


@else


<div class="card-pad" style="display:grid;gap:12px">


@foreach($reviews as $task)


<a href="{{ route('tasks.show',$task) }}"
class="card"
style="padding:16px;">


<strong>
{{ $task->title }}
</strong>


<div class="muted" style="margin-top:8px">
{{ $task->project->project_code }}
</div>


</a>


@endforeach


</div>


@endif


</section>


</div>





<section class="card mt-4">


<div class="section-head">

<h2 class="section-title">
My Projects
</h2>

</div>



<div class="grid grid-3 card-pad">


@forelse($projects as $row)


<a href="{{ route('projects.show',$row['project']) }}"
class="card project-card">


<div>

<div class="project-code">
{{ $row['project']->project_code }}
</div>


<div class="project-name">
{{ $row['project']->name }}
</div>

</div>



<div class="meta-row">

<x-badge :value="$row['project']->current_phase"/>

<x-badge :value="$row['health']"/>

</div>



<div>


<div style="display:flex;justify-content:space-between;font-size:13px">

<span class="muted">
Progress
</span>


<strong>
{{ number_format($row['progress'],0) }}%
</strong>


</div>


<x-progress :value="$row['progress']"/>


</div>



</a>


@empty

<div class="empty">
No projects available.
</div>

@endforelse


</div>


</section>


@endsection
