@extends('layouts.app')
@section('title','Search')
@section('content')
<div class="page-head"><div><h1 class="page-title">Search</h1><p class="page-subtitle">Permission-aware results for “{{ $q }}”.</p></div></div>

<div class="grid grid-2">
<section class="card">
 <div class="section-head"><h2 class="section-title">Projects</h2><span class="hint">{{ $projects->count() }} results</span></div>
 <div class="card-pad">@forelse($projects as $p)<a class="task-card" style="display:block" href="{{ route('projects.show',$p) }}"><div class="task-title">{{ $p->name }}</div><div class="task-meta">{{ $p->project_code }} · {{ $p->current_phase->value }}</div></a>@empty<div class="empty">No projects.</div>@endforelse</div>
</section>
<section class="card">
 <div class="section-head"><h2 class="section-title">Tasks</h2><span class="hint">{{ $tasks->count() }} results</span></div>
 <div class="card-pad">@forelse($tasks as $task)<a class="task-card" style="display:block" href="{{ route('tasks.show',$task) }}"><div class="task-title">{{ $task->title }}</div><div class="task-meta">{{ $task->project->project_code }} · {{ $task->task_code }}</div></a>@empty<div class="empty">No tasks.</div>@endforelse</div>
</section>
</div>

<div class="grid grid-2 mt-4">
@if(!$isGuest)
<section class="card">
 <div class="section-head"><h2 class="section-title">People</h2><span class="hint">{{ $people->count() }} results</span></div>
 <div class="card-pad">@forelse($people as $m)<div class="task-card"><div class="task-title">{{ $m->user->name }}</div><div class="task-meta">{{ $m->company_position ?: $m->organization_role->value }} · {{ $m->user->email }}</div></div>@empty<div class="empty">No people.</div>@endforelse</div>
</section>
@endif
<section class="card">
 <div class="section-head"><h2 class="section-title">Files</h2><span class="hint">{{ $files->count() }} results</span></div>
 <div class="card-pad">@forelse($files as $link)<div class="task-card"><div class="task-title">{{ $link->file->original_name }}</div><div class="task-meta">{{ $link->file->mime_type ?: 'File' }}</div></div>@empty<div class="empty">No files.</div>@endforelse</div>
</section>
</div>
@endsection
