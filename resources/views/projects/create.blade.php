@extends('layouts.app')
@section('title','Create Project')
@section('content')
<div class="page-head"><div><h1 class="page-title">Create Project</h1><p class="page-subtitle">Start a project in the Create lifecycle phase.</p></div></div>
<form method="POST" action="{{ route('projects.store') }}" class="card card-pad">
@csrf
<div class="form-grid">
  <div class="form-group"><label class="label">Project Name</label><input class="input" name="name" value="{{ old('name') }}" required></div>
  <div class="form-group"><label class="label">Project Code</label><input class="input" name="project_code" value="{{ old('project_code') }}" placeholder="WEB201" required><span class="hint">Human-readable identifier, not a security credential.</span></div>
  <div class="form-group"><label class="label">Project Type</label><select class="select" name="project_type">@foreach(['General Project','Software Development','Marketing Campaign','Product Launch','HR / Recruitment','Operations'] as $t)<option>{{ $t }}</option>@endforeach</select></div>
  <div class="form-group"><label class="label">Visibility</label><select class="select" name="visibility"><option value="PRIVATE">Private</option><option value="WORKSPACE">Workspace</option></select></div>
  <div class="form-group"><label class="label">Project Manager</label><select class="select" name="project_manager_id" required><option value="">Select...</option>@foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>@endforeach</select></div>
  <div class="form-group"><label class="label">Start Date</label><input class="input" type="date" name="start_date" value="{{ old('start_date',now()->format('Y-m-d')) }}"></div>
  <div class="form-group"><label class="label">Target Completion</label><input class="input" type="date" name="target_completion" value="{{ old('target_completion') }}"></div>
  <div class="form-group full"><label class="label">Description</label><textarea class="textarea" name="description">{{ old('description') }}</textarea></div>
</div>
<div class="mt-4"><button class="btn btn-primary">Create Project</button> <a class="btn btn-secondary" href="{{ route('projects.index') }}">Cancel</a></div>
</form>
@endsection
