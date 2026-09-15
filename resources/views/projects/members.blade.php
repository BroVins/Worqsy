@extends('layouts.app')
@section('title',$project->name.' · Members')
@section('content')
<div class="page-head"><div><div class="project-code">{{ $project->project_code }}</div><h1 class="page-title">{{ $project->name }}</h1></div></div>
<x-project-tabs :project="$project" active="members" />
<section class="card mt-4">
 <div class="table-wrap"><table class="table"><thead><tr><th>Name</th><th>Position</th><th>Role</th><th>Identity</th><th>Permission</th><th>Status</th><th></th></tr></thead><tbody>
 @foreach($project->memberships as $m)<tr><td><strong>{{ $m->user->name }}</strong><br><span class="muted">{{ $m->user->email }}</span></td><td>{{ $m->position?->name ?: '—' }}</td><td><x-badge :value="$m->project_role" /></td><td>{{ $m->project_handle }}</td><td><x-badge :value="$m->permission_mode" /></td><td><x-badge :value="$m->status" /></td><td>
@can('update',$project)
  @if($m->status->value==='ACTIVE')
  <form method="POST" action="{{ route('projects.members.revoke',[$project,$m]) }}">@csrf<button class="btn btn-danger btn-sm" data-confirm="Revoke this project access?">Revoke</button></form>
  @else
  <form method="POST" action="{{ route('projects.members.reactivate',[$project,$m]) }}">@csrf<button class="btn btn-secondary btn-sm">Reactivate</button></form>
  @endif
@endcan
</td></tr>@endforeach
 </tbody></table></div>
</section>
@can('update',$project)
<section class="card mt-4">
 <div class="section-head"><h2 class="section-title">Add / Update Project Member</h2></div>
 <form class="card-pad" method="POST" action="{{ route('projects.members.store',$project) }}">@csrf
  <div class="form-grid">
   <div class="form-group"><label class="label">User</label><select class="select" name="user_id" required>@foreach($available as $u)<option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>@endforeach</select></div>
   <div class="form-group"><label class="label">Project Role</label><select class="select" name="project_role">@foreach(['PROJECT_MANAGER','LEAD','MEMBER','GUEST'] as $v)<option>{{ $v }}</option>@endforeach</select></div>
   <div class="form-group"><label class="label">Project Position</label><input class="input" name="position" placeholder="Backend Developer" required></div>
   <div class="form-group"><label class="label">Permission Mode</label><select class="select" name="permission_mode">@foreach(['STANDARD','READ_ONLY','COMMENT_ONLY','RESTRICTED'] as $v)<option>{{ $v }}</option>@endforeach</select></div>
  </div>
  <button class="btn btn-primary mt-3">Save Membership</button>
 </form>
</section>
@endcan
@endsection
