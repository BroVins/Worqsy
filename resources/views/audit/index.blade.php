@extends('layouts.app')
@section('title','Audit')
@section('content')
<div class="page-head"><div><h1 class="page-title">Audit Log</h1><p class="page-subtitle">Operational history. This UI intentionally provides no edit or delete action.</p></div></div>
<section class="card"><div class="table-wrap"><table class="table"><thead><tr><th>Time</th><th>Actor</th><th>Event</th><th>Resource</th><th>Identity</th></tr></thead><tbody>
@forelse($events as $e)<tr><td>{{ $e->created_at?->format('d M Y H:i:s') }}</td><td>{{ $e->actor?->name ?? 'System' }}</td><td><strong>{{ $e->event_type }}</strong></td><td>{{ $e->resource_type }}<br><span class="muted" style="font-family:monospace;font-size:11px">{{ $e->resource_id }}</span></td><td>{{ $e->actor_project_identity ?: '—' }}</td></tr>@empty<tr><td colspan="5"><div class="empty">No audit events.</div></td></tr>@endforelse
</tbody></table></div><div class="card-pad">{{ $events->links() }}</div></section>
@endsection
