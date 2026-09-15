@extends('layouts.app')
@section('title','Revisions')
@section('content')
<div class="page-head"><div><h1 class="page-title">Revisions</h1><p class="page-subtitle">Structured revision requests currently assigned to your work.</p></div></div>
<div class="grid grid-2">
@forelse($revisions as $revision)
<section class="card"><div class="section-head"><div><strong>Revision #{{ str_pad($revision->revision_number,2,'0',STR_PAD_LEFT) }}</strong><div class="hint">{{ $revision->task->project->project_code }} · {{ $revision->task->title }}</div></div><x-badge :value="$revision->status" /></div>
<div class="card-pad"><div class="muted">Overall deadline: {{ $revision->overall_deadline?->format('d M Y H:i') ?: '—' }}</div><ul>@foreach($revision->items as $item)<li style="margin:10px 0">{{ $item->description }} — <strong>{{ $item->status->value }}</strong></li>@endforeach</ul><a class="btn btn-secondary btn-sm" href="{{ route('tasks.show',$revision->task) }}">Open Task</a></div></section>
@empty<div class="card empty">No active revisions.</div>@endforelse
</div>
@endsection
