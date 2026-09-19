@extends('layouts.app')

@section('title','Project Timeline')

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
        Timeline view for monitoring project execution stages.
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
Project Timeline
</h2>

</div>



<div class="card-pad" style="display:grid;gap:20px">



@forelse($tasks as $task)


<div class="card" style="padding:18px">


<div style="display:flex;justify-content:space-between;gap:15px">

<div>

<a href="{{ route('tasks.show',$task) }}">

<strong>
{{ $task->title }}
</strong>

</a>


<div class="muted" style="font-size:13px;margin-top:6px">

Start:
{{ $task->created_at?->format('d M Y') ?? '-' }}

&nbsp; | &nbsp;

Due:
{{ $task->due_date?->format('d M Y') ?? '-' }}

</div>

</div>


<x-badge :value="$task->status"/>


</div>



<div style="margin-top:14px">

<div style="
height:10px;
background:#eef0f4;
border-radius:999px;
overflow:hidden;
">


<div style="
height:100%;
width:{{ $task->status->value === 'APPROVED' ? 100 : 50 }}%;
background:#405cf5;
border-radius:999px;
">
</div>


</div>


</div>



</div>


@empty


<div class="empty">
No timeline data available.
</div>


@endforelse



</div>


</section>


@endsection
