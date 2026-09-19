@extends('layouts.app')

@section('title','Project List')

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
        Detailed task list and project workflow overview.
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
Task List
</h2>


<div class="muted" style="font-size:13px">
{{ $tasks->count() }} Tasks
</div>


</div>




<div class="table-wrap">


<table class="table">

<thead>

<tr>

<th>Task</th>

<th>Assignee</th>

<th>Status</th>

<th>Deadline</th>

</tr>

</thead>


<tbody>


@forelse($tasks as $task)


<tr>


<td>

<a href="{{ route('tasks.show',$task) }}">

<strong>
{{ $task->title }}
</strong>


<div class="muted" style="font-size:12px;margin-top:4px">

{{ $task->task_code ?? '' }}

</div>


</a>

</td>



<td>

@if($task->assignees && $task->assignees->count())

{{ $task->assignees->first()->name }}

@else

<span class="muted">
Unassigned
</span>

@endif

</td>



<td>

<x-badge :value="$task->status"/>

</td>



<td>

{{ $task->due_date?->format('d M Y') ?? 'No deadline' }}

</td>



</tr>


@empty


<tr>

<td colspan="4">

<div class="empty">
No tasks available.
</div>

</td>

</tr>


@endforelse


</tbody>


</table>


</div>


</section>


@endsection
