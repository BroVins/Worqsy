@extends('layouts.app')
@section('title','Assigned Projects')
@section('content')
<div class="page-head"><div><h1 class="page-title">Assigned Projects</h1><p class="page-subtitle">External access is limited to explicitly assigned project resources.</p></div></div>
<div class="grid grid-3">
@forelse($projects as $row)
<a class="card project-card" href="{{ route('projects.show',$row['project']) }}">
 <div><div class="project-code">{{ $row['project']->project_code }}</div><div class="project-name">{{ $row['project']->name }}</div></div>
 <div class="meta-row"><x-badge :value="$row['project']->current_phase" /><x-badge :value="$row['health']" /></div>
 <div><div class="flex justify-between" style="font-size:12px;margin-bottom:6px"><span class="muted">Visible progress</span><strong>{{ number_format($row['progress'],0) }}%</strong></div><x-progress :value="$row['progress']" /></div>
</a>
@empty<div class="card empty">No assigned projects.</div>@endforelse
</div>
@endsection
