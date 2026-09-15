@extends('layouts.app')
@section('title','Calendar')
@section('content')
<div class="page-head"><div><h1 class="page-title">Calendar</h1><p class="page-subtitle">Upcoming task deadlines across your projects.</p></div></div>
@php $groups=$tasks->groupBy(fn($t)=>$t->due_date->format('Y-m-d')); @endphp
<div class="grid grid-3">
@forelse($groups as $date=>$items)<section class="card"><div class="section-head"><h2 class="section-title">{{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}</h2></div><div class="card-pad">@foreach($items as $task)<a class="task-card" style="display:block" href="{{ route('tasks.show',$task) }}"><div class="task-title">{{ $task->title }}</div><div class="task-meta">{{ $task->project->project_code }} · {{ $task->due_date->format('H:i') }}</div><div class="mt-2"><x-badge :value="$task->status" /></div></a>@endforeach</div></section>
@empty<div class="card empty">No dated tasks.</div>@endforelse
</div>
@endsection
