@extends('layouts.app')

@section('title','Task Detail')

@section('content')

<div class="page-head">

    <div>
        <div class="muted" style="font-size:13px">
            {{ $task->project->project_code ?? '' }}
        </div>

        <h1 class="page-title">
            {{ $task->title }}
        </h1>

        <p class="page-subtitle">
            Manage task execution, review workflow, and collaboration activities.
        </p>
    </div>


    @if(isset($task->status))
        <x-badge :value="$task->status"/>
    @endif

</div>



<div style="
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:18px;
margin-bottom:20px;
">


<div class="card" style="padding:20px;">
    <small class="muted">Status</small>
    <h2 style="margin-top:8px;font-size:20px">
        {{ $task->status }}
    </h2>
</div>


<div class="card" style="padding:20px;">
    <small class="muted">Priority</small>
    <h2 style="margin-top:8px;font-size:20px">
        {{ $task->priority ?? '-' }}
    </h2>
</div>


<div class="card" style="padding:20px;">
    <small class="muted">Deadline</small>
    <h2 style="margin-top:8px;font-size:20px">
        {{ $task->due_date?->format('d M Y') ?? '-' }}
    </h2>
</div>


<div class="card" style="padding:20px;">
    <small class="muted">Assignee</small>
    <h2 style="margin-top:8px;font-size:20px">
        {{ $task->assignee->name ?? '-' }}
    </h2>
</div>


</div>



<div style="
display:grid;
grid-template-columns:minmax(0,2fr) minmax(260px,1fr);
gap:20px;
">


<section class="card" style="padding:25px;">

<h2 style="margin-bottom:15px;">
Task Description
</h2>


<p class="muted" style="line-height:1.7;">
    {{ $task->description ?? 'No description available.' }}
</p>



<hr style="margin:25px 0;">



<h2 style="margin-bottom:20px;">
Workflow Progress
</h2>



<div style="display:grid;gap:18px;">


<div>
    <strong>● Task Created</strong>
    <div class="muted">
        Task initialized in workspace
    </div>
</div>


<div>
    <strong>● Assigned to Member</strong>
    <div class="muted">
        Work assigned to responsible user
    </div>
</div>


<div>
    <strong>● Submitted for Review</strong>
    <div class="muted">
        Waiting for reviewer decision
    </div>
</div>


<div>
    <strong>○ Approval / Revision</strong>
    <div class="muted">
        Final verification stage
    </div>
</div>



<div>
    <strong>○ Completed</strong>
    <div class="muted">
        Task successfully completed
    </div>
</div>



</div>


</section>



<section class="card" style="padding:25px;">

<h2 style="margin-bottom:20px;">
Task Actions
</h2>


<div style="display:grid;gap:12px;">


@if(Route::has('tasks.submit'))

<a href="{{ route('tasks.submit',$task) }}"
   class="btn btn-primary">
    Submit Task
</a>

@endif



@if(Route::has('tasks.approve'))

<a href="{{ route('tasks.approve',$task) }}"
   class="btn btn-primary">
    Approve Task
</a>

@endif



@if(Route::has('tasks.revision'))

<a href="{{ route('tasks.revision',$task) }}"
   class="btn btn-secondary">
    Request Revision
</a>

@endif



</div>



<hr style="margin:25px 0;">


<h3>
Task Information
</h3>


<div style="margin-top:15px;font-size:14px;">

<div>
Project:
<strong>
{{ $task->project->name ?? '-' }}
</strong>
</div>


<div style="margin-top:8px;">
Assigned:
<strong>
{{ $task->assignee->name ?? '-' }}
</strong>
</div>


</div>


</section>


</div>




<section class="card" style="padding:25px;margin-top:20px;">


<h2 style="margin-bottom:20px;">
Activity & Discussion
</h2>



<div class="muted">

✓ Task created<br><br>

✓ Member assigned<br><br>

✓ Submission and review history will appear here

</div>


</section>


@endsection
