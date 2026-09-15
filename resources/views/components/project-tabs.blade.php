@props(['project','active'=>'overview'])
@php
$projectMembership = auth()->check()
    ? $project->memberships()->where('user_id',auth()->id())->where('status','ACTIVE')->first()
    : null;
$isGuest = $projectMembership?->project_role?->value === 'GUEST';
@endphp
<div class="tabs">
  <a class="tab {{ $active==='overview'?'active':'' }}" href="{{ route('projects.show',$project) }}">Overview</a>
  @foreach(['list'=>'List','board'=>'Board','calendar'=>'Calendar','timeline'=>'Timeline','grid'=>'Grid'] as $key=>$label)
    <a class="tab {{ $active===$key?'active':'' }}" href="{{ route('projects.view',[$project,$key]) }}">{{ $label }}</a>
  @endforeach
  <a class="tab {{ $active==='files'?'active':'' }}" href="{{ route('projects.files',$project) }}">Files</a>
  <a class="tab {{ $active==='discussion'?'active':'' }}" href="{{ route('projects.discussion',$project) }}">Discussion</a>
  @unless($isGuest)
  <a class="tab {{ $active==='members'?'active':'' }}" href="{{ route('projects.members',$project) }}">Members</a>
  <a class="tab {{ $active==='activity'?'active':'' }}" href="{{ route('projects.activity',$project) }}">Activity</a>
  @endunless
</div>
