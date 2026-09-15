@extends('layouts.app')
@section('title',$project->name)
@section('content')
<div class="page-head">
  <div><div class="project-code">{{ $project->project_code }}</div><h1 class="page-title">{{ $project->name }}</h1><p class="page-subtitle">{{ $project->description ?: 'No project description.' }}</p></div>
  @can('createTask',$project)<a class="btn btn-primary" href="{{ route('tasks.create',$project) }}">+ New Task</a>@endcan
</div>
<x-project-tabs :project="$project" active="overview" />

<div class="grid grid-4 mt-4">
  <div class="card stat"><div class="stat-label">Progress</div><div class="stat-value">{{ number_format($data['progress'],0) }}%</div><div class="mt-2"><x-progress :value="$data['progress']" /></div></div>
  <div class="card stat"><div class="stat-label">Health</div><div class="mt-3"><x-badge :value="$data['health']" /></div></div>
  <div class="card stat"><div class="stat-label">Lifecycle</div><div class="mt-3"><x-badge :value="$project->current_phase" /></div></div>
  @if(!$isGuest)
  <div class="card stat"><div class="stat-label">Team</div><div class="stat-value">{{ $project->memberships->where('status','ACTIVE')->count() }}</div><div class="stat-note">Active project memberships</div></div>
  @else
  <div class="card stat"><div class="stat-label">Access</div><div class="mt-3"><x-badge value="GUEST" /></div><div class="stat-note">External project scope</div></div>
  @endif
</div>

<div class="grid grid-2 mt-4">
<section class="card">
  <div class="section-head"><h2 class="section-title">Task Snapshot</h2><a href="{{ route('projects.view',[$project,'list']) }}" class="muted" style="font-size:13px">Open list</a></div>
  <div class="table-wrap"><table class="table"><thead><tr><th>Task</th><th>Status</th><th>Assignee</th></tr></thead><tbody>
  @forelse($tasks->take(8) as $task)<tr><td><a href="{{ route('tasks.show',$task) }}"><strong>{{ $task->title }}</strong><br><span class="muted">{{ $task->task_code }}</span></a></td><td><x-badge :value="$task->status" /></td><td>{{ $task->assignees->pluck('name')->join(', ') ?: '—' }}</td></tr>
  @empty<tr><td colspan="3"><div class="empty">No tasks yet.</div></td></tr>@endforelse
  </tbody></table></div>
</section>

<section class="card">
  <div class="section-head"><h2 class="section-title">Project Details</h2></div>
  <div class="card-pad"><dl class="kv">
    <dt>Project code</dt><dd>{{ $project->project_code }}</dd>
    <dt>Current phase</dt><dd><x-badge :value="$project->current_phase" /></dd>
    <dt>Visibility</dt><dd>{{ $project->visibility }}</dd>
    <dt>Start date</dt><dd>{{ $project->start_date?->format('d M Y') ?? '—' }}</dd>
    <dt>Target completion</dt><dd>{{ $project->target_completion?->format('d M Y') ?? '—' }}</dd>
    <dt>Internal ID</dt><dd style="font-family:monospace;font-size:12px">{{ $project->id }}</dd>
  </dl></div>
</section>
</div>

<div class="grid grid-2 mt-4">
@if(!$isGuest)
<section class="card">
  <div class="section-head"><h2 class="section-title">Members</h2></div>
  <div class="table-wrap"><table class="table"><thead><tr><th>Name</th><th>Role</th><th>Position</th><th>Mode</th></tr></thead><tbody>
  @foreach($project->memberships as $m)<tr><td><strong>{{ $m->user->name }}</strong><br><span class="muted">{{ $m->project_handle }}</span></td><td><x-badge :value="$m->project_role" /></td><td>{{ $m->position?->name ?? '—' }}</td><td><x-badge :value="$m->permission_mode" /></td></tr>@endforeach
  </tbody></table></div>
</section>
@else
<section class="card"><div class="section-head"><h2 class="section-title">External Access</h2></div><div class="card-pad"><p class="muted">Member directory and internal project organization are hidden for Guest access.</p></div></section>
@endif

@can('update',$project)
<section class="card">
  <div class="section-head"><h2 class="section-title">Lifecycle Transition</h2></div>
  <form class="card-pad" method="POST" action="{{ route('projects.phase.transition',$project) }}">@csrf
    <div class="form-group"><label class="label">Next Phase</label><select class="select" name="next_phase">@foreach(['CREATE','MAINTENANCE','DEVELOPMENT','CLOSED'] as $p)<option value="{{ $p }}">{{ $p }}</option>@endforeach</select><span class="hint">Phase cycles are preserved in project history. Development may occur multiple times.</span></div>
    <div class="form-group mt-3"><label class="label">Active Members in Next Phase</label>
      @foreach($project->memberships->where('status','ACTIVE') as $m)
      <label style="display:flex;gap:8px;align-items:center;font-size:13px"><input type="checkbox" name="active_membership_ids[]" value="{{ $m->id }}" checked> {{ $m->user->name }} — {{ $m->position?->name }}</label>
      @endforeach
      <span class="hint">Project history is preserved even when a member becomes inactive in the next phase.</span>
    </div>
    <button class="btn btn-secondary mt-3">Transition Phase</button>
  </form>
</section>
@endcan
</div>

<section class="card mt-4">
  <div class="section-head"><h2 class="section-title">Lifecycle History</h2></div>
  <div class="table-wrap"><table class="table"><thead><tr><th>Phase</th><th>Cycle</th><th>Started</th><th>Completed</th><th>Status</th></tr></thead><tbody>
  @foreach($project->phaseCycles->sortBy('started_at') as $phase)<tr><td><x-badge :value="$phase->phase_type" /></td><td>#{{ $phase->cycle_number }}</td><td>{{ $phase->started_at?->format('d M Y H:i') }}</td><td>{{ $phase->completed_at?->format('d M Y H:i') ?? '—' }}</td><td>{{ $phase->status }}</td></tr>@endforeach
  </tbody></table></div>
</section>
@endsection
