@extends('layouts.app')
@section('title',$project->name.' · Timeline')
@section('content')
<div class="page-head"><div><div class="project-code">{{ $project->project_code }}</div><h1 class="page-title">{{ $project->name }}</h1><p class="page-subtitle">Timeline representation based on task start and due dates.</p></div></div>
<x-project-tabs :project="$project" active="timeline" />
<section class="card mt-4"><div class="table-wrap"><table class="table"><thead><tr><th>Task</th><th>Start</th><th>Due</th><th>Status</th><th>Timeline</th></tr></thead><tbody>
@forelse($tasks as $task)
@php
$start=$task->start_date?->copy()->startOfDay();
$due=$task->due_date?->copy()->startOfDay();
$days=$start&&$due?max(1,$start->diffInDays($due)):null;
@endphp
<tr><td><a href="{{ route('tasks.show',$task) }}"><strong>{{ $task->title }}</strong></a></td><td>{{ $start?->format('d M Y') ?? '—' }}</td><td>{{ $due?->format('d M Y') ?? '—' }}</td><td><x-badge :value="$task->status" /></td><td style="min-width:220px">@if($days)<div class="progress"><span style="width:{{ min(100,max(8,$days*4)) }}%"></span></div><span class="hint">{{ $days }} days</span>@else<span class="muted">Dates incomplete</span>@endif</td></tr>
@empty<tr><td colspan="5"><div class="empty">No tasks.</div></td></tr>@endforelse
</tbody></table></div></section>
@endsection
