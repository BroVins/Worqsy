@extends('layouts.app')
@section('title','Messages')
@section('content')
<div class="page-head"><div><h1 class="page-title">Messages</h1><p class="page-subtitle">Internal direct messages. External Guests cannot access this area.</p></div></div>
<div class="grid grid-2">
<section class="card"><div class="section-head"><h2 class="section-title">Conversations</h2></div><div class="card-pad">
@forelse($conversations as $c)<a class="task-card" style="display:block" href="{{ route('messages.show',$c) }}"><div class="task-title">{{ $c->members->where('id','!=',auth()->id())->pluck('name')->join(', ') ?: 'Conversation' }}</div><div class="task-meta">{{ optional($c->messages->first())->body ?: 'No messages yet.' }}</div></a>@empty<div class="empty">No conversations.</div>@endforelse
</div></section>
<section class="card"><div class="section-head"><h2 class="section-title">Start Conversation</h2></div><form class="card-pad" method="POST" action="{{ route('messages.start') }}">@csrf<div class="form-group"><label class="label">Internal Member</label><select class="select" name="user_id">@foreach($people as $u)<option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>@endforeach</select></div><button class="btn btn-primary mt-3">Start</button></form></section>
</div>
@endsection
