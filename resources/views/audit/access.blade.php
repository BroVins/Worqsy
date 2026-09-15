@extends('layouts.app')
@section('title','Access Log')
@section('content')
<div class="page-head"><div><h1 class="page-title">Access Log</h1><p class="page-subtitle">Aggregated visibility of who accessed protected resources.</p></div></div>
<section class="card"><div class="table-wrap"><table class="table"><thead><tr><th>User</th><th>Resource</th><th>First Viewed</th><th>Last Viewed</th><th>Count</th></tr></thead><tbody>
@forelse($records as $r)<tr><td><strong>{{ $r->user?->name ?: 'Unknown' }}</strong><br><span class="muted">{{ $r->user?->email }}</span></td><td>{{ $r->resource_type }}<br><span class="muted" style="font-family:monospace;font-size:11px">{{ $r->resource_id }}</span></td><td>{{ $r->first_access_at?->format('d M Y H:i:s') }}</td><td>{{ $r->last_access_at?->format('d M Y H:i:s') }}</td><td>{{ $r->access_count }}</td></tr>
@empty<tr><td colspan="5"><div class="empty">No access records.</div></td></tr>@endforelse
</tbody></table></div><div class="card-pad">{{ $records->links() }}</div></section>
@endsection
