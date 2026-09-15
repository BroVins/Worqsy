@extends('layouts.app')
@section('title',$project->name.' · Activity')
@section('content')
<div class="page-head"><div><div class="project-code">{{ $project->project_code }}</div><h1 class="page-title">{{ $project->name }}</h1></div></div>
<x-project-tabs :project="$project" active="activity" />
<section class="card mt-4"><div class="table-wrap"><table class="table"><thead><tr><th>Time</th><th>Actor</th><th>Event</th><th>Resource</th></tr></thead><tbody>
@forelse($events as $e)<tr><td>{{ $e->created_at?->format('d M Y H:i') }}</td><td>{{ $e->actor?->name ?: 'System' }}</td><td><strong>{{ $e->event_type }}</strong></td><td>{{ $e->resource_type }}</td></tr>@empty<tr><td colspan="4"><div class="empty">No activity yet.</div></td></tr>@endforelse
</tbody></table></div><div class="card-pad">{{ $events->links() }}</div></section>
@endsection
