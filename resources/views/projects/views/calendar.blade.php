@extends('layouts.app')

@section('title','Project Calendar')

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
        Track project deadlines and scheduled tasks.
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
        Task Schedule
    </h2>
</div>


<div class="card-pad" style="display:grid;gap:14px">


@forelse($tasks->sortBy('due_date') as $task)

<a href="{{ route('tasks.show',$task) }}"
class="card"
style="padding:16px">


<div style="display:flex;justify-content:space-between;gap:12px">

<div>

<strong>
{{ $task->title }}
</strong>

<div class="muted" style="font-size:13px;margin-top:6px">

{{ $task->due_date?->format('d M Y') ?? 'No deadline' }}

</div>

</div>


<x-badge :value="$task->status"/>


</div>


</a>

@empty

<div class="empty">
No scheduled tasks available.
</div>

@endforelse


</div>

</section>


@endsection
