@extends('layouts.app')
@section('title','My Work')
@section('content')
<div class="page-head">
  <div><h1 class="page-title">My Work</h1><p class="page-subtitle">Your tasks, reviews, and project context across the current workspace.</p></div>
</div>

<div class="grid grid-4">
  <div class="card stat"><div class="stat-label">My Tasks</div><div class="stat-value">{{ $tasks->count() }}</div><div class="stat-note">Active assignments</div></div>
  <div class="card stat"><div class="stat-label">Waiting Review</div><div class="stat-value">{{ $reviews->count() }}</div><div class="stat-note">Requires your attention</div></div>
  <div class="card stat"><div class="stat-label">Projects</div><div class="stat-value">{{ $projects->count() }}</div><div class="stat-note">Current project access</div></div>
  <div class="card stat"><div class="stat-label">Overdue</div><div class="stat-value">{{ $tasks->filter(fn($t)=>$t->due_date && $t->due_date->isPast() && $t->status->value!=='APPROVED')->count() }}</div><div class="stat-note">Needs attention</div></div>
</div>

<div class="grid grid-2 mt-4">
  <section class="card">
    <div class="section-head"><h2 class="section-title">My Tasks</h2><a href="{{ route('projects.index') }}" class="muted" style="font-size:13px">View projects</a></div>
    @if($tasks->isEmpty())<div class="empty">No assigned tasks.</div>@else
    <div class="table-wrap"><table class="table"><thead><tr><th>Task</th><th>Project</th><th>Status</th><th>Due</th></tr></thead><tbody>
      @foreach($tasks as $task)
      <tr><td><a href="{{ route('tasks.show',$task) }}"><strong>{{ $task->title }}</strong><br><span class="muted">{{ $task->task_code }}</span></a></td>
      <td>{{ $task->project->project_code }}</td><td><x-badge :value="$task->status" /></td><td>{{ $task->due_date?->format('d M Y H:i') ?? '—' }}</td></tr>
      @endforeach
    </tbody></table></div>@endif
  </section>

  <section class="card">
    <div class="section-head"><h2 class="section-title">Review Inbox</h2><a href="{{ route('reviews.index') }}" class="muted" style="font-size:13px">Open reviews</a></div>
    @if($reviews->isEmpty())<div class="empty">No work waiting for your review.</div>@else
    <div class="table-wrap"><table class="table"><thead><tr><th>Task</th><th>Project</th><th>Due</th></tr></thead><tbody>
      @foreach($reviews as $task)<tr><td><a href="{{ route('tasks.show',$task) }}"><strong>{{ $task->title }}</strong></a></td><td>{{ $task->project->project_code }}</td><td>{{ $task->due_date?->format('d M') ?? '—' }}</td></tr>@endforeach
    </tbody></table></div>@endif
  </section>
</div>

<section class="card mt-4">
  <div class="section-head"><h2 class="section-title">My Projects</h2></div>
  <div class="grid grid-3 card-pad">
    @forelse($projects as $row)
      <a class="card project-card" href="{{ route('projects.show',$row['project']) }}">
        <div><div class="project-code">{{ $row['project']->project_code }}</div><div class="project-name">{{ $row['project']->name }}</div></div>
        <div class="meta-row"><x-badge :value="$row['project']->current_phase" /><x-badge :value="$row['health']" /></div>
        <div><div class="flex justify-between" style="font-size:12px;margin-bottom:6px"><span class="muted">Progress</span><strong>{{ number_format($row['progress'],0) }}%</strong></div><x-progress :value="$row['progress']" /></div>
      </a>
    @empty <div class="empty">No projects available.</div> @endforelse
  </div>
</section>
@endsection
