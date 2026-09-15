@extends('layouts.app')
@section('title','Create Task')
@section('content')
<div class="page-head"><div><div class="project-code">{{ $project->project_code }}</div><h1 class="page-title">Create Task</h1><p class="page-subtitle">{{ $project->name }}</p></div></div>
<form method="POST" action="{{ route('tasks.store',$project) }}" class="card card-pad">@csrf
<div class="form-grid">
  <div class="form-group full"><label class="label">Task Title</label><input class="input" name="title" value="{{ old('title') }}" required></div>
  <div class="form-group full"><label class="label">Description</label><textarea class="textarea" name="description">{{ old('description') }}</textarea></div>
  <div class="form-group"><label class="label">Priority</label><select class="select" name="priority">@foreach(['LOW','MEDIUM','HIGH','CRITICAL'] as $v)<option value="{{ $v }}" @selected(old('priority','MEDIUM')===$v)>{{ $v }}</option>@endforeach</select></div>
  <div class="form-group"><label class="label">Weight</label><select class="select" name="weight">@foreach([1=>'Small',2=>'Medium',3=>'Large',5=>'Major'] as $v=>$label)<option value="{{ $v }}">{{ $v }} — {{ $label }}</option>@endforeach</select></div>
  <div class="form-group"><label class="label">Start Date</label><input class="input" type="date" name="start_date"></div>
  <div class="form-group"><label class="label">Due Date</label><input class="input" type="datetime-local" name="due_date"></div>
  <div class="form-group"><label class="label">Estimate (minutes)</label><input class="input" type="number" min="1" name="estimate_minutes"></div>
  <div class="form-group"><label class="label">External Visibility</label><label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="guest_visible" value="1"> Visible to project Guests</label><span class="hint">Off by default. Guest membership alone does not reveal this task.</span></div>
  <div class="form-group"><label class="label">Assignee</label><select class="select" name="assignee_ids[]" required>@foreach($members as $m)<option value="{{ $m->user_id }}">{{ $m->user->name }} — {{ $m->position?->name }}</option>@endforeach</select></div>
  <div class="form-group"><label class="label">Reviewer</label><select class="select" name="reviewer_ids[]" required>@foreach($members as $m)<option value="{{ $m->user_id }}">{{ $m->user->name }} — {{ $m->position?->name }}</option>@endforeach</select></div>
  <div class="form-group"><label class="label">Approver</label><select class="select" name="approver_ids[]" required>@foreach($members as $m)<option value="{{ $m->user_id }}">{{ $m->user->name }} — {{ $m->position?->name }}</option>@endforeach</select></div>
</div>
<div class="mt-4"><button class="btn btn-primary">Create Task</button> <a class="btn btn-secondary" href="{{ route('projects.show',$project) }}">Cancel</a></div>
</form>
@endsection
