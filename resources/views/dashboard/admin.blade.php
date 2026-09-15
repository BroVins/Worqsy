@extends('layouts.app')
@section('title','Admin Overview')
@section('content')
<div class="page-head"><div><h1 class="page-title">Operational Overview</h1><p class="page-subtitle">Organization and project operations for {{ $workspace->name }}.</p></div>
@if($workspaceMembership?->adminPermission?->create_project)<a class="btn btn-primary" href="{{ route('projects.create') }}">+ Create Project</a>@endif</div>
<div class="grid grid-4">
 <div class="card stat"><div class="stat-label">Active Projects</div><div class="stat-value">{{ $projects->where('status','ACTIVE')->count() }}</div></div>
 <div class="card stat"><div class="stat-label">Review Backlog</div><div class="stat-value">{{ $reviewBacklog }}</div></div>
 <div class="card stat"><div class="stat-label">Revision Backlog</div><div class="stat-value">{{ $revisionBacklog }}</div></div>
 <div class="card stat"><div class="stat-label">External Guests</div><div class="stat-value">{{ $guestCount }}</div></div>
</div>
<section class="card mt-4"><div class="section-head"><h2 class="section-title">Project Operations</h2><a href="{{ route('reports.index') }}" class="muted" style="font-size:13px">Reports</a></div>
<div class="table-wrap"><table class="table"><thead><tr><th>Project</th><th>Lifecycle</th><th>Health</th><th>Progress</th><th>Target</th></tr></thead><tbody>
@foreach($cards as $row)<tr><td><a href="{{ route('projects.show',$row['project']) }}"><strong>{{ $row['project']->name }}</strong><br><span class="muted">{{ $row['project']->project_code }}</span></a></td><td><x-badge :value="$row['project']->current_phase" /></td><td><x-badge :value="$row['health']" /></td><td style="min-width:180px">{{ number_format($row['progress'],1) }}%<x-progress :value="$row['progress']" /></td><td>{{ $row['project']->target_completion?->format('d M Y') ?: '—' }}</td></tr>@endforeach
</tbody></table></div></section>
@endsection
