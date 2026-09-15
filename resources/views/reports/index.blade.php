@extends('layouts.app')
@section('title','Reports')
@section('content')
<div class="page-head"><div><h1 class="page-title">Reports</h1><p class="page-subtitle">Basic V1 project progress, health, overdue, review, and revision reporting.</p></div></div>
<div class="grid grid-4">
 <div class="card stat"><div class="stat-label">Projects</div><div class="stat-value">{{ $rows->count() }}</div></div>
 <div class="card stat"><div class="stat-label">Average Progress</div><div class="stat-value">{{ number_format($rows->avg('progress') ?: 0,0) }}%</div></div>
 <div class="card stat"><div class="stat-label">Overdue Tasks</div><div class="stat-value">{{ $rows->sum('overdue') }}</div></div>
 <div class="card stat"><div class="stat-label">Revision Backlog</div><div class="stat-value">{{ $rows->sum('revision') }}</div></div>
</div>
<section class="card mt-4"><div class="table-wrap"><table class="table"><thead><tr><th>Project</th><th>Lifecycle</th><th>Health</th><th>Progress</th><th>Overdue</th><th>Reviewing</th><th>Revision</th></tr></thead><tbody>
@foreach($rows as $row)<tr><td><a href="{{ route('projects.show',$row['project']) }}"><strong>{{ $row['project']->name }}</strong><br><span class="muted">{{ $row['project']->project_code }}</span></a></td><td><x-badge :value="$row['project']->current_phase" /></td><td><x-badge :value="$row['health']" /></td><td style="min-width:170px">{{ number_format($row['progress'],1) }}%<x-progress :value="$row['progress']" /></td><td>{{ $row['overdue'] }}</td><td>{{ $row['reviewing'] }}</td><td>{{ $row['revision'] }}</td></tr>@endforeach
</tbody></table></div></section>
@endsection
