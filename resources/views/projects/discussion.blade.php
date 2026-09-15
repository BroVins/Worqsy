@extends('layouts.app')
@section('title',$project->name.' · Discussion')
@section('content')
<div class="page-head"><div><div class="project-code">{{ $project->project_code }}</div><h1 class="page-title">{{ $project->name }}</h1></div></div>
<x-project-tabs :project="$project" active="discussion" />
<div class="grid grid-2 mt-4">
<section class="card">
 <div class="section-head"><h2 class="section-title">Project Discussion</h2></div>
 <div class="card-pad">
 @forelse($messages as $message)
  <div style="padding:14px 0;border-bottom:1px solid var(--line)">
   <div class="flex justify-between"><strong>{{ $message->user->name }}</strong><span class="hint">{{ $message->created_at->diffForHumans() }}</span></div>
   <p class="mb-0" style="line-height:1.6">{{ $message->body }}</p>
  </div>
 @empty <div class="empty">No project updates yet.</div> @endforelse
 <div class="mt-3">{{ $messages->links() }}</div>
 </div>
</section>
<section class="card">
 <div class="section-head"><h2 class="section-title">Post Update</h2></div>
 <form class="card-pad" method="POST" action="{{ route('projects.discussion.store',$project) }}">@csrf
   <div class="form-group"><label class="label">Message</label><textarea class="textarea" name="body" required></textarea></div>
   <button class="btn btn-primary mt-3">Post</button>
 </form>
</section>
</div>
@endsection
