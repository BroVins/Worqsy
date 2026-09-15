@extends('layouts.app')
@section('title',$project->name.' · Calendar')
@section('content')
<div class="page-head"><div><div class="project-code">{{ $project->project_code }}</div><h1 class="page-title">{{ $project->name }}</h1><p class="page-subtitle">Tasks grouped by due date. This V1 keeps one shared task dataset across every view.</p></div></div>
<x-project-tabs :project="$project" active="calendar" />
@php $groups=$tasks->groupBy(fn($t)=>$t->due_date?->format('Y-m-d') ?? 'No due date'); @endphp
<div class="grid grid-3 mt-4">@foreach($groups as $date=>$items)<section class="card"><div class="section-head"><h2 class="section-title">{{ $date==='No due date'?$date:\Carbon\Carbon::parse($date)->format('D, d M Y') }}</h2></div><div class="card-pad">@foreach($items as $task)<a class="task-card" style="display:block" href="{{ route('tasks.show',$task) }}"><div class="task-title">{{ $task->title }}</div><div class="task-meta"><x-badge :value="$task->status" /> {{ $task->due_date?->format('H:i') }}</div></a>@endforeach</div></section>@endforeach</div>
@endsection
