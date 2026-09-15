@extends('layouts.app')
@section('title','Projects')
@section('content')
<div class="page-head">
  <div><h1 class="page-title">Projects</h1><p class="page-subtitle">Projects you are authorized to access in this workspace.</p></div>
  @if(($workspaceMembership?->organization_role?->value ?? '')==='ADMIN' && ($workspaceMembership?->adminPermission?->create_project))
    <a class="btn btn-primary" href="{{ route('projects.create') }}">+ Create Project</a>
  @endif
</div>
<div class="grid grid-3">
@forelse($projects as $row)
<a class="card project-card" href="{{ route('projects.show',$row['project']) }}">
  <div><div class="project-code">{{ $row['project']->project_code }}</div><div class="project-name">{{ $row['project']->name }}</div></div>
  <div class="meta-row"><x-badge :value="$row['project']->current_phase" /><x-badge :value="$row['health']" /></div>
  <div class="muted" style="font-size:13px;min-height:38px">{{ \Illuminate\Support\Str::limit($row['project']->description,90) }}</div>
  <div><div class="flex justify-between" style="font-size:12px;margin-bottom:6px"><span class="muted">Progress</span><strong>{{ number_format($row['progress'],0) }}%</strong></div><x-progress :value="$row['progress']" /></div>
</a>
@empty <div class="card empty">No projects available.</div> @endforelse
</div>
@endsection
