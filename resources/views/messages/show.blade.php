@extends('layouts.app')
@section('title','Conversation')
@section('content')
<div class="page-head"><div><h1 class="page-title">{{ $conversation->members->where('id','!=',auth()->id())->pluck('name')->join(', ') ?: 'Conversation' }}</h1><p class="page-subtitle">Internal direct message</p></div><a class="btn btn-secondary" href="{{ route('messages.index') }}">Back</a></div>
<section class="card"><div class="card-pad">
@forelse($conversation->messages as $m)<div style="padding:12px 0;border-bottom:1px solid var(--line)"><div class="flex justify-between"><strong>{{ $m->sender->name }}</strong><span class="hint">{{ $m->created_at->format('d M H:i') }}</span></div><p class="mb-0">{{ $m->body }}</p></div>@empty<div class="empty">No messages yet.</div>@endforelse
<form class="mt-4" method="POST" action="{{ route('messages.send',$conversation) }}">@csrf<div class="form-group"><textarea class="textarea" name="body" placeholder="Write a message..." required></textarea></div><button class="btn btn-primary mt-3">Send</button></form>
</div></section>
@endsection
