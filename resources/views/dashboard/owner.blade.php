@extends('layouts.app')
@section('title','Company Overview')
@section('content')
@php
$onTrack=$cards->where('health','ON_TRACK')->count();
$atRisk=$cards->where('health','AT_RISK')->count();
$overdue=$cards->where('health','OVERDUE')->count();
$overall=$cards->count()?round($cards->avg('progress'),1):0;
@endphp
<div class="page-head">
  <div><h1 class="page-title">Company Overview</h1><p class="page-subtitle">Executive oversight for {{ $workspace->name }}.</p></div>
</div>
<div class="grid grid-4">
  <div class="card stat"><div class="stat-label">Active Projects</div><div class="stat-value">{{ $projects->where('status','ACTIVE')->count() }}</div></div>
  <div class="card stat"><div class="stat-label">On Track</div><div class="stat-value">{{ $onTrack }}</div></div>
  <div class="card stat"><div class="stat-label">Needs Attention</div><div class="stat-value">{{ $atRisk+$overdue }}</div></div>
  <div class="card stat"><div class="stat-label">Overall Progress</div><div class="stat-value">{{ $overall }}%</div></div>
</div>
<section class="card mt-4">
  <div class="section-head"><h2 class="section-title">Portfolio Health</h2><a href="{{ route('reports.index') }}" class="muted" style="font-size:13px">Open reports</a></div>
  <div class="table-wrap"><table class="table"><thead><tr><th>Project</th><th>Lifecycle</th><th>Health</th><th>Progress</th><th>Target</th></tr></thead><tbody>
  @forelse($cards as $row)<tr>
    <td><a href="{{ route('projects.show',$row['project']) }}"><strong>{{ $row['project']->name }}</strong><br><span class="muted">{{ $row['project']->project_code }}</span></a></td>
    <td><x-badge :value="$row['project']->current_phase" /></td><td><x-badge :value="$row['health']" /></td>
    <td style="min-width:190px"><div class="flex justify-between" style="font-size:12px"><span>{{ number_format($row['progress'],1) }}%</span></div><x-progress :value="$row['progress']" /></td>
    <td>{{ $row['project']->target_completion?->format('d M Y') ?? '—' }}</td>
  </tr>@empty<tr><td colspan="5"><div class="empty">No projects yet.</div></td></tr>@endforelse
  </tbody></table></div>
</section>
@endsection
