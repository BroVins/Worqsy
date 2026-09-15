@extends('layouts.app')
@section('title','Notifications')
@section('content')
<div class="page-head"><div><h1 class="page-title">Notifications</h1><p class="page-subtitle">Assignments, reviews, revisions, approvals, and important project events.</p></div>
<form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="btn btn-secondary">Mark all read</button></form></div>
<section class="card">
<div class="table-wrap"><table class="table"><thead><tr><th>Status</th><th>Notification</th><th>Time</th><th></th></tr></thead><tbody>
@forelse($notifications as $n)<tr><td>{{ $n->read_at ? 'Read' : 'Unread' }}</td><td><strong>{{ $n->data['title'] ?? class_basename($n->type) }}</strong><br><span class="muted">{{ $n->data['body'] ?? '' }}</span></td><td>{{ $n->created_at->diffForHumans() }}</td><td><form method="POST" action="{{ route('notifications.read',$n->id) }}">@csrf<button class="btn btn-secondary btn-sm">Open</button></form></td></tr>
@empty<tr><td colspan="4"><div class="empty">No notifications.</div></td></tr>@endforelse
</tbody></table></div><div class="card-pad">{{ $notifications->links() }}</div></section>
@endsection
