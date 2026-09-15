@extends('layouts.app')
@section('title','Reviews')
@section('content')
<div class="page-head"><div><h1 class="page-title">Review Inbox</h1><p class="page-subtitle">Centralized work waiting for your review or approval.</p></div></div>
<section class="card">
<div class="table-wrap"><table class="table"><thead><tr><th>Task</th><th>Project</th><th>Assignee</th><th>Priority</th><th>Due</th><th></th></tr></thead><tbody>
@forelse($tasks as $task)<tr><td><strong>{{ $task->title }}</strong><br><span class="muted">{{ $task->task_code }}</span></td><td>{{ $task->project->project_code }}</td><td>{{ $task->assignees->pluck('name')->join(', ') }}</td><td><x-badge :value="$task->priority" /></td><td>{{ $task->due_date?->format('d M Y H:i') ?? '—' }}</td><td><a class="btn btn-secondary btn-sm" href="{{ route('tasks.show',$task) }}">Review</a></td></tr>
@empty<tr><td colspan="6"><div class="empty">Nothing is waiting for your review.</div></td></tr>@endforelse
</tbody></table></div>
</section>
@endsection
